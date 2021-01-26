<?php

/*==========================
 This snippet shows how to add a column to the Users admin page with each users' last active date.
 Copy these contents to functions.php
 ===========================*/
 
 //Add columns to user listings
add_filter('manage_users_columns', 'gearside_user_columns_head');
function gearside_user_columns_head($defaults){
    $defaults['status'] = 'Status';
    return $defaults;
}
add_action('manage_users_custom_column', 'gearside_user_columns_content', 15, 3);
function gearside_user_columns_content($value='', $column_name, $id){
    if ( $column_name == 'status' ){
		if ( gearside_is_user_online($id) ){
			return '<strong style="color: green;">Online</strong>';
		} else {
			return ( gearside_user_last_online($id) )? '<small>Last Seen: <br /><em>' . date('M j, Y @ g:ia', gearside_user_last_online($id)) . '</em></small>' : ''; //Return the user's "Last Seen" date, or return empty if that user has never logged in.
		}
	}
}

/*==========================
 This snippet shows how to add an active user count to the WordPress Dashboard.
 Copy these contents to functions.php
 ===========================*/

//Active Users Metabox
add_action('wp_dashboard_setup', 'gearside_activeusers_metabox');
function gearside_activeusers_metabox(){
	global $wp_meta_boxes;
	wp_add_dashboard_widget('gearside_activeusers', 'Active Users', 'dashboard_gearside_activeusers');
}
function dashboard_gearside_activeusers(){
		$user_count = count_users();
		$users_plural = ( $user_count['total_users'] == 1 )? 'User' : 'Users'; //Determine singular/plural tense
		echo '<div><a href="users.php">' . $user_count['total_users'] . ' ' . $users_plural . '</a> <small>(' . gearside_online_users('count') . ' currently active)</small></div>';
}

//Get a count of online users, or an array of online user IDs.
//Pass 'count' (or nothing) as the parameter to simply return a count, otherwise it will return an array of online user data.
function gearside_online_users($return='count'){
    $logged_in_users = get_transient('wp_users_status');
    $expired_time = get_option( 'qt_options', 'option' )['min_online_time'] ? get_option( 'qt_options', 'option' )['min_online_time'] * 60 : 300;
    $minus = time() - $expired_time;
	
	//If no users are online
	if ( empty($logged_in_users) ){
		return ( $return == 'count' )? 0 : false; //If requesting a count return 0, if requesting user data return false.
	}
	
	$user_online_count = 0;
	$online_users = array();
	foreach ( $logged_in_users as $user ){
		if ( !empty($user['username']) && isset($user['last']) && $user['last'] > $minus ){ //If the user has been online in the last 900 seconds, add them to the array and increase the online count.
			$online_users[] = $user;
			$user_online_count++;
		}
	}

	return ( $return == 'count' )? $user_online_count : $online_users; //Return either an integer count, or an array of all online user data.
}