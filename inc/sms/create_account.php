<?php 
//-----------------------------------------------------
// Create new user
//-----------------------------------------------------
function wc_create_qt_new_customer( $email, $username = '', $password = '', $phone , $last_name) { 
 
    // Check the email address. Because this function called after order created => email always filled
    // if ( empty( $email ) || ! is_email( $email ) ) { 
    //     return new WP_Error( 'registration-error-invalid-email', __( 'Please provide a valid email address.', 'woocommerce' ) ); 
    // } 
 
    // if ( email_exists( $email ) ) { 
    //     return new WP_Error( 'registration-error-email-exists', __( 'An account is already registered with your email address. Please login.', 'woocommerce' ) ); 
    // } 
    if ( email_exists( $email ) ) { 
        return array(
            'status'    => false,
            'message'   => __( 'An account is already registered with your email address.', 'woocommerce' ),
            'data'      => null
        ); 
    } 
 
    // Handle username creation. 
    if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) || ! empty( $username ) ) { 
        $username = sanitize_user( $username ); 
        if ( empty( $username ) || ! validate_username( $username ) ) { 
            return array(
                'status'    => false,
                'message'   => __( 'Please enter a valid account username.', 'woocommerce' ),
                'data'      => null
            );
        } 
        if ( username_exists( $username ) ) { 
            return array(
                'status'    => false,
                'message'   => __( 'An account is already registered with that username. Please choose another.', 'woocommerce' ),
                'data'      => null
            );
        } 
    } else { 
        $username = sanitize_user( current( explode( '@', $email ) ), true ); 
 
        // Ensure username is unique. 
        $append = 1; 
        $o_username = $username; 
 
        while ( username_exists( $username ) ) { 
            $username = $o_username . $append; 
            $append++; 
        } 
    } 
 
    // Handle password creation. 
    if ( 'yes' === get_option( 'woocommerce_registration_generate_password' ) && empty( $password ) ) { 
        $password = wp_generate_password(10, true, true); 
        $password_generated = true; 
    } elseif ( empty( $password ) ) { 
        return array(
            'status'    => false,
            'message'   => __( 'Please enter an account password.', 'woocommerce' ),
            'data'      => null
        );
    } else { 
        $password_generated = false; 
    } 
 
    // Use WP_Error to handle registration errors. 
    $errors = new WP_Error(); 
 
    do_action( 'woocommerce_register_post', $username, $email, $errors ); 
 
    $errors = apply_filters( 'woocommerce_registration_errors', $errors, $username, $email ); 
 
    if ( $errors->get_error_code() ) { 
        return array(
            'status'    => false,
            'message'   => $errors,
            'data'      => null
        ); 
    } 
 
    $new_customer_data = apply_filters( 'woocommerce_new_customer_data', array( 
        'user_login'    => $username,  
        'user_pass'     => $password,  
        'user_email'    => $email, 
        'last_name'     => $last_name, 
        'role'          => 'customer',  
    ) ); 
 
    $customer_id = wp_insert_user( $new_customer_data ); 

    if ( is_wp_error( $customer_id ) ) { 
        return array(
            'status'    => false,
            'message'   => __( 'Couldn’t register you… please contact us if you continue to have problems.', 'woocommerce' ),
            'data'      => null
        ); 
    } else {
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field($phone) );
        update_user_meta( $customer_id, 'billing_phone', sanitize_text_field($phone) );
    }
 
    do_action( 'woocommerce_created_customer', $customer_id, $new_customer_data, $password_generated ); 
 
    return array(
        'status'    => true,
        'message'   => __( 'Account created', 'woocommerce' ),
        'data'      => $customer_id
    );; 
} 
?>