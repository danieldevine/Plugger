<?php

namespace Coderjerk\Plugger\Views;

use Coderjerk\Plugger\Admin;
use Coderjerk\Plugger\Plugger;
use Coderjerk\Plugger\Enums\AdminViewContext;
use WP_List_Table;

class ListTable extends WP_List_Table
{

    public Plugger $plugger;

    public array $plugins;

    protected array $view_totals = [];

    protected string $plugger_url = "/wp-admin/plugins.php?page=plugger";

    public function __construct(Plugger $plugger)
    {
        $this->plugger = $plugger;
        $this->plugins = $this->plugger->plugins;

        foreach (AdminViewContext::cases() as $case) {
            $this->view_totals[$case->value] = 0;
        }

        $this->setViewTotals();

        parent::__construct([
            'singular' => __('Plugin', 'plugger'),
            'plural' => __('Plugins', 'plugger'),
            'ajax' => false
        ]);

    }

    public function setViewTotals(): void
    {
        $this->view_totals[AdminViewContext::ALL->value] = count($this->plugins);
        $this->view_totals[AdminViewContext::ACTIVATE->value] = count($this->toActivate());
        $this->view_totals[AdminViewContext::INSTALL->value] = count($this->toInstall());
    }

    public function get_columns(): array
    {
        return [
            'cb' => '<input type="checkbox" name="bulk-update[]" />',
            'name' => 'Name',
            'slug' => 'Slug',
            'source' => 'Source',
            'type' => 'Type',
        ];
    }

    public function get_table_classes(): array
    {
        return ['widefat', 'fixed'];
    }

    public function prepare_items(): void
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $data = $this->table_data();

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }

    public function get_hidden_columns(): array
    {
        return ['slug'];
    }

    public function get_sortable_columns(): array
    {
        return ['name' => ['name', true]];
    }

    protected function table_data(): array
    {
        $data = [];
        
        $plugins = match ($_REQUEST['view'] ?? '') {
            AdminViewContext::INSTALL->value => $this->toInstall(),
            AdminViewContext::ACTIVATE->value => $this->toActivate(),
            default => $this->plugins
        };

        foreach ($plugins as $plugin) {
            $data[] = [
                'cb' => "<input type='checkbox' name='bulk-update[]' value='{$plugin->slug}' />",
                'name' => $plugin->name,
                'slug' => $plugin->slug,
                'source' => $plugin->source->value,
                'type' => $plugin->type,
            ];
        }

        return $data;
    }

    public function column_default($item, $column_name): mixed
    {
        return $item[$column_name];
    }

    public function get_bulk_actions(): array
    {
        return ['bulk-update' => 'Update'];
    }

    public function no_items(): void
    {
        _e('Nothing to update or install.', 'plugger');
    }

    protected function toInstall(): array
    {
        return array_merge($this->plugger->mustInstall(), $this->plugger->shouldInstall());
    }

    protected function toActivate(): array
    {
        return array_merge($this->plugger->mustActivate(), $this->plugger->shouldActivate());
    }

    public function get_views(): array
    {
        $views = [];
        $current = (!empty($_REQUEST['view'])) ? $_REQUEST['view'] : AdminViewContext::ALL->value;

        foreach (AdminViewContext::cases() as $case) {
            $css_class = ($current == $case->value ? 'class="current"' : '');
            $url = $this->plugger_url . "&view=" . $case->value;
            $name = ucfirst($case->value);
            $count = $this->view_totals[$case->value];
            $views[$case->value] = "<a href='$url' {$css_class}>{$name} ({$count})</a>";
        }

        return $views;

    }
}
