<?php
if(!class_exists("GsSettingsPage")) {
    class GsSettingsPage
    {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private $options;

        /**
         * Start up
         */
        public function __construct()
        {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );
        }

        /**
         * Add options page
         */
        public function add_plugin_page()
        {
            // This page will be under "Settings"
            add_options_page(
                'Cài đặt Quang Trung', 
                'Cài đặt Quang Trung', 
                'manage_options', 
                'qt-settings', 
                array( $this, 'create_plugin_settings_page' )
            );
        }

        /**
         * Options page callback
         */
        public function create_plugin_settings_page()
        {
            // Set class property
            $this->options = get_option( 'qt_options' );
            ?>
            <div class="wrap">
                <form method="post" action="options.php">
                    <?php
                        // This prints out all hidden setting fields
                        settings_fields( 'qt_option_group' );
                        do_settings_sections( 'qt-settings' );
                        submit_button();
                    ?>
                </form>
            </div>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#enable_create_user, #enable_sms_create_user, #enable_sms_create_order, #enable_sms_success_order').change(function (e) { 
                        e.preventDefault();
                        if(jQuery(this).prop('checked')) {
                            jQuery(this).val('on')
                        }else{
                            jQuery(this).val('off')
                        }
                    });
                });
            </script>
            <?php
        }

        /**
         * Register and add settings
         */
        public function page_init()
        {        
            register_setting(
                'qt_option_group', // Option group
                'qt_options', // Option name
                array() // Sanitize
            );

            add_settings_section(
                'gt_customer_setting_section', // ID
                'Khách hàng mới', // Title
                '', // Callback
                'qt-settings' // Page
            );   

            add_settings_field(
                'enable_create_user', // ID
                'Tạo tài khoản mới', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_customer_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'enable_create_user', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'checkbox', // Field Type
                    'subtype'       => 'single', // Field Subtype
                    'name'          => 'enable_create_user', // Field Name
                    'description'   => __('Bật/tắt tự động tạo tài khoản khách hàng sau khi mua hàng (chưa có tài khoản).', GS_TEXTDOMAIN),
                    'options'       => array(
                        
                    )
                ) // Callback Arguments          
            );

            add_settings_section(
                'gt_sms_setting_section', // ID
                'Tin nhắn', // Title
                '', // Callback
                'qt-settings' // Page
            ); 

            add_settings_field(
                'enable_sms_create_user', // ID
                'Tài khoản mới', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_sms_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'enable_sms_create_user', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'checkbox', // Field Type
                    'subtype'       => 'single', // Field Subtype
                    'name'          => 'enable_sms_create_user', // Field Name
                    'description'   => __('Bật/tăt gửi tin nhắn khi tạo tài khoản khách hàng.', GS_TEXTDOMAIN),
                    'options'       => array(
                        
                    )
                ) // Callback Arguments          
            );

            add_settings_field(
                'sms_create_user_pattern', // ID
                'Nội dung tin nhắn tài khoản mới', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_sms_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'sms_create_user_pattern', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'textarea', // Field Type
                    'subtype'       => 'text', // Field Subtype
                    'name'          => 'sms_create_user_pattern', // Field Name
                    'description'   => __('Nội dung tin nhắn gửi đi: {customer_id}, {customer_name}, {username}, {password}, {site_name}, {site_url}, {email}, {phone}.', GS_TEXTDOMAIN),
                    'options'       => array(
                        'rows'      => 3,
                    )
                ) // Callback Arguments          
            );

            add_settings_field(
                'enable_sms_create_order', // ID
                'Đơn hàng mới', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_sms_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'enable_sms_create_order', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'checkbox', // Field Type
                    'subtype'       => 'single', // Field Subtype
                    'name'          => 'enable_sms_create_order', // Field Name
                    'description'   => __('Bật/tăt gửi tin nhắn khi khách hàng mua hàng tại website.', GS_TEXTDOMAIN),
                    'options'       => array(
                        
                    )
                ) // Callback Arguments          
            );

            add_settings_field(
                'sms_create_order_pattern', // ID
                'Nội dung tin nhắn đơn hàng mới', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_sms_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'sms_create_order_pattern', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'textarea', // Field Type
                    'subtype'       => 'text', // Field Subtype
                    'name'          => 'sms_create_order_pattern', // Field Name
                    'description'   => __('Nội dung tin nhắn gửi đi: {customer_id}, {customer_name}, {username}, {password}, {site_name}, {site_url}, {email}, {phone}, {order_code}, {order_products}, {order_summary}, {order_status}', GS_TEXTDOMAIN),
                    'options'       => array(
                        'rows'      => 3,
                    )
                ) // Callback Arguments          
            );

            add_settings_field(
                'enable_sms_success_order', // ID
                'Đơn hàng thành công', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_sms_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'enable_sms_success_order', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'checkbox', // Field Type
                    'subtype'       => 'single', // Field Subtype
                    'name'          => 'enable_sms_success_order', // Field Name
                    'description'   => __('Bật tăt gửi tin nhắn khi đơn hàng thành công.', GS_TEXTDOMAIN),
                    'options'       => array(
                        
                    )
                ) // Callback Arguments          
            );

            add_settings_field(
                'sms_success_order_pattern', // ID
                'Nội dung tin nhắn đơn hàng thành công', // Title 
                array( $this, 'gs_render_settings_field' ), // Callback
                'qt-settings', // Page
                'gt_sms_setting_section', // Section 
                array (
                    'parent'        => 'qt_options', // Option name
                    'id'            => 'sms_success_order_pattern', // Field ID
                    'class'         => 'regular-text one-line', // Field ID
                    'type'          => 'textarea', // Field Type
                    'subtype'       => 'text', // Field Subtype
                    'name'          => 'sms_success_order_pattern', // Field Name
                    'description'   => __('Nội dung tin nhắn gửi đi: {customer_id}, {customer_name}, {username}, {password}, {site_name}, {site_url}, {email}, {phone}, {order_code}, {order_products}, {order_summary}, {order_status}', GS_TEXTDOMAIN),
                    'options'       => array(
                        'rows'      => 3,
                    )
                ) // Callback Arguments          
            );
        }

        /** 
         * Get the settings option array and print one of its values
         */
        function gs_render_settings_field($args) {
            $field_options = $this->inputOptions($args['options']);
            $field_name = isset($args['parent']) ? $args['parent'].'['.$args['name'].']' : $args['name'];
            
            switch($args['type']) {
                case 'input':
                    switch($args['subtype']){
                        case 'text':
                            printf(
                                '<input type="%1$s" class="%2$s" id="%3$s" name="%4$s" %5$s value="%6$s" />',
                                $args['subtype'],
                                $args['class'],
                                $args['id'],
                                $field_name,
                                $field_options,
                                sanitize_text_field( isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : '' )
                            );
                            printf('<p>%1$s</p>', $args['description']);
                            break;
                        case 'number':
                            printf(
                                '<input type="%1$s" class="%2$s" id="%3$s" name="%4$s" %5$s value="%6$s" />',
                                $args['subtype'],
                                $args['class'],
                                $args['id'],
                                $field_name,
                                $field_options,
                                intval( isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : '' )
                            );
                            printf('<p>%1$s</p>', $args['description']);
                            break;
                        default :
                            echo __('Chọn loại input cho trường cài đặt!!');
                    }
                    break;
                case 'textarea':
                    printf(
                        '<textarea type="%1$s" class="%2$s" id="%3$s" name="%4$s" %5$s>%6$s</textarea>',
                        $args['subtype'],
                        $args['class'],
                        $args['id'],
                        $field_name,
                        $field_options,
                        sanitize_text_field( isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : '' )
                    );
                    printf('<p>%1$s</p>', $args['description']);
                    break;
                case 'checkbox':
                    if($args['subtype'] == 'single') {
                        printf(
                            '<input type="checkbox" class="%1$s" id="%2$s" name="%3$s" value="%4$s" %5$s><label for="%6$s">%7$s</label>',
                            $args['class'],
                            $args['id'],
                            $field_name,
                            sanitize_text_field( isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : 'off' ),
                            $this->options[$args['id']] == "on" ? 'checked' : '',
                            $field_name,
                            $args['description']
                        );
                    } else {
                        printf(
                            '<input type="checkbox" class="%1$s" id="%2$s" name="%3$s" value="%4$s" %5$s><label for="">Label</label>',
                            $args['class'],
                            $args['id'],
                            $field_name,
                            sanitize_text_field( isset( $this->options[$args['id']] ) ? esc_attr( $this->options[$args['id']]) : '' ),
                            $field_options,
                        );
                        printf('<p>%1$s</p>', $args['description']);
                    }
                    break;
                default: 
                    echo __('Chọn loại input cho trường cài đặt!!');
            }
            
        }

        function inputOptions($array)
        {
            if(empty($array) || $array == "") {
                return "";
            }
            $output = "";
            foreach($array as $key => $item) {
                if($item === true) {
                    $output = $output." ".$key.'="true"';
                }elseif($item === false) {
                    $output = $output." ".$key.'="false"';
                }else{
                    $output = $output." ".$key.'="'.$item.'"';
                }
            }
            return $output;
        }
    }
    
    $settings = new GsSettingsPage();
}