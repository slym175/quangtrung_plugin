<?php 
// define the woocommerce_new_order callback 
function action_woocommerce_new_order( $order_get_id ) { 
    $order = wc_get_order($order_get_id);
    $order_data = array(
        'billing_email'     => $order->get_billing_email(),
        'billing_phone'     => $order->get_billing_phone(),
        'billing_last_name' => $order->get_billing_last_name(),
    );
    
    $log = new WC_Logger();
    $sms = new SMS_Sender();
    if(get_qt_options('enable_create_user') && get_qt_options('enable_create_user') == "on") {
        wc_create_qt_new_customer($order_data['billing_email'], '', '', $order_data['billing_phone'], $order_data['billing_last_name']);
    }

    if(get_qt_options('enable_sms_create_order') && get_qt_options('enable_sms_create_order') == "on") {
        $message = $sms->generateMessage( get_qt_options('sms_create_order_pattern'), 'order', $order_get_id );
        $response = $sms->sendSMS( $message );
        $log->log( 'new-woocommerce-log-name', print_r( $response, true ) );
    }
}; 
         
// add the action 
add_action( 'woocommerce_new_order', 'action_woocommerce_new_order', 10, 1 ); 
    
?>