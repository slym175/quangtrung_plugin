<?php

/*==========================
 This snippet contains utility functions to create/update and pull data from the active user transient.
 Copy these contents to functions.php
 ===========================*/

//Update user online status
add_action('init', 'gearside_users_status_init');
add_action('admin_init', 'gearside_users_status_init');
function gearside_users_status_init(){
	$logged_in_users = get_transient('wp_users_status'); //Get the active users from the transient.
	$user = wp_get_current_user(); //Get the current user's data

	$expired_time = get_option( 'qt_options', 'option' )['min_online_time'] ? get_option( 'qt_options', 'option' )['min_online_time'] * 60 : 300;
	$minus = time() - $expired_time;

	//Update the user if they are not on the list, or if they have not been online in the last 900 seconds (15 minutes)
	if ( !isset($logged_in_users[$user->ID]['last']) || $logged_in_users[$user->ID]['last'] <= $minus ){
		$logged_in_users[$user->ID] = array(
			'id' 		=> $user->ID,
			'username' 	=> $user->user_login,
			'last' 		=> time(),
		);
		set_transient('wp_users_status', $logged_in_users, $expired_time); //Set this transient to expire 15 minutes after it is created.
		update_user_online($user->ID, $logged_in_users[$user->ID]['last']);
	}
}

//Check if a user has been online in the last 15 minutes
function gearside_is_user_online($id){	
	$expired_time = get_option( 'qt_options', 'option' )['min_online_time'] ? get_option( 'qt_options', 'option' )['min_online_time'] * 60 : 300;
	$logged_in_users = get_transient('wp_users_status'); //Get the active users from the transient.
	
	return isset($logged_in_users[$id]['last']) && $logged_in_users[$id]['last'] > $minus; //Return boolean if the user has been online in the last 900 seconds (15 minutes).
}

//Check when a user was last online.
function gearside_user_last_online($id){
	$logged_in_users = get_transient('wp_users_status'); //Get the active users from the transient.
	
	//Determine if the user has ever been logged in (and return their last active date if so).
	if ( isset($logged_in_users[$id]['last']) ){
		return $logged_in_users[$id]['last'];
	} else {
		return false;
	}
}

function update_user_online($user_id, $last_time)
{
	if($id == 0 || $id) {
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'dangkynhanqua';
	
	
}