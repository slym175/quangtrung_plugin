<?php
function qt_deactivate() {
    wp_clear_scheduled_hook( 'check_qua_bao_hanh_cron' );
}
 
function run_qua_bao_hanh_cron() {
    global $wpdb;
    $table_baohanh = $wpdb->prefix . 'baohanh';
    $table_baohanh_items = $wpdb->prefix . 'baohanh_items';

    $min_time = get_option('minimum_bao_hanh_time', 'option') ? get_option('minimum_bao_hanh_time', 'option') : 12;

    $sql = <<<SQL
        SELECT
            {$table_baohanh}.*,
            {$table_baohanh_items}.tem,
            {$table_baohanh_items}.description,
            {$table_baohanh_items}.time,
            {$table_baohanh_items}.attachment
        FROM
            {$table_baohanh}
            LEFT JOIN {$table_baohanh_items} ON {$table_baohanh}.bh_code = {$table_baohanh_items}.bh_code
        WHERE 1=1
            AND {$table_baohanh}.recieved_gift = '0'
            AND {$table_baohanh_items}.bh_code IS NULL
        ORDER BY {$table_baohanh}.bh_code
SQL;
    // closing identifier should not be indented, although it might look ugly
    // ... omitted
    
    $baohanh = $wpdb->get_results($sql);

    $list_bh = array();
    $i = 0;
    foreach($baohanh as $key => $bh) {
        $bh_time = $bh->type_time == 'month' ? $bh->time_bh : $bh->time_bh * 12;
        $date1 = new DateTime(); $date1->format('m/d/Y');
        $date2 = new DateTime(date('m/d/Y', $bh->created_at)); $date2->format('m/d/Y');
        $diff = $date1->diff($date2);
        if(($diff->m >= $bh_time || $diff->m >= $min_time) && $bh->recieved_gift == 0) {
            $list_bh[$i] = $bh->bh_code;
            $i++;
        }
    }

    $strr = "(". implode(',', $list_bh) .")";
    $update_sql = "UPDATE $table_baohanh SET recieved_gift = 1 WHERE bh_code IN $strr";
    $wpdb->query( $wpdb->prepare($update_sql) );
}

add_action('init', function() {
    add_action( 'check_qua_bao_hanh_cron', 'run_qua_bao_hanh_cron' );
    register_deactivation_hook( __FILE__, 'qt_deactivate' );
 
    if (! wp_next_scheduled( 'check_qua_bao_hanh_cron' )) {
        wp_schedule_event( time(), 'daily', 'check_qua_bao_hanh_cron' );
    }
});

?>