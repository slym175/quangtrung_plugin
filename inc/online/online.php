<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
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

function update_user_online($user_id, $last_time_after)
{
	$log = new WC_Logger();
	if($user_id == 0) {
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'dangkynhanqua';
	$rows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d AND status = 0", $user_id ) );

	$log->log( 'demo-log-divide', '---------------------------------------------------<br>----------------------------------------------------------');
	$log->log( 'demo-log-rows', print_r( date('Y-m-d H:i:s', $rows->last_online), true ) );

	$times = get_qt_options('daily_login_time') != null ? explode(',', trim(get_qt_options('daily_login_time'))) : null;

	$log->log( 'demo-log-times', print_r( $times, true ) );

	if(is_array($times) && isset($times)) {
		foreach($times as $timee) {
			$from = strtotime(date('Y-m-d', time()) ." ". explode("-", $timee)[0] .":00");
			$to = strtotime(date('Y-m-d', time()) ." ". explode("-", $timee)[1] .":00");

			// $log->log( 'demo-log-from', print_r( $from, true ) );
			// $log->log( 'demo-log-to', print_r( $to, true ) );
			// $log->log( 'demo-log-last_time_after', print_r( $last_time_after, true ) );

			// $log->log( 'demo-log-from', print_r( date('Y-m-d', time()) ." ". explode("-", $timee)[0] .":00", true ) );
			// $log->log( 'demo-log-to', print_r( date('Y-m-d', time()) ." ". explode("-", $timee)[1] .":00", true ) );
			// $log->log( 'demo-log-last_time_after', print_r( date('Y-m-d H:i:s', $last_time_after), true ) );

			if($last_time_after > $from && $last_time_after < $to && $rows && ($rows->last_online < $from || $rows->last_online > $to)) {
				$log->log( 'demo-log-true', print_r( $user_id, true ) );
				$old_times_online = (array) $rows->times_online;
				$new_times_online = array();
				$new_times_online[date('d-m-Y', time())] = $old_times_online[date('d-m-Y', time())] ? $old_times_online[date('d-m-Y', time())] + 1 : 1;
				$times_online = array_merge($old_times_online, $new_times_online);
				$wpdb->update( 
					$table, 
					array( 
						'last_online' 	=> $last_time_after,   // string
						'times_online' 	=> maybe_serialize($times_online)    // integer (number) 
					), 
					array( 'id' => $rows->id ), 
					array( 
						'%d',   // value1
						'%s'    // value2
					), 
					array( '%d' ) 
				);
				$log->log( 'demo-log-true', print_r( $new_times_online, true ) );
			}else{
				$log->log( 'demo-log-false', print_r( $rows, true ) );
			}
		}
	}
}