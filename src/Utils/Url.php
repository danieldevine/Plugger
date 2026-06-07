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
}
