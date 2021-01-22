<?php

global $jal_db_version;
$jal_db_version = '1.0';

function create_hoidap_table() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'hoidap';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name tinytext COLLATE NOT NULL,
		phone tinytext COLLATE NOT NULL,
		email tinytext COLLATE NOT NULL,
		question_type tinytext COLLATE NOT NULL,
		contents text COLLATE NOT NULL,
		link text COLLATE NOT NULL,
		created timestamp NULL DEFAULT current_timestamp()
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

function create_baohanh_table() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'baohanh';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
        bh_code varchar(255) COLLATE NOT NULL,
        order_id int(11) NOT NULL,
        product_id int(11) NOT NULL,
        phone varchar(255) COLLATE NOT NULL,
        customer_name varchar(255) COLLATE DEFAULT NULL,
        time_bh tinyint(4) NOT NULL,
        type_time varchar(50) COLLATE DEFAULT NULL,
        created_at int(11) NOT NULL,
        status tinyint(4) NOT NULL DEFAULT 1
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}