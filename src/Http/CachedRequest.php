<?php

namespace Coderjerk\Plugger\Http;

class CachedRequest
{
    public static function make($slug)
    {
        // if a persistent object cache is in use then this will be snappy.
        $cache_key = 'plugger-repo-data-' . $slug;
        $data = wp_cache_get($cache_key);

        if ($data) {
            return $data;
        }

        $response = wp_remote_get($slug);

        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        wp_cache_set($cache_key, $data, 'default', 9000);

        return $data;
    }
}
