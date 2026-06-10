<?php

namespace Coderjerk\Plugger\Actions;

use Coderjerk\Plugger\Utils\Url;
use WP_Upgrader_Skin;

class Install
{
    use HasUpgrader;

    public static function installSinglePlugin($plugin): void
    {
        $upgrader = self::upgrader();
        $upgrader->install($plugin->url, [true]);
    }
}
