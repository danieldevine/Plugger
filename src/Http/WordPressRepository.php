<?php

namespace Coderjerk\Plugger\Http;

/**
 * Avoid issues with WP's plugin_api() function by just doing it ourselves.
 */
class WordPressRepository
{
    protected static string $base_url = "https://api.wordpress.org/plugins/info/1.2/";

    protected static function buildQuery($plugin): string
    {
        // @WP_Shit the API 'docs' are in the form of a how-to blog post.
        // @link https://wplake.org/blog/wordpress-org-api/
        return add_query_arg([
            'action' => 'plugin_information',
            'slug' => $plugin
        ], self::$base_url);
    }

    public static function call($plugin)
    {
        $response = wp_remote_get(self::buildQuery($plugin));
        if (is_wp_error($response)) {
            return null;
        }
        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }
}
