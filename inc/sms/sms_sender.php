<?php 

if(!class_exists('SMS_Sender')) {
    class SMS_Sender
    {

        // class instance
        static $instance;

        // class constructor
        public function __construct()
        {
        }

        public function sendSMS()
        {
            # code...
        }

        public function status()
        {
            # code...
        }

        public function generateOrderMessage($message, $order_id)
        {
            //$order = wc_get_order($order_id);      
            $data = array(
                'customer_id'       => '1',
                'customer_name'     => 'thuyluong', 
                'username'          => 'thuyluong_user', 
                'password'          => 'thuyluong_pass', 
                'site_name'         => 'QuangTrung', 
                'site_url'          => 'localhost', 
                'email'             => 'thuyhu9876@gmail.com', 
                'phone'             => '0986114671', 
                'order_code'        => '932f23', 
                'order_products'    => 'Sanpham1, Sanpham2', 
                'order_summary'     => '400.000vnd', 
                'order_status'      => 'Dang xu ly'
            );

            foreach($data as $key => $value) {
                $message = preg_replace('/{'.$key.'}/i', $value, $message);
            }
            return $message;

            // Usage
            // $sms = new SMS_Sender();
            // echo $sms->generateOrderMessage(get_option('qt_options')['sms_create_order_pattern'], $order_id);
        }

        public function generateUserMessage($message, $user_id)
        {
            $user = get_user_by('id', $user_id); 
            $data = array(
                'customer_id'   => '',
                'customer_name' => '',
                'username'      => '',
                'password'      => '',
                'site_name'     => '',
                'site_url'      => '',
                'email'         => '',
                'phone'         => ''
            );
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