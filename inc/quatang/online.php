<?php 

add_action('init', 'user_online_update');
function user_online_update()
{
    $min_time = get_option('min_online_time', 'option') ? intval(get_option('min_online_time', 'option')): 1;
    // get the user activity the list
    $logged_in_users = get_transient('wp_online_statuses');

    // get current user ID
    $user = wp_get_current_user();
    
    if( isset($user) ) {
        // check if the current user needs to update his online status;
        // he does if he doesn't exist in the list
        $no_need_to_update = isset($logged_in_users[$user->ID])
        // and if his "last activity" was less than let's say ...$min_time minutes ago          
        && $logged_in_users[$user->ID]['last_online'] > (time() - ($min_time * 60));

        // update the list if needed
        if(!$no_need_to_update){
            $logged_in_users[$user->ID] = array(
                'user_id'       => $user->ID,
                'last_online'   => time()
            );
            set_transient('wp_online_statuses', $logged_in_users, $expire_in = ($min_time * 60));
            // update database
            $log = new WC_Logger();
            $log->log( 'new-online-name', print_r(get_transient('wp_online_statuses'), true) );
        }
    }
}

function is_wp_user_online($user_id)
{
    $data = get_transient('wp_online_statuses');
    if(isset($data[$user_id]) && $data[$user_id]) {
        return true;
    }
    return false;
}

function is_wp_user_offline($user_id)
{
    $min_time = get_option('min_online_time', 'option') ? intval(get_option('min_online_time', 'option')): 1;
    $expire_in = $min_time * 60;
    $data = get_transient('wp_online_statuses');
    if(isset($data[$user_id]) && $data[$user_id]) {
        if(time() - $data[$user_id][1] >= $expire_in) {
            return true;
        }
    }
    return false;
}

function wp_get_users_online()
{
    return get_transient('wp_online_statuses');
}