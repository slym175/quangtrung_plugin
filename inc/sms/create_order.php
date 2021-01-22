<?php 

function action_woocommerce_send_sms_new_order( $order_get_id ) { 
    $order = wc_get_order($order_get_id);
    $log = new WC_Logger();
    if(get_option( 'enable_new_order', 'option' )) {
        $log->log( 'new-woocommerce-log-name', print_r( $order_data, true ) );
    }
}; 
         
// add the action 
add_action( 'woocommerce_new_order', 'action_woocommerce_send_sms_new_order', 10, 1 ); 


?>