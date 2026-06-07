<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Utils\Html;
use Coderjerk\Plugger\Enums\NoticeType;

class Notifier
{
    public string $message;

    public NoticeType $notice_type;

    public function __construct(string $message, NoticeType $notice_type)
    {
        $this->message = $message;
        $this->notice_type = $notice_type;

        add_action('admin_notices', [$this, 'render'], 10, 2);
    }

    public function render(): void
    {
        print Html::wrap($this->message, 'div', ['class' => 'notice ' . $this->notice_type->value]);
    }
}
