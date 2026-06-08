<?php

namespace Coderjerk\Plugger\Enums;

enum AdminViewContext: string
{
    case ALL = 'all';
    case INSTALL = 'install';
    case UPDATE = 'update';
    case ACTIVATE = 'activate';
}
