<?php

namespace Coderjerk\Plugger\Enums;

enum PluginSource
{
    case WP_REPOSITORY;
    case EXTERNAL;
    case LOCAL;
}
