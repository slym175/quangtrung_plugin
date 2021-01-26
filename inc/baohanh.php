<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

define('GIFT_STATUS', array(
    '0' => 'Chưa đủ điều kiện',
    '1' => 'Chưa nhận',
    '2' => 'Đã nhận'
));

class Baohanh_List extends WP_List_Table
{

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Baohanh', 'sp'), //singular name of the listed records
            'plural' => __('Baohanh', 'sp'), //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ]);

    }


    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_data($per_page = 5, $page_number = 1)
    {
        $s = $_REQUEST['s'];
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}baohanh";

        if(isset($s) && $s){
            $sql .= ' where bh_code="'.$s.'"';
        }

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;


        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }


    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_customer($id)
    {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}customers",
            ['ID' => $id],
            ['%d']
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count()
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}baohanh";

        return $wpdb->get_var($sql);
    }


    /** Text displayed when no customer data is available */
    public function no_items()
    {
        _e('No customers avaliable.', 'sp');
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'code':
                return $item[$column_name];
            case 'order_id':
                return '#' . $item[$column_name];
            case 'customer_name':
                return $item[$column_name];
            case 'phone':
                return $item[$column_name];
            case 'recieved_gift':
                return GIFT_STATUS[$item[$column_name]];
            default:
                return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_name($item)
    {

        $delete_nonce = wp_create_nonce('sp_delete_customer');

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['ID']), $delete_nonce)
        ];

        return $title . $this->row_actions($actions);
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'code' => 'Mã bảo hành',
            'order_id' => 'Mã đơn hàng',
            'phone' => 'Số điện thoại',
            'customer_name' => 'Tên khách hàng',
            'recieved_gift' => 'Quà tặng',
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
        $sortable_columns = array(
            'phone' => array('phone', true),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        $actions = [

        ];

        return $actions;
    }


    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items()
    {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('customers_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_data($per_page, $current_page);
    }

    //Thêm action khi hover vào row
    function column_code($item)
    {
        $actions = array(
            'view' => sprintf('<a href="?page=%s&action=%s&code=%s">Xem chi tiết</a>', $_REQUEST['page'], 'view', $item['bh_code']),
            'history' => sprintf('<a href="?page=%s&action=%s&code=%s">Lịch sử bảo hành</a>', $_REQUEST['page'], 'history', $item['bh_code']),
        );

        return sprintf('%1$s %2$s', $item['bh_code'], $this->row_actions($actions));
    }

    public function process_bulk_action()
    {
        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'sp_delete_customer')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_customer(absint($_GET['customer']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url
                wp_redirect(esc_url_raw(add_query_arg()));
                exit;
            }

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_customer($id);

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            wp_redirect(esc_url_raw(add_query_arg()));
            exit;
        }
    }

}


class SP_Baohanh
{

    // class instance
    static $instance;

    // customer WP_List_Table object
    public $customers_obj;
    public $template_dir = "nt_templates";

    // class constructor
    public function __construct()
    {
        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_action('admin_menu', [$this, 'plugin_menu']);
    }


    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    public function plugin_menu()
    {

        $hook = add_menu_page(
            'Danh sách bảo hành',
            'Bảo hành',
            'manage_options',
            'baohanh',
            [$this, 'plugin_settings_page'],
            '', 25
        );

        add_action("load-$hook", [$this, 'screen_option']);

    }


    /**
     * Plugin settings page
     */
    public function plugin_settings_page()
    {
        $action = $_REQUEST['action'];
        if ($action == 'view') {
            echo tk_get_template('baohanh/view.php', true);
        } elseif ($action == 'history'){
            echo tk_get_template('baohanh/history.php', true);
        }elseif ($action == 'edit'){
            echo tk_get_template('baohanh/edit.php', true);
        }elseif ($action == 'create'){
            echo tk_get_template('baohanh/create.php', true);
        }else { ?>
            <div class="wrap">
                <h1 class="wp-heading-inline">Danh sách bảo hành</h1>
                <div id="poststuff">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="get">
                                <p class="search-box">
                                    <label class="screen-reader-text" for="search_id-search-input">Tìm kiếm:</label>
                                    <input type="hidden" id="page" name="page" value="baohanh">
                                    <input type="search" id="search_id-search-input" name="s" value="">
                                    <input type="submit" id="search-submit" class="button" value="Tìm kiếm">
                                </p>
                            </form>
                            <?php
                            $this->customers_obj->prepare_items();
                            $this->customers_obj->display(); ?>

                        </div>
                    </div>
                    <br class="clear">
                </div>
            </div>
        <?php }
    }

    /**
     * Screen options
     */
    public function screen_option()
    {

        $option = 'per_page';
        $args = [
            'label' => 'Bảo hành',
            'default' => 5,
            'option' => 'customers_per_page'
        ];

        add_screen_option($option, $args);

        $this->customers_obj = new Baohanh_List();
    }


    /** Singleton instance */
    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}


add_action('plugins_loaded', function () {
    SP_Baohanh::get_instance();
});
