<?php

namespace Coderjerk\Plugger\Utils;

use Coderjerk\Plugger\Plugin;

class Plugins
{
    public static function initialisePlugins($plugins): array
    {
        $initialised_plugins = [];

        foreach ($plugins as $plugin) {
            $initialised_plugins[] = new Plugin($plugin);
        }

        return $initialised_plugins;
    }

    public static function getPluginNames($plugins): string
    {
        $names = [];

        foreach ($plugins as $plugin) {
            $names[] = $plugin->name;
        }

        return implode(', ', $names);
    }
}
