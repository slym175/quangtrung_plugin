<?php
//Works with WooCommerce 3.2.6
add_action( 'woocommerce_shipping_init', 'qt_shipping_method' );
function qt_shipping_method() {
    if ( ! class_exists( 'WCQT_Weight_Shipping_Method' ) ) {
        class WCQT_Weight_Shipping_Method extends WC_Shipping_Method {

            public function __construct( $instance_id = 0 ) {
                $this->instance_id 	        = absint( $instance_id );
                $this->id                   = 'qt_weight_shipping';//this is the id of our shipping method
                $this->method_title         = __( 'Vận chuyển theo cân nặng', QUANGTRUNG_TEXTDOMAIN );
                $this->method_description   = __( 'Giao hàng dựa trên cân nặng', QUANGTRUNG_TEXTDOMAIN );
                //add to shipping zones list
                $this->supports = array(
                    'shipping-zones',
                    // 'settings',
                    'instance-settings',
                    'instance-settings-modal',
                );

                $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Vận chuyển theo cân nặng', QUANGTRUNG_TEXTDOMAIN);

                $this->init();

            }

            function init() {
                // Load the settings API
                $this->init_form_fields();
                $this->init_settings();

                // Save settings in admin if you have any defined
                add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
            }
            //Fields for the settings page
            function init_form_fields() {

                //fileds for the modal form from the Zones window
                $this->instance_form_fields = array(
                    'title' => array(
                        'title'         => __( 'Tiêu đề', QUANGTRUNG_TEXTDOMAIN ),
                        'type'          => 'text',
                        'description'   => __( 'Tiêu đề hiển thị trang ngoài', QUANGTRUNG_TEXTDOMAIN ),
                        'default'       => __( 'Vận chuyển theo cân nặng', QUANGTRUNG_TEXTDOMAIN )
                    ),
                    'cost' => array(
                        'title'         => __( 'Giá', QUANGTRUNG_TEXTDOMAIN ),
                        'type'          => 'number',
                        'description'   => __( 'Giá giao hàng', QUANGTRUNG_TEXTDOMAIN ),
                        'default'       => 4000
                    ),
                );

                //$this->form_fields - use this with the same array as above for setting fields for separate settings page
            }

            public function calculate_shipping( $package = array()) {
            //as we are using instances for the cost and the title we need to take those values drom the instance_settings
                $intance_settings =  $this->instance_settings;

                $weight = 0;
                $cost = 0;
 
                foreach ( $package['contents'] as $item_id => $values ) 
                { 
                    $_product = $values['data']; 
                    $weight = $weight + $_product->get_weight() * $values['quantity']; 
                }
 
                $weight = wc_get_weight( $weight, 'kg' , 'kg');

                $cost = $weight * $intance_settings['cost'];

                // Register the rate
                $this->add_rate( array(
                    'id'      => $this->id,
                    'label'   => $intance_settings['title'],
                    'cost'    => $cost,
                    'package' => $package,
                    'taxes'   => false,
                ));
            }
        }
    }

    //add your shipping method to WooCommers list of Shipping methods
    add_filter( 'woocommerce_shipping_methods', 'add_qt_shipping_method' );
    function add_qt_shipping_method( $methods ) {
        $methods['qt_weight_shipping'] = 'WCQT_Weight_Shipping_Method';
        return $methods;
    }
}