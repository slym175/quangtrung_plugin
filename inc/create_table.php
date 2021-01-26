<?php

global $wpdb;

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$charset_collate = $wpdb->get_charset_collate();

$table_hoidap = $wpdb->prefix . 'hoidap';
$sql_hoidap = "CREATE TABLE $table_hoidap (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	name tinytext COLLATE NOT NULL,
	phone tinytext COLLATE NOT NULL,
	email tinytext COLLATE NOT NULL,
	question_type tinytext COLLATE NOT NULL,
	contents text COLLATE NOT NULL,
	link text COLLATE NOT NULL,
	created timestamp NULL DEFAULT current_timestamp(),
	PRIMARY KEY  (id)
) $charset_collate;";
dbDelta( $sql_hoidap );

$table_baohanh = $wpdb->prefix . 'baohanh';
$sql_baohanh = "CREATE TABLE $table_baohanh (
    bh_code varchar(255) COLLATE NOT NULL,
    order_id int(11) NOT NULL,
    product_id int(11) NOT NULL,
    phone varchar(255) COLLATE NOT NULL,
    customer_name varchar(255) COLLATE DEFAULT NULL,
    time_bh tinyint(4) NOT NULL,
    type_time varchar(50) COLLATE DEFAULT NULL,
	recieved_gift tinyint(4) NOT NULL DEFAULT 0,
    gift mediumint(9) NOT NULL DEFAULT 0,
    created_at int(11) NOT NULL,
    status tinyint(4) NOT NULL DEFAULT 1
) $charset_collate;";
dbDelta( $sql_baohanh );

$table_baohanh_items = $wpdb->prefix . 'baohanh_items';
$sql_baohanh_items = "CREATE TABLE $table_baohanh_items (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
    bh_code varchar(255) COLLATE NOT NULL,
    status varchar(255) NOT NULL,
    tem varchar(255) NOT NULL,
    created_at int(11) COLLATE NOT NULL,
    description text COLLATE DEFAULT NULL,
    time int(11) NOT NULL,
    attachment varchar(500) COLLATE DEFAULT NULL
) $charset_collate;";
dbDelta( $sql_baohanh_items );

// $table_online_user = $wpdb->prefix . 'users_online';
// $sql_online_user = "CREATE TABLE $table_online_user (
// 	id mediumint(9) NOT NULL AUTO_INCREMENT,
//     user_id mediumint(9) NOT NULL,
// 	product_id mediumint(9) NOT NULL,
// 	last_online int(11) NOT NULL,
// 	times_online text NOT NULL,
// 	created_at int(11) NOT NULL,
//     PRIMARY KEY (id)
// ) $charset_collate;";
// dbDelta( $sql_online_user );


// status 0: đang thực hiện, 1: đã hoàn thành, 2: hủy bỏ
$table_dangkynhanqua = $wpdb->prefix . 'dangkynhanqua';
$sql_dangkynhanqua = "CREATE TABLE $table_dangkynhanqua (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
    user_id mediumint(9) NOT NULL,
	product_id mediumint(9) NOT NULL,
	status tinyint(4) NOT NULL DEFAULT 0,
	created_at int(11) NOT NULL,
    last_online int(11) NOT NULL,
    times_online text NOT NULL,
    PRIMARY KEY (id)
) $charset_collate;";
dbDelta( $sql_dangkynhanqua );