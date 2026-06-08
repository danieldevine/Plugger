<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Enums\NoticeType;
use Coderjerk\Plugger\Utils\Plugins;

class Plugger
{
    /**
     * @var array The plugins required or reccommended from our theme.
     */
    public array $plugins;

    /**
     * @var array|null We will provide limited configuration at a later date
     */
    public ?array $config;

    public function __construct(array $plugins, array $config = [])
    {
        $this->config = $config;
        $this->plugins = $plugins;
        $this->plugins = Plugins::initialisePlugins($this->plugins);

        Admin::init($this);
    }

    public function init(): void
    {
        // no point harassing the innocent
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $must_install = $this->mustInstall();
        $must_activate = $this->mustActivate();
        $should_install = $this->shouldInstall();
        $should_activate = $this->shouldActivate();

        if (!empty($must_install)) {
            $names = Plugins::getPluginNames($must_install);
            new Notifier('Your theme requires that these plugins be installed: ' . $names, NoticeType::NOTICE_ERROR);
        }

        if (!empty($must_activate)) {
            $names = Plugins::getPluginNames($must_activate);
            new Notifier('Your theme requires that these installed plugins be activated: ' . $names, NoticeType::NOTICE_ERROR);
        }

        if (!empty($should_install)) {
            $names = Plugins::getPluginNames($should_install);
            new Notifier('Your theme recommends that these plugins be installed: ' . $names, NoticeType::NOTICE_WARNING);
        }

        if (!empty($should_activate)) {
            $names = Plugins::getPluginNames($should_activate);
            new Notifier('Your theme recommends that these installed plugins be activated: ' . $names, NoticeType::NOTICE_WARNING);
        }
    }

    public function mustActivate(): array
    {
        return array_filter($this->plugins, fn($plugin) => $plugin->is_installed && !$plugin->is_active && $plugin->is_required);
    }

    public function mustInstall(): array
    {
        return array_filter($this->plugins, fn($plugin) => !$plugin->is_installed && $plugin->is_required);
    }

    public function shouldActivate(): array
    {
        return array_filter($this->plugins, fn($plugin) => $plugin->is_installed && !$plugin->is_active && !$plugin->is_required);
    }

    public function shouldInstall(): array
    {
        return array_filter($this->plugins, fn($plugin) => !$plugin->is_installed && !$plugin->is_required);
    }

}
