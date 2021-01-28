<?php
// send SMS when order change status to PENDING 
function action_woocommerce_send_sms_order_pending($order_id) {
    // $log = new WC_Logger();
    // if(get_qt_options( 'enable_sms_completed_order' ) && get_qt_options( 'enable_sms_completed_order' ) == "on") {
    //     $sms = new SMS_Sender();
    //     $message = $sms->generateMessage( get_qt_options('sms_completed_order_pattern'), 'order', $order_id );
    //     $response = $sms->sendSMS( $message );
    //     $log->log( 'action_woocommerce_send_sms_order_pending', print_r( $response, true ) );
    // }
}
add_action( 'woocommerce_order_status_pending', 'action_woocommerce_send_sms_order_pending');

// send SMS when order change status to FAILED 
function action_woocommerce_send_sms_order_failed($order_id) {
    # code...
}
add_action( 'woocommerce_order_status_failed', 'action_woocommerce_send_sms_order_failed');
   
// send SMS when order change status to ON HOLD 
function action_woocommerce_send_sms_order_hold($order_id) {
    # code...
}
add_action( 'woocommerce_order_status_on-hold', 'action_woocommerce_send_sms_order_hold');
    
// send SMS when order change status to PROCESSING 
function action_woocommerce_send_sms_order_processing($order_id) {
    # code...
}
add_action( 'woocommerce_order_status_processing', 'action_woocommerce_send_sms_order_processing');
   
// send SMS when order change status to COMPLETED 
function action_woocommerce_send_sms_order_completed($order_id) {
    $log = new WC_Logger();
    if(get_qt_options( 'enable_sms_completed_order' ) && get_qt_options( 'enable_sms_completed_order' ) == "on") {
        $sms = new SMS_Sender();
        $message = $sms->generateMessage( get_qt_options('sms_completed_order_pattern'), 'order', $order_id );
        $response = $sms->sendSMS( $message );
        $log->log( 'action_woocommerce_send_sms_order_completed', print_r( $response, true ) );
    }
}
add_action( 'woocommerce_order_status_completed', 'action_woocommerce_send_sms_order_completed');
   
// send SMS when order change status to REFUNDED
function action_woocommerce_send_sms_order_refunded($order_id) {
    # code...
}
add_action( 'woocommerce_order_status_refunded', 'action_woocommerce_send_sms_order_refunded');
  
// send SMS when order change status to CANCELLED 
function action_woocommerce_send_sms_order_cancelled($order_id) {
    # code... 
}
add_action( 'woocommerce_order_status_cancelled', 'action_woocommerce_send_sms_order_cancelled');