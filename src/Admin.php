<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Utils\Html;

class Admin
{
    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'addAdminMenu']);
    }

    public static function addAdminMenu(): void
    {
        add_plugins_page(
            'Plugger',
            'Theme Required',
            'install_plugins',
            'plugger',
            [self::class, 'adminPage'],
            1
        );
    }

    public static function adminPage(): void
    {
        print Html::wrap('Plugger', 'h1');
    }
}
