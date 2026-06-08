<?php

namespace Coderjerk\Plugger\Utils;

class Url
{
    public static function isWordPressRepoUrl($string): bool
    {
        if (preg_match('|^http[s]?://wordpress\.org/(?:extend/)?plugins/|', $string)) {
            return true;
        }
        return false;
    }

    public static function isUrl($string): bool
    {
        if (preg_match('|^http[s]?://|', $string)) {
            return true;
        }
        return false;
    }
    
    public static function nonceUrl($item, $base_url): string
    {
        $query = [
            'plugin' => urlencode($item['slug']),
            'plugger-action' => $item['action'] . '-plugin'
        ];

        return wp_nonce_url(
            add_query_arg($query, $base_url),
            'plugger-' . $item['action'],
            'plugger-nonce'
        );
    }
}
