<?php

namespace Coderjerk\Plugger\Http;

/**
 * Avoid issues with WP's plugin_api() function by just doing it ourselves.
 */
class WordPressRepository implements Repository
{
    protected static string $base_url = "https://api.wordpress.org/plugins/info/1.2/";

    public static function buildQuery(string $slug): string
    {
        // @WP_Shit the API 'docs' are in the form of a how-to blog post.
        // @link https://wplake.org/blog/wordpress-org-api/
        return add_query_arg([
            'action' => 'plugin_information',
            'slug' => $slug,
            'fields' => [
                'short_description' => true,
                'sections' => false,
                'contributors' => false,
                'screenshots' => false,
            ]
        ], self::$base_url);
    }

    public static function call(string $slug): ?array
    {
        return CachedRequest::make(self::buildQuery($slug));
    }
}
