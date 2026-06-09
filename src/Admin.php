<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Utils\Html;
use Coderjerk\Plugger\Views\ListTable;

class Admin
{
    public static Plugger $plugger;

    public static function init(Plugger $plugger): void
    {
        self::$plugger = $plugger;
        add_action('admin_menu', [self::class, 'addAdminMenu']);
    }

    public static function addAdminMenu(): void
    {
        add_plugins_page(
            'Plugger',
            'Theme Plugins',
            'install_plugins',
            'plugger',
            [self::class, 'adminPage'],
            1
        );
    }

    public static function adminPage(): void
    {
        $title = Html::wrap('Theme Plugins', 'h1');
        $title .= Html::wrap('These plugins are necessary or highly recommended for your theme to work as intended.', 'p');
        print HTMl::wrap($title, 'div', ['class' => 'wrap']); // we need the 'wrap' class to position the title above admin notices.
        $table = new ListTable(self::$plugger);
        $table->prepare_items();
        echo "<div class='tablenav top'>\n";
        $table->views();
        echo "</div>\n";
        echo "<div class='wrap'>";
        $table->display();
        echo "</div>";
    }
}
