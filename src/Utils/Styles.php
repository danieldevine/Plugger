<?php

namespace Coderjerk\Plugger\Utils;

class Styles
{
    public static function init(): void
    {
        add_action('admin_enqueue_scripts', [self::class, 'pluggerStyles']);
    }

    public static function pluggerStyles(): void
    {
        wp_register_style('plugger_css', false);
        wp_enqueue_style('plugger_css');

        wp_add_inline_style('plugger_css', '
        .plugger__row td, .plugger__row th {
            border-bottom: 1px solid #c3c4c7;
        }
        .plugger__row--required {
            background-color: #fcf0f0;
        }
        .plugger__row--required th {
            border-left: 3px solid #cc1818;
        }
        .plugger__row--recommended {
            background-color: #fef8ee;
        }
        .plugger__row--recommended th{
            border-left: 3px solid #f0b849;
        }
        ');
    }
}
