<?php

namespace Coderjerk\Plugger\Http;

interface Repository
{
    static function buildQuery(string $slug): string;

    static function call(string $slug): ?array;
}
