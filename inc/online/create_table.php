<?php

global $jal_db_version;
$jal_db_version = '1.0';

function create_user_online_table() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'online_user';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		ID mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint( 20 ) NOT NULL default 0,
		user_type varchar( 20 ) NOT NULL default 'guest',
		user_name varchar( 250 ) NOT NULL default '',
		user_ip varchar( 39 ) NOT NULL default '',
		user_agent text NOT NULL,
        last_online datetime NOT NULL,
        is_online tinyint(9) NOT NULL default 0,
        online_times text NOT NULL,
		PRIMARY KEY  (ID)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}