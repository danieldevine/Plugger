<?php

namespace Coderjerk\Plugger\Enums;

enum NoticeType: string
{
    case NOTICE_ERROR = 'notice-error';
    case NOTICE_WARNING = 'notice-warning';
    case NOTICE_INFO = 'notice-info';
    case NOTICE_SUCCESS = 'notice-success';
}
