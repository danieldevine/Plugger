<?php

use Coderjerk\Plugger\Plugger;

$composer_path = dirname(__DIR__, 4) . '/vendor/';

if (file_exists($composer_path)) {

    require_once $composer_path . 'autoload.php';

    $is_wp_cli = defined('WP_CLI') && WP_CLI;

    if (wp_get_environment_type() == 'development' && !$is_wp_cli) {

        /**
         * Dump variables and die.
         */
        if (!function_exists('dd')) {
            function dd()
            {
                call_user_func_array('dump', func_get_args());
                die();
            }
        }
    }

}

$plugins = [
    [
        'name' => 'Yoast SEO',
        'slug' => 'wordpress-seo',
        'required' => true,
        'force_activation' => true,
    ],
    [
        'name' => 'Yoast Duplicate Post',
        'slug' => 'duplicate-post',
        'required' => false,
    ],
    [
        'name' => 'Bulkboy',
        'slug' => 'bulkboy',
        'source' => 'https://github.com/danieldevine/bulkboy/archive/refs/tags/v1.0.0.zip',
        'required' => false,
        'force_activation' => false,
    ],
];
$plugger = new Plugger($plugins);
$plugger->init();
