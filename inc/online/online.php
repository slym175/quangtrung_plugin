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
	// $log = new WC_Logger();
	if($user_id == 0) {
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'dangkynhanqua';
	$rows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d AND status = 0", $user_id ) );
	$times = get_qt_options('daily_login_time') != null ? explode(',', trim(get_qt_options('daily_login_time'))) : null;

	// $log->log( 'demo-log-times', print_r( $times, true ) );

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
				// $log->log( 'demo-log-true', print_r( $user_id, true ) );
				$old_times_online = (array) json_decode($rows->times_online);
				$new_times_online = array();
				if(isset($old_times_online[date('d-m-Y', time())]) && $old_times_online[date('d-m-Y', time())]) {
					$old_times_online[date('d-m-Y', time())] = 0;
				}
				$new_times_online[date('d-m-Y', time())] = $old_times_online[date('d-m-Y', time())] + 1;
				$times_online = array_merge($old_times_online, $new_times_online);

				// $log->log( 'old_times_online', print_r( $old_times_online, true ) );
				// $log->log( 'new_times_online', print_r( $new_times_online, true ) );
				// $log->log( 'times_online', print_r( $times_online, true ) );
				
				$wpdb->update( 
					$table, 
					array( 
						'last_online' 	=> $last_time_after, 
						'times_online' 	=> json_encode($times_online) 
					), 
					array( 'id' => $rows->id ), 
					array( 
						'%d', 
						'%s'
					), 
					array( '%d' ) 
				);

				if(checkGiftCondition($rows)) {
					$wpdb->update( 
						$table, 
						array( 
							'status' => 1, 
						), 
						array( 'id' => $rows->id ), 
						array( 
							'%d', 
						), 
						array( '%d' ) 
					);
				}

				// $log->log( 'demo-log-true', print_r( $new_times_online, true ) );
			}
		}
	}
}

function checkGiftCondition($gift)
{
    $min_date = get_post_meta( $gift->product_id, 'so_ngay_dang_nhap', true);
    $online_times = get_post_meta( $gift->product_id, 'so_lan_trong_ngay', true);

	$array_rg = json_decode($gift->times_online);
	$date = 0;

	if(isset($array_rg) && is_array($array_rg)) {
		foreach($array_rg as $value) {
			if($value >= $online_times) {
				$date++;
			}
		}
	}

	if($date <= $min_date) {
		return true;
	}

	return false;
}