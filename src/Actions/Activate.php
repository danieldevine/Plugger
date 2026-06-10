<?php

namespace Coderjerk\Plugger\Actions;

use Coderjerk\Plugger\Utils\Url;

class Activate
{
    use HasUpgrader;

    public static function activateSinglePlugin($plugin): void
    {
        activate_plugin($plugin->file_path);
        wp_redirect(Url::pluggerUrl());
    }
}
