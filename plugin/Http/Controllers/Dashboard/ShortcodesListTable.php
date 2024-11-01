<?php

namespace WPXShortcodesManagerLight\Http\Controllers\Dashboard;

if (! class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

use WPXShortcodesManagerLight\WPBones\Html\Html;
use WPXShortcodesManagerLight\Models\Shortcode;
use WPXShortcodesManagerLight\PureCSSSwitch\Html\HtmlTagSwitchButton;

final class ShortcodesListTable extends \WP_List_Table
{
    public function __construct()
    {
        $current = get_current_screen();

        parent::__construct(
            [
            'singular' => 'listtable_shortcode_id',
            'plural'   => 'shortcodes',
            'ajax'     => false,
            'screen'   => 'toplevel_page_wpxsm_menu',
      ]
        );
    }

    protected function get_table_classes()
    {
        return [ 'widefat', 'striped', $this->_args[ 'plural' ] ];
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function get_columns()
    {
        $columns = [
          'cb'                              => '<input type="checkbox" />',
          Shortcode::COLUMN_ID              => __('Shortcode', 'wpx-shortcodes-manager-light'),
          Shortcode::COLUMN_CALLABLE        => __('Callable', 'wpx-shortcodes-manager-light'),
          Shortcode::COLUMN_CALLBACK        => __('Callback', 'wpx-shortcodes-manager-light'),
          Shortcode::COLUMN_CUSTOM          => __('Custom', 'wpx-shortcodes-manager-light'),
          Shortcode::COLUMN_ENABLED         => __('Enabled', 'wpx-shortcodes-manager-light'),
          Shortcode::COLUMN_SHOW_IN_CONTENT => __('Display', 'wpx-shortcodes-manager-light'),
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return [];
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="cron[]" value="%s" />',
            $item[ Shortcode::COLUMN_ID ]
        );
    }

    /**
     * Display a cel content for a column.
     *
     * @param array  $item        The single item
     * @param string $column_name Column name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            default:
                return print_r($item, true);
        }
    }

    public function column_shortcode_id($item)
    {
        return $item[ Shortcode::COLUMN_ID ];
    }

    public function column_callable($item)
    {
        $callable = $item[ Shortcode::COLUMN_CALLABLE ] ? 'Yes' : 'No';

        return $callable;
    }

    public function column_callback($item)
    {
        if (( bool) $item[ Shortcode::COLUMN_CUSTOM ]) {
            $method = __('__internal', 'wpx-shortcodes-manager-light');
        } else {
            $parts = explode("::", $item[ Shortcode::COLUMN_CALLBACK ]);

            $method = isset($parts[ 1 ]) ? $parts[ 1 ] : $parts[ 0 ];
        }

        return '<code>' . $method . '</code>';

    }

    public function column_custom($item)
    {
        $custom = (bool) $item[ Shortcode::COLUMN_CUSTOM ];

        if ($custom) {
            return sprintf(
                '<a class="button button-small button-secondary" href="%s">%s</a> <button name="destroy" value="%s" class="button button-small button-secondary" data-confirm="%s">%s</button>',
                add_query_arg(
                    [
                    'shortcode_id' => $item[ Shortcode::COLUMN_ID ],
                    'view'         => 'edit',
                        ]
                ),
                __('Edit'),
                $item[ Shortcode::COLUMN_ID ],
                sprintf(__("Warning! Are you sure to delete '%s' custom shortcode?", 'wpx-shortcodes-manager-light'), $item[ ShortCode::COLUMN_NAME ]),
                __('Delete', 'wpx-shortcodes-manager-light')
            );
        } else {
            //return sprintf( '<a href="%s">%s</a>', '#', __( 'Edit' ) );
        }
    }

    public function column_enabled($item)
    {
        $enabled = (bool) $item[ Shortcode::COLUMN_ENABLED ];
        $class   = $enabled ? 'wpxsm-button-disable' : 'wpxsm-button-enable';

        $button = HtmlTagSwitchButton::name($item[ Shortcode::COLUMN_ID ] . '-endable')
                                     ->checked($enabled)
                                     ->class($class)
                                     ->data(Shortcode::COLUMN_ID, $item[ Shortcode::COLUMN_ID ]);

        return $button->html();
    }

    public function column_show_in_content($item)
    {
        $enabled = (bool) $item[ Shortcode::COLUMN_SHOW_IN_CONTENT ];
        $class   = $enabled ? 'wpxsm-button-hide-in-content' : 'wpxsm-button-show-in-content';

        $button = HtmlTagSwitchButton::name($item[ Shortcode::COLUMN_ID ] . '-hide')
                                     ->checked($enabled)
                                     ->class($class)
                                     ->data(Shortcode::COLUMN_ID, $item[ Shortcode::COLUMN_ID ]);

        return $button->html();
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [
          'disable' => __('Disable'),
        ];

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();

        // Process bulk action
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('wpxsm_shortcodes_per_page', 5);

        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        // get items
        $items = $this->getItems();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $total_items = count($items);

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $slice_items = array_slice($items, (($current_page - 1) * $per_page), $per_page);

        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */

        $this->items = $slice_items;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args(
            [
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
      ]
        );

    }

    public function process_bulk_action()
    {

        //    //Detect when a bulk action is being triggered...
        //    if ( 'delete' === $this->current_action() ) {
        //
        //      // In our file that handles the request, verify the nonce.
        //      $nonce = esc_attr( $_REQUEST['_wpnonce'] );
        //
        //      if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
        //        die( 'Go get a life script kiddies' );
        //      }
        //      else {
        //        Shortcode::delete_customer( absint( $_GET['customer'] ) );
        //
        //        wp_redirect( esc_url( add_query_arg() ) );
        //        exit;
        //      }
        //
        //    }
        //
        //    // If the delete bulk action is triggered
        //    if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
        //         || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        //    ) {
        //
        //      $delete_ids = esc_sql( $_POST['bulk-delete'] );
        //
        //      // loop over the array of record IDs and delete them
        //      foreach ( $delete_ids as $id ) {
        //        Shortcode::delete_customer( $id );
        //
        //      }
        //
        //      wp_redirect( esc_url( add_query_arg() ) );
        //      exit;
        //    }
    }

    /**
     * The itens can be not found for two main reason: the query search has param tha t doesn't match with items, or the
     * items list (or the database query) return an empty list.
     *
     */
    public function no_items()
    {
        // Default message
        printf(__('No %s found.', 'wpx-cron-manager-light'), 'Crons');

        // If in search mode
        // @todo Find a way to determine if we are in 'search' mode or not
        echo '<br/>';

        _e('Please, check again your search parameters.', 'wpx-cron-manager-light');
    }

    protected function getItems($args = [])
    {
        return Shortcode::all();
    }

    public static function getSingleRow($id)
    {
        $instance = new self();

        $item = Shortcode::find($id);

        if ($item) {
            ob_start();
            $instance->single_row($item);
            $content = ob_get_contents();
            ob_end_clean();

            return $content;
        }

    }

}
