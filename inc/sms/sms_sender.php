<?php 

if(!class_exists('SMS_Sender')) {
    class SMS_Sender
    {

        // class instance
        static $instance;
        // protected $message;
        // protected $message_type;
        // protected $id;

        // class constructor
        public function __construct()
        {
            # code...
        }

        public function sendSMS($message)
        {
            # code...
            
            return $this->sendSMSResponse(true, 'Gửi tin nhắn thành công', $message);
        }

        private function sendSMSResponse($status = true, $message = '', $data = '')
        {
            return array(
                'status'    => $status,
                'message'   => $message,
                'data'      => $data
            );
        }

        public function generateMessage($message, $message_type, $id)
        {
            $log = new WC_Logger();
            $order_data = "";
            $user_data = "";
            switch($message_type) {
                case 'user':
                    $user_data = get_user_by( 'id', $id );
                    break;
                case 'order':
                    $order_data = new WC_Order($id);;
                    $user_data = get_user_by_email( $order_data->get_billing_email() );
                    break;
                default:
                    $order_data = "";
                    $user_data = "";
            }

            $products_data = "";
            if($order_data != "") {
                //$order_items = $order_data->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
                foreach ( $order_data->get_items() as $item_id => $item ) {
                    $name = $item->get_name();
                    $quantity = $item->get_quantity();
                    $products_data = $name . " " . $quantity . __('product', 'woocommerce') . ($item == end($order_data->get_items()) ? '' : ',');
                }
            }

            $data = array(
                'customer_id'       => $user_data != "" ? $user_data->ID : 0,
                'customer_name'     => $user_data != "" ? ($user_data->last_name ? $user_data->last_name : $order_data->get_billing_last_name()) : '', 
                'username'          => $user_data != "" ? $user_data->user_login : '', 
                'password'          => $user_data != "" ? $user_data->user_pass : '', 
                'site_name'         => get_bloginfo('name'), 
                'site_url'          => get_bloginfo('url'), 
                'email'             => $user_data != "" ? $user_data->user_email : '', 
                'phone'             => $user_data != "" ? get_user_meta($user_data->ID, 'billing_phone', true) : '', 
                'order_code'        => $order_data != "" ? $id : 0, 
                'order_products'    => $products_data, 
                'order_summary'     => $order_data != "" ? $order_data->get_total() : '', 
                'order_status'      => $order_data != "" ? $order_data->get_status() : ''
            );

            foreach($data as $key => $value) {
                $message = preg_replace('/{'.$key.'}/i', $value, $message);
            }
            
            return $message;
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
}