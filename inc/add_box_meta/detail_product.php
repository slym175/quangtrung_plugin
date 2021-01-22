<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

add_action( 'admin_menu', 'rudr_metabox_for_select2' );
add_action( 'save_post', 'rudr_save_metaboxdata', 10, 2 );

/*
 * Add a metabox
 * I hope you're familiar with add_meta_box() function, so, nothing new for you here
 */
function rudr_metabox_for_select2() {
    add_meta_box( 'rudr_select2', 'Quà tặng', 'qt_display_select2_metabox', 'product', 'normal', 'default' );
}

/*
 * Display the fields inside it
 */
function qt_display_select2_metabox( $post_object ) {

    // do not forget about WP Nonces for security purposes

    // I decided to write all the metabox html into a variable and then echo it at the end
    $html = '';

    // always array because we have added [] to our <select> name attribute
    $appended_posts = get_post_meta( $post_object->ID, 'gift',true );


    /*
     * Select Posts with AJAX search
     */

    $html .= '<p><label for="gift">Chọn danh sách quà tặng kèm sản phẩm:</label><br /><select id="gift" name="gift[]" multiple="multiple" style="width:99%;max-width:25em;">';

    if( $appended_posts ) {
        foreach( $appended_posts as $post_id ) {
            $title = get_the_title( $post_id );
            // if the post title is too long, truncate it and add "..." at the end
            $title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
            $html .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
        }
    }
    $html .= '</select></p>';

    echo $html;
}


function rudr_save_metaboxdata( $post_id, $post ) {

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

    // if post type is different from our selected one, do nothing
    if ( $post->post_type == 'product' ) {
        if( isset( $_POST['gift'] ) )
            update_post_meta( $post_id, 'gift', $_POST['gift'] );
        else
            delete_post_meta( $post_id, 'gift' );
    }
    return $post_id;
}

add_action( 'wp_ajax_mishagetposts', 'rudr_get_posts_ajax_callback' ); // wp_ajax_{action}
function rudr_get_posts_ajax_callback(){
    $s = $_GET['q'];
    $return = array();
    global $wpdb;
    $search_results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type='gift' AND post_title LIKE '%s' limit 10", '%'. $wpdb->esc_like( $s ) .'%') );

    if( $search_results ){
        foreach ($search_results as $result){
            $title = ( mb_strlen( $result->post_title ) > 50 ) ? mb_substr( $result->post_title, 0, 49 ) . '...' : $result->post_title;
            $return[] = array( $result->ID, $title );
        }
    }
    echo json_encode( $return );
    die;
}