<?php

namespace Coderjerk\Plugger\Enums;

enum RowActions: string
{
    case INSTALL = 'install';
    case ACTIVATE = 'activate';
    case UPDATE = 'update';
    case NONE = 'none';
}
