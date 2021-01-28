<?php

add_action( 'wp_ajax_update_guarantee_gift', 'update_guarantee_gift' );
add_action( 'wp_ajax_nopriv_update_guarantee_gift', 'update_guarantee_gift' );
function update_guarantee_gift() {
 
    global $wpdb;
    $table = $wpdb->prefix . 'baohanh';
    //do bên js để dạng json nên giá trị trả về dùng phải encode
    $gift = (isset($_REQUEST['gift'])) ? esc_attr($_REQUEST['gift']) : '';
    $bh_code = (isset($_REQUEST['code'])) ? esc_attr($_REQUEST['code']) : '';

    $wpdb->update( 
        $table, 
        array( 
            'gift' => intval($gift),
        ), 
        array( 'bh_code' => $bh_code ), 
        array( 
            '%d', 
        ), 
        array( '%s' ) 
    );
    
    if($wpdb->last_error !== '') {
        echo $wpdb->last_error;
        die();//bắt buộc phải có khi kết thúc
    }else{
        echo "Cập nhật thành công.";
        die;
    } 
}

add_action( 'wp_ajax_receive_gift_registration', 'receive_gift_registration' );
add_action( 'wp_ajax_nopriv_receive_gift_registration', 'receive_gift_registration' );
function receive_gift_registration() {
 
    global $wpdb;
    $table = $wpdb->prefix . 'dangkynhanqua';
    //do bên js để dạng json nên giá trị trả về dùng phải encode
    $product = (isset($_REQUEST['product'])) ? esc_attr($_REQUEST['product']) : '';
    $user = (isset($_REQUEST['user'])) ? esc_attr($_REQUEST['user']) : '';

    $data = "";
    if (isset($user) && $user) {
        $sql = "SELECT * FROM {$table} WHERE user_id = {$user}";
        $data = $wpdb->get_row($wpdb->prepare($sql), ARRAY_A);
    }

    if (isset($data) && $data) {
        foreach($data as $d) {
            if(intval($d['status']) == intval(0)) {
                echo "Bạn đang tham gia nhận 1 sản phẩm khác.";
                die;
            }
        }
    }
    $wpdb->insert( 
        $table, 
        array( 
            'user_id'       => intval($user),
            'product_id'    => intval($product),
            'status'        => 0,
            'created_at'    => time(),
            'last_online'   => time(),
            'times_online'  => serialize(array())
        ), 
        array( 
            '%d', 
            '%d', 
            '%d', 
            '%d', 
        ), 
    );
        
    if($wpdb->last_error !== '') {
        echo $wpdb->last_error;
            
        die();//bắt buộc phải có khi kết thúc
    }else{
        echo "Đăng ký nhận quà thành công.";
        die;
        
    } 
    
}

add_action( 'wp_ajax_update_list_registration_gift', 'update_list_registration_gift' );
add_action( 'wp_ajax_nopriv_update_list_registration_gift', 'update_list_registration_gift' );
function update_list_registration_gift() {
 
    global $wpdb;
    $table = $wpdb->prefix . 'dangkynhanqua';
    //do bên js để dạng json nên giá trị trả về dùng phải encode
    $id = (isset($_REQUEST['id'])) ? esc_attr($_REQUEST['id']) : '';
    $status = (isset($_REQUEST['status'])) ? esc_attr($_REQUEST['status']) : '';

    $wpdb->update( 
        $table, 
        array( 
            'status' => intval($status),
        ), 
        array( 'id' => $id ), 
        array( 
            '%d', 
        ), 
        array( '%d' ) 
    );
    
    if($wpdb->last_error !== '') {
        echo $wpdb->last_error;
        die();//bắt buộc phải có khi kết thúc
    }else{
        echo "Cập nhật thành công.";
        die;
    } 
}
