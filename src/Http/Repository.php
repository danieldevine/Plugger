<?php

namespace Coderjerk\Plugger\Http;

/**
 * Establishing an interface as in the future
 * we may want to add more repo options
 */
interface Repository
{
    static function buildQuery(string $slug): string;

    static function call(string $slug): ?array;
}
