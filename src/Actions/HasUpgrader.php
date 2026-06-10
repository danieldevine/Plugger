<?php

namespace Coderjerk\Plugger\Actions;

use Coderjerk\Plugger\Utils\Url;
use Plugin_Upgrader;
use Plugin_Upgrader_Skin;

/**
 * Makes the WordPress Plugin Upgrader available, more reliably.
 */
trait HasUpgrader
{
    public static function upgrader(): Plugin_Upgrader
    {
        // Lord Lucan/Shergar/The Scarlet Pimpernel
        if (!class_exists('Plugin_Upgrader', false)) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }

        $args = ['url' => Url::pluggerUrl()];
        $skin = new Plugin_Upgrader_Skin($args);

        return new Plugin_Upgrader($skin);
    }
}
