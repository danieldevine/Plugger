<?php

namespace Coderjerk\Plugger\Enums;

enum PluginSource: string
{
    case WP_REPOSITORY = 'WordPress';
    case EXTERNAL = "External";
    case LOCAL = "Bundled";
}
