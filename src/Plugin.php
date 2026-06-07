<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Enums\PluginSource;
use Coderjerk\Plugger\Utils\Url;
use Coderjerk\Plugger\Http\WordPressRepository;

class Plugin
{
    public PluginSource $source;
    public bool $is_active;
    public bool $is_installed;
    public bool $is_required;
    public bool $force_activation;
    public mixed $wp_repository_data;
    public string $link;
    public string $name;
    public string $slug;
    public string $status;
    public string $filePath;

    public function __construct(array $plugin)
    {
        $this->name = $plugin['name'];
        $this->slug = $plugin['slug'];
        $this->is_required = $plugin['required'] ?? false;
        $this->force_activation = $plugin['force_activation'] ?? false;
        $this->source = $this->getPluginSource($plugin);
        $this->filePath = $this->getFilePath($plugin['slug']);
        $this->is_installed = $this->isInstalled($plugin['slug']);
        $this->is_active = $this->isActive();
        //$this->wp_repository_data = $this->getWPRepositoryData($plugin['slug']); -- too slow
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

    /**
     * Plugin 'slugs' don't really exist anywhere except for the Wordpress.org repo
     * generally they can be derived from the folder name of the plugin
     * if the 'slug' is coming from get_plugins() it is the foldername/file-path (usually)
     * so we need to process it into a usable repo slug.
     *
     * @link    https://wordpress.stackexchange.com/questions/120004/how-can-i-find-plugins-slug#answer-290402
     *
     * @WP_Shit The likes of fucken Hello Dolly don't work like this though
     *
     */
    protected static function getWpRepoSlug(string $path): string
    {
        $path = preg_replace('/\.php$/', '', $path);

        // If there's a slash, then return the folder name as that's probably the slug
        if (str_contains($path, '/')) {
            return explode('/', $path)[0];
        }

        return $path;
    }

    protected function isInstalled($path): bool
    {
        if (is_dir(WP_PLUGIN_DIR . "/" . $path)) {
            return true;
        }

        return false;
    }

    /**
     * If this is a WP repo plugin and we're able
     * to match path to slug, then we can get some useful info
     */
    protected function getWPRepositoryData($path)
    {
        if ($this->source !== PluginSource::WP_REPOSITORY) {
            return null;
        }

        $slug = self::getWpRepoSlug($path);

        $data = WordPressRepository::call($slug);

        if (array_key_exists('error', $data)) {
            return null;
        }

        return $data;
    }
}
