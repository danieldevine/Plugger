<?php

namespace Coderjerk\Plugger\Utils;

class Path
{
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
    public static function getWpRepoSlug(string $path): string
    {
        $path = preg_replace('/\.php$/', '', $path);

        // If there's a slash, then return the folder name as that's probably the slug
        if (str_contains($path, '/')) {
            return explode('/', $path)[0];
        }

        return $path;
    }

    public static function getGithubRepoSlug(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode('/', trim($path, '/'));

        $owner = $parts[0] ?? null;
        $repo = $parts[1] ?? null;

        return "{$owner}/{$repo}";
    }
}
