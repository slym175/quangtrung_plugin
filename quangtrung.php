<?php
/**
 * Date: 7/3/2020
 * Time: 2:43 PM
 * Plugin Name: Quangtrung
 * Author: trugnduc.vnu@gmail.com.
 * Text Domain: quangtrung
 */

if (!defined('NS_PLUGIN_FILE')) {
    define('NS_PLUGIN_FILE', __FILE__);
}

define ('QUANGTRUNG_PLUGIN_URL', plugins_url () . '/' . basename (dirname (__FILE__)));
define ('QUANGTRUNG_TEXTDOMAIN', 'gt_textdomain');

if ( ! defined( 'YITH_WCWL_DIR' ) ) {
    define( 'QUANGTRUNG_DIR', plugin_dir_path( __FILE__ ) );
}

include_once dirname(NS_PLUGIN_FILE) . '/inc/ajax.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/admin.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/news-widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/page-widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/form_search_widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/category_widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/form-sale.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/xem_nhieu_widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/filter-product-widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/filter-product-price-widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/news-detail-widget.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/create_table.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/baohanh.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/hoidap.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/weight_shipping_calculate.php';

// SMS
include_once dirname(NS_PLUGIN_FILE) . '/inc/sms/index.php';

// Qua tang
include_once dirname(NS_PLUGIN_FILE) . '/inc/quatang/index.php';

// Online
include_once dirname(NS_PLUGIN_FILE) . '/inc/online/index.php';

//include_once dirname(NS_PLUGIN_FILE) . '/inc/add_box_meta/detail_product.php';
include_once dirname(NS_PLUGIN_FILE) . '/inc/add_box_meta/repeatable-fields-metabox.php';

if (!class_exists('Quangtrung')) {
    add_action('plugins_loaded', array('Quangtrung', 'init'));

    class Quangtrung{

        protected static $instance;
        public function __construct()
        {
            add_action( 'wp_enqueue_scripts', 'load_plugins_scripts' );
            add_action( 'admin_enqueue_scripts', 'quangtrung_enqueue' );
        }

        public static function init()
        {
            is_null(self::$instance) AND self::$instance = new self;
            return self::$instance;
        }
    }
}

function quangtrung_enqueue(){
    wp_enqueue_script('quangtrung-script', QUANGTRUNG_PLUGIN_URL . '/assets/js/admin.js', 1,true );
    wp_enqueue_script('inline-edit-script', QUANGTRUNG_PLUGIN_URL . '/assets/js/inline-edit.js', 1, true);
    wp_enqueue_style( 'inline-edit-style', QUANGTRUNG_PLUGIN_URL . '/assets/css/inline-edit.css', 1, false);
}

function load_plugins_scripts()
{
    if(is_page('he-thong-cua-hang')){
        wp_register_script('custom-script', THEME_URL_URI . '/assets/js/localstore.js', 1, true);
        wp_enqueue_script('custom-script');
        global $tinh_thanhpho;
        global $quan_huyen;
        global $xa_phuong_thitran;
        if(!is_array($tinh_thanhpho) || empty($tinh_thanhpho)){
            include 'cities/tinh_thanhpho.php';
        }
        if(!is_array($quan_huyen) || empty($quan_huyen)){
            include 'cities/quan_huyen.php';
        }
        if(!is_array($xa_phuong_thitran) || empty($xa_phuong_thitran)){
            include 'cities/xa_phuong_thitran.php';
        }

        $array = array(
            'city' => json_encode($tinh_thanhpho),
            'district'   =>  json_encode($quan_huyen),
            'ward'   =>  json_encode($xa_phuong_thitran),
            'ajaxurl'   =>  admin_url('admin-ajax.php'),
        );
        wp_localize_script('custom-script', 'localstore_array', $array);
    }
}

/**
 * load file
 */
function tk_get_template($path, $return = false)
{
    $plugin_path = QUANGTRUNG_DIR . 'templates/' . $path;
    if ($return) {
        ob_start();
    }

    // include file located.
    include($plugin_path);

    if ($return) {
        return ob_get_clean();
    }
}

function get_type_time_bh($type){
    switch ($type){
        case 'month':
            return 'Tháng';
            break;
        case 'year':
            return 'Năm';
            break;
    }
    return '';
}

function custom_contactform7_before_send_mail( $form_to_DB ) {
    //set your db details
    global $wpdb;
    $table = $wpdb->prefix.'hoidap';

    if($form_to_DB && $form_to_DB->id == 4704) {     
        $currentformInstance = WPCF7_ContactForm::get_current();
        $submission = WPCF7_Submission::get_instance();
        $data =& $submission->get_posted_data();
        $link =& $submission->get_meta('url');
        
        $insert_data = array(
            'name'          => $data['your-name'],
            'phone'         => $data['your-phone'],
            'email'         => $data['your-email'],
            'question_type' => $data['your-question-type'][0],
            'contents'      => $data['your-message'],
            'link'          => $link,
        );
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s');
        $wpdb->insert($table, $insert_data, $format);     

        $mail_cc = "";
        if($data['your-question-type'][0] == "Câu hỏi kỹ thuật") {
            $mail_cc = get_qt_options('tech_staff_email');
        }
        if($data['your-question-type'][0] == "Câu hỏi thông thường"){
            $mail_cc = get_qt_options('cskh_staff_email');
        }

        $mail = $currentformInstance->prop('mail');
        if($mail_cc != "") {
            $mail['additional_headers'] = "Cc: $mail_cc";
        }
        
        $currentformInstance->set_properties(array(
            "mail" => $mail
        ));

        return $currentformInstance;
    }    
    return $form_to_DB;
}
add_action( 'wpcf7_before_send_mail', 'custom_contactform7_before_send_mail' );

add_filter("wpdiscuz_custom_field_text", function ($value, $args) {
    // $value .= print_r($args);
    if($args['name'] == "Số điện thoại" && !is_admin( )) {
        $value = substr_replace(trim($value, " "), '***', -3);
    }
    return $value;
}, 10, 2);