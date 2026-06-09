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
    public bool $is_active;
    public bool $is_installed;
    public bool $is_required;
    public bool $force_activation;
    public mixed $repository_data;
    public string $description = '';
    public string $link;
    public string $name;
    public string $slug;
    public string $status;
    public string $filePath;
    public string $type;
    public string $url = '';
    public RowActions $action;

    public function __construct(array $plugin)
    {
        $this->name = $plugin['name'];
        $this->slug = $plugin['slug'];
        $this->is_required = $plugin['required'] ?? false;
        $this->type = $plugin['required'] ? 'Required' : 'Recommended';
        $this->force_activation = $plugin['force_activation'] ?? false;
        $this->source = $this->getPluginSource($plugin);
        $this->filePath = $this->getFilePath($plugin['slug']);
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
        if (is_plugin_active($this->filePath)) {
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
        $repository_data = match ($this->source) {
            PluginSource::WP_REPOSITORY => $this->getWPRepositoryData(),
            PluginSource::EXTERNAL => $this->getExternalRepositoryData(),
            default => null,
        };

        $this->repository_data = $repository_data;

        return $repository_data;
    }

    protected function getWPRepositoryData(): ?array
    {
        $path = Path::getWpRepoSlug($this->slug);
        $data = WordPressRepository::call($path);

        if (array_key_exists('error', $data)) {
            return null;
        }

        if ($data['short_description']) {
            $this->description = $data['short_description'];
        }

        return $data;
    }

    protected function getExternalRepositoryData(): ?array
    {
        if (!$this->url && Url::isGitHubRepoUrl($this->url)) {
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

}
