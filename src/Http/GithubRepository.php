<?php

namespace Coderjerk\Plugger\Http;

class GithubRepository implements Repository
{
    protected static string $base_url = 'https://api.github.com/repos/';

    public static function buildQuery(string $slug): string
    {
        return self::$base_url . $slug;
    }

    public static function call(string $slug): ?array
    {
        return CachedRequest::make(self::buildQuery($slug));
    }
}
