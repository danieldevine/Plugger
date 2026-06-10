<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Enums\PluginSource;
use Coderjerk\Plugger\Enums\RowActions;
use Coderjerk\Plugger\Utils\Path;
use Coderjerk\Plugger\Utils\Url;
use Coderjerk\Plugger\Http\WordPressRepository;
use Coderjerk\Plugger\Http\GitHubRepository;

class Plugin
{
    public PluginSource $source;
    public RowActions $action;
    public bool $force_activation;
    public bool $is_active;
    public bool $is_installed;
    public bool $is_required;
    public mixed $repository_data;
    public string $description = '';
    public string $file_path;
    public string $link;
    public string $name;
    public string $slug;
    public string $status;
    public string $type;
    public string $url = '';

    public function __construct(array $plugin)
    {
        $this->name = $plugin['name'];
        $this->slug = $plugin['slug'];
        $this->is_required = $plugin['required'] ?? false;
        $this->type = $plugin['required'] ? 'Required' : 'Recommended';
        $this->force_activation = $plugin['force_activation'] ?? false;
        $this->source = $this->getPluginSource($plugin);
        $this->file_path = $this->getFilePath($plugin['slug']);
        $this->is_installed = $this->isInstalled($plugin['slug']);
        $this->is_active = $this->isActive();
        $this->action = $this->setAction();
        $this->repository_data = null;
    }

    protected function setAction(): RowActions
    {
        if ($this->is_installed && !$this->is_active) {
            return RowActions::ACTIVATE;
        }

        if (!$this->is_installed) {
            return RowActions::INSTALL;
        }

        return RowActions::NONE;
    }

    protected function getFilePath($slug): string
    {
        $keys = array_keys($this->getInstalledPlugins());

        foreach ($keys as $key) {
            if (preg_match('|^' . $slug . '/|', $key)) {
                return $key;
            }
        }

        return $slug;
    }

    protected function getPluginSource($plugin): PluginSource
    {
        if (empty($plugin['source'])) {
            return PluginSource::WP_REPOSITORY;
        }

        if (UrL::isUrl($plugin['source'])) {
            $this->url = $plugin['source'];

            if (Url::isWordPressRepoUrl($plugin['source'])) {
                return PluginSource::WP_REPOSITORY;
            }
            return PluginSource::EXTERNAL;
        }

        return PluginSource::LOCAL;
    }

    protected function getInstalledPlugins(): array
    {
        return get_plugins();
    }

    protected function isActive(): bool
    {
        if (is_plugin_active($this->file_path)) {
            return true;
        }
        return false;
    }

    protected function isInstalled($path): bool
    {
        if (is_dir(WP_PLUGIN_DIR . "/" . $path)) {
            return true;
        }

        return false;
    }

    public function getRepositoryData(): ?array
    {
        if ($this->is_installed) {
            return $this->getInstalledPluginData();
        }

        $repository_data = match ($this->source) {
            PluginSource::WP_REPOSITORY => $this->getWPRepositoryData(),
            PluginSource::EXTERNAL => $this->getExternalRepositoryData(),
            default => null,
        };

        $this->repository_data = $repository_data;

        return $repository_data;
    }

    /**
     * The plugin isn't installed and is from the WordPress repo,
     * so we'll try to get data from there.
     */
    protected function getWPRepositoryData(): ?array
    {
        $path = Path::getWpRepoSlug($this->slug);
        $data = WordPressRepository::call($path);

        if (array_key_exists('error', $data)) {
            return null;
        }

        if (isset($data['short_description'])) {
            $this->description = $data['short_description'];
        }

        if (isset($data['download_link'])) {
            $this->url = $data['download_link'];
        }

        return $data;
    }

    /**
     * The plugin is not installed and comes from an external repo
     * Chances are its GitHub so we'll try there for data.
     */
    protected function getExternalRepositoryData(): ?array
    {
        if (!$this->url) {
            return null;
        }

        if (!Url::isGitHubRepoUrl($this->url)) {
            return null;
        }

        $path = Path::getGitHubRepoSlug($this->url);
        $data = GitHubRepository::call($path);

        if (array_key_exists('error', $data)) {
            return null;
        }

        if ($data['description']) {
            $this->description = $data['description'];
        }

        return $data;
    }

    /**
     * The plugin is installed (maybe not active though)
     * so we'll get data from WordPress.
     */
    protected function getInstalledPluginData(): ?array
    {
        $installed_plugins = $this->getInstalledPlugins();

        if (empty($installed_plugins)) {
            return null;
        }

        if (!array_key_exists($this->file_path, $installed_plugins)) {
            return null;
        }

        $data = $installed_plugins[$this->file_path];

        if ($data['Description']) {
            $this->description = $data['Description'];
        }

        $this->repository_data = $data;
        return $installed_plugins[$this->file_path];
    }

}
