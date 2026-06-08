<?php

namespace Coderjerk\Plugger\Views;

use Coderjerk\Plugger\Enums\AdminViewContext;
use Coderjerk\Plugger\Plugger;
use Coderjerk\Plugger\Utils\Url;
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
            'cb' => '<input type="checkbox" />',
            'name' => 'Name',
            'slug' => 'Slug',
            'source' => 'Source',
            'type' => 'Type',
        ];
    }

    public function get_table_classes(): array
    {
        return ['widefat', 'plugger'];
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
        return ['slug', 'action'];
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
                'cb' => "<input type='checkbox'/>",
                'name' => $plugin->name,
                'slug' => $plugin->slug,
                'source' => $plugin->source->value,
                'type' => $plugin->type,
                'action' => $plugin->action->value,
            ];
        }

        return $data;
    }

    public function column_default($item, $column_name): mixed
    {
        return $item[$column_name];
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" id="%3$s" />',
            esc_attr($this->_args['singular']),
            esc_attr($item['slug']),
            esc_attr($item['slug'])
        );
    }

    public function single_row($item): void
    {
        $css_class = match ($item['type']) {
            'Recommended' => 'recommended',
            'Required' => 'required',
            default => 'normal',
        };

        echo "<tr class='plugger__row plugger__row--{$css_class}'>";
        $this->single_row_columns($item);
        echo '</tr>';
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

    /**
     * 'views' are the row of links that appear above the table, in this context.
     * Printed in the Coderjerk\Plugger\Admin class as $table->views()
     */
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

    protected function column_name($item): string
    {
        $nonce = Url::nonceUrl($item, $this->plugger_url);
        $actions = [
            $item['action'] => sprintf("<a href='%1s'>%2s</a>", $nonce, ucFirst($item['action']))
        ];

        // @WP_Shit: hilarious thread https://core.trac.wordpress.org/ticket/48751
        // you have to explicitly require the row actions to be visible.
        // Because it would be bad for accessibility somehow if people could see stuff.
        // cf @Designers_Shit
        return $item['name'] . ' ' . $this->row_actions($actions, $always_visible = true);
    }
}
