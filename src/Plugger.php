<?php

namespace Coderjerk\Plugger;

use Coderjerk\Plugger\Enums\NoticeType;

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
        $this->plugins = self::processPlugins($plugins);
    }

    protected static function getNames($plugins): string
    {
        $names = [];

        foreach ($plugins as $plugin) {
            $names[] = $plugin->name;
        }

        return implode(', ', $names);
    }

    public function init(): void
    {
        $must_install = $this->mustInstall();
        $must_activate = $this->mustActivate();
        $should_install = $this->shouldInstall();
        $should_activate = $this->shouldActivate();

        if (!empty($must_install)) {
            $names = self::getNames($must_install);
            new Notifier('Your theme requires that these plugins be installed: ' . $names, NoticeType::NOTICE_ERROR);
        }

        if (!empty($must_activate)) {
            $names = self::getNames($must_activate);
            new Notifier('Your theme requires that these installed plugins be activated: ' . $names, NoticeType::NOTICE_ERROR);
        }

        if (!empty($should_install)) {
            $names = self::getNames($should_install);
            new Notifier('Your theme recommends that these plugins be installed: ' . $names, NoticeType::NOTICE_WARNING);
        }

        if (!empty($should_activate)) {
            $names = self::getNames($should_activate);
            new Notifier('Your theme recommends that these installed plugins be activated: ' . $names, NoticeType::NOTICE_WARNING);
        }

    }

    /**
     * Initialise the Plugin objects.
     *
     * @param $plugins
     *
     * @return array
     */
    public static function processPlugins($plugins): array
    {
        $processed_plugins = [];

        foreach ($plugins as $plugin) {
            $processed_plugins[] = new Plugin($plugin);
        }

        return $processed_plugins;
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
