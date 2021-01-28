<?php

/*
Plugin Name: WP_List_Table Class Example
Plugin URI: https://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
Description: Demo WP_List_Table Class works
Version: 1.0
Author: trungduc.vnu@gmail.com
Author URI:  https://w3guy.com
*/

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

define('R_GIFT_STATUS', array(
    '0' => 'Đang thực hiện',
    '1' => 'Có thể nhận quà',
    '2' => 'Đã nhận quà'
));

class Gift_Registration_List extends WP_List_Table
{

    /** Class constructor */
    public function __construct()
    {

        parent::__construct([
            'singular' => __('Dangkynhanqua', 'sp'), //singular name of the listed records
            'plural' => __('Dangkynhanqua', 'sp'), //plural name of the listed records
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
    public static function get_customers($per_page = 10, $page_number = 1)
    {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}dangkynhanqua";

        $input_gift_status = $_REQUEST['input_gift_status'];
        if(isset($input_gift_status) && $input_gift_status){
            $sql .= ' where status = '. intval($input_gift_status);
        }

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        } else {
            $sql .= ' ORDER BY created_at DESC';
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
            "{$wpdb->prefix}dangkynhanqua",
            ['id' => $id],
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

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}dangkynhanqua";

        return $wpdb->get_var($sql);
    }


    /** Text displayed when no customer data is available */
    public function no_items()
    {
        _e('Không có nội dung hiển thị.', 'sp');
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
            case 'user_id':
                $userdata = get_userdata( $item[$column_name] );
                return $userdata->display_name;
            case 'product_id':
                return get_the_title( $item[$column_name] );
            case 'status':
                return R_GIFT_STATUS[$item[$column_name]];
            case 'created_at':
                return date('d-m-Y H:i:s', $item[$column_name]);
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
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_user_id($item)
    {

        $userdata = get_userdata( $item['user_id'] );
        $title = '<strong>' . $userdata->display_name . '</strong>';

        // $actions = [
        //     'quick-edit' => sprintf( '<a class="quick-edit" href="javascript:void(0)">Sửa nhanh</a>' ),
        //     // 'mark' => sprintf('<a href="?page=%s&action=%s">Đã đọc</a>', $_REQUEST['page'], 'mark_viewed'),
        // ];

        return $title;
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_status($item)
    {

        $title = '';
        $title .= '<div class="quick-edit-section"><select class="gitf_status">';
        
        foreach(R_GIFT_STATUS as $key => $sta) {
            // $title .= '<option value="'. intval($key) .'" '. $key == $item['status'] ? 'selected' : "" .'>'. $sta .'</option>';
            $title .= '<option value="';
            $title .= intval($key);
            $title .= '"';
            $title .= $key == $item['status'] ? 'selected' : "";
            $title .= '>';
            $title .= $sta;
            $title .= '</option>';
        }
            
        $title .= '</select><a href="javascript:void(0)" onclick="changeStatus(this)" data-url="'. admin_url( 'admin-ajax.php' ) .'" data-id="'. absint( $item['id'] ) .'" class="button">Sửa</a></div>';

        return $title;
    }


    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns()
    {
        $columns = [
            'cb'            => '<input type="checkbox" />',
            'user_id'       => 'Khách hàng',
            'product_id'    => 'Sản phẩm',
            'status'        => 'Trạng thái',
            'created_at'    => 'Ngày tạo',
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
            'user_id'       => array('user_id', true),
            'product_id'    => array('product_id', true),
            'status'        => array('status', true),
            'created_at'    => array('created_at', true),
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
//            'bulk-delete' => 'Delete',
//            'bulk-view' => 'View'
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

        $per_page = $this->get_items_per_page('customers_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_customers($per_page, $current_page);
    }

    public function process_bulk_action()
    {

        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, 'sp_delete_question')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_customer(absint($_GET['question']));

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

    function extra_tablenav( $which ) {
		$input_gift_status = @$_POST['input_gift_status'] ? esc_attr($_POST['input_gift_status']) : -1;
		if ( $which == "top" ) : ?>
		<div class="actions">
			<p class="search-box">
				<label for="post-search-input" class="screen-reader-text">Search Pages:</label>
				<select name="input_gift_status" id="input_gift_status">
                    <option value="-1">Chọn trạng thái</option>
                    <?php foreach(R_GIFT_STATUS as $key => $value) : ?>
                        <option value="<?= $key ?>" <?= $key == $input_gift_status ? "selected" : "" ?> ><?= $value ?></option>
                    <?php endforeach ?>
                </select>
				<input type="submit" value="Lọc" class="button" id="search-submit" name="">
			</p>
		</div>
        <?php endif;
	}

}


class Gift_Registration_Plugin
{

    // class instance
    static $instance;

    // customer WP_List_Table object
    public $customers_obj;

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
            'Danh sách nhận quà',
            'Đăng ký nhận quà',
            'manage_options',
            'dang_ky_nhan_qua',
            [$this, 'plugin_settings_page'], 'dashicons-buddicons-community', 21
        );

        add_action("load-$hook", [$this, 'screen_option']);

    }


    /**
     * Plugin settings page
     */
    public function plugin_settings_page()
    {
        ?>
        <div class="wrap">
            <h2>Danh sách đăng ký nhận quà</h2>

            <div id="poststuff">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php
                            $this->customers_obj->prepare_items();
                            $this->customers_obj->display(); ?>
                        </form>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

    /**
     * Screen options
     */
    public function screen_option()
    {

        $option = 'per_page';
        $args = [
            'label' => 'Phân trang: ',
            'default' => 5,
            'option' => 'customers_per_page'
        ];

        add_screen_option($option, $args);

        $this->customers_obj = new Gift_Registration_List();
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


add_action('init', function () {
    Gift_Registration_Plugin::get_instance();
});
