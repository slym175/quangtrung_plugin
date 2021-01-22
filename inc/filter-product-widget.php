<?php

class FilterProduct extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'filter_product',
            'Lọc theo thuộc tính sản phẩm',
            array('description' => 'Widget lọc sản phẩm')
        );
    }

    function form($instance)
    {

        $default = array(
            'title' => 'Tiêu đề widget',
            'attribute_id' => ''
        );
        $instance = wp_parse_args((array)$instance, $default);
        $title = esc_attr($instance['title']);
        $post_number = esc_attr($instance['attribute_id']);

        $attributes = wc_get_attribute_taxonomies();


        echo '<p>Nhập tiêu đề </p><input type="text" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $title . '"/>';
        echo '<p>Chọn thuộc tính</p><select name="' . $this->get_field_name('attribute_id') . '" id="" class="form-control">';
        foreach ($attributes as $attribute) {
            $selected = ($attribute->attribute_name == $post_number) ? 'selected' : '';
            echo '<option value="' . $attribute->attribute_name . '" ' . $selected . '>' . $attribute->attribute_label . '</option>';
        }
        echo '</select>';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['attribute_id'] = strip_tags($new_instance['attribute_id']);
        return $instance;
    }

    // Hàm build giao diện hiển thị
    function widget($args, $instance)
    {
        if(wp_is_mobile()){
            $this->widget_mobile($args, $instance);
        }else{
            extract($args);
            $title = apply_filters('widget_title', $instance['title']);
            echo '<div class="title-cate">
                <h2>'.$title.'</h2>
            </div>';

            $taxonomy = 'pa_' .$instance['attribute_id'];
            $arr = [
                'taxonomy' => $taxonomy,
            ];
            $attributes = get_terms($arr);

            $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes(); //Những thuộc tính đang đc filter
            $base_link          = $this->get_current_page_url(); // Link hiện tại của url
            $query_type = 'or';
            echo '<div class="check-box-item">';
            foreach ($attributes as $term): ?>
                <?php
                $current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
                $option_is_set  = in_array( $term->slug, $current_values, true );

                // Skip the term for the current archive.
                if ( $this->get_current_term_id() === $term->term_id ) {
                    continue;
                }

                $filter_name    = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
                $current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array(); // WPCS: input var ok, CSRF ok.
                $current_filter = array_map( 'sanitize_title', $current_filter );

                if ( ! in_array( $term->slug, $current_filter, true ) ) {
                    $current_filter[] = $term->slug;
                }

                $link = remove_query_arg( $filter_name, $base_link );

                // Add current filters to URL.
                foreach ( $current_filter as $key => $value ) {
                    // Exclude query arg for current term archive term.
                    if ( $value === $this->get_current_term_slug() ) {
                        unset( $current_filter[ $key ] );
                    }

                    // Exclude self so filter can be unset on click.
                    if ( $option_is_set && $value === $term->slug ) {
                        unset( $current_filter[ $key ] );
                    }
                }

                if ( ! empty( $current_filter ) ) {
                    asort( $current_filter );
                    $link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );

                    // Add Query type Arg to URL.
                    if ( 'or' === $query_type && ! ( 1 === count( $current_filter ) && $option_is_set ) ) {
                        $link = add_query_arg( 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ), 'or', $link );
                    }
                    $link = str_replace( '%2C', ',', $link );
                }

                ?>
                <label class="check-cate">
                    <input type="checkbox" class="product-attribute" id="<?= $term->name ?>" name="<?= $term->name ?>" data-url="<?= esc_url( $link ) ?>" <?= ($option_is_set) ? 'checked'  : '' ?>>
                    <?= $term->name ?>
                </label>
            <?php endforeach;
            echo '</div>';
        }
    }

    function widget_mobile($args, $instance){
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo '<div class="col-12 p-0 py-2">
            <span class="filter-title font-weight-bold">' . $title . '</span>
        </div>';

        $taxonomy = 'pa_' .$instance['attribute_id'];
        $arr = [
            'taxonomy' => $taxonomy,
        ];
        $attributes = get_terms($arr);

        $_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes(); //Những thuộc tính đang đc filter
        $base_link          = $this->get_current_page_url(); // Link hiện tại của url
        $query_type = 'or';

        foreach ($attributes as $term): ?>
            <?php
            $current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
            $option_is_set  = in_array( $term->slug, $current_values, true );

            // Skip the term for the current archive.
            if ( $this->get_current_term_id() === $term->term_id ) {
                continue;
            }

            $filter_name    = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
            $current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array(); // WPCS: input var ok, CSRF ok.
            $current_filter = array_map( 'sanitize_title', $current_filter );

            if ( ! in_array( $term->slug, $current_filter, true ) ) {
                $current_filter[] = $term->slug;
            }

            $link = remove_query_arg( $filter_name, $base_link );

            // Add current filters to URL.
            foreach ( $current_filter as $key => $value ) {
                // Exclude query arg for current term archive term.
                if ( $value === $this->get_current_term_slug() ) {
                    unset( $current_filter[ $key ] );
                }

                // Exclude self so filter can be unset on click.
                if ( $option_is_set && $value === $term->slug ) {
                    unset( $current_filter[ $key ] );
                }
            }

            if ( ! empty( $current_filter ) ) {
                asort( $current_filter );
                $link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );

                // Add Query type Arg to URL.
                if ( 'or' === $query_type && ! ( 1 === count( $current_filter ) && $option_is_set ) ) {
                    $link = add_query_arg( 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ), 'or', $link );
                }
                $link = str_replace( '%2C', ',', $link );
            }

            ?>
            <div class="col-6 p-0">
                <div class="c-checkbox-inline">
                    <input type="checkbox" class="d-none product-attribute" id="<?= $term->name ?>" name="<?= $term->name ?>" data-url="<?= esc_url( $link ) ?>" <?= ($option_is_set) ? 'checked'  : '' ?>>
                    <label class="c-checkbox-label" for="<?= $term->name ?>"><?= $term->name ?></label>
                </div>
            </div>
        <?php endforeach;
    }



    protected function get_current_term_id() {
        return absint( is_tax() ? get_queried_object()->term_id : 0 );
    }

    protected function get_current_term_slug() {
        return absint( is_tax() ? get_queried_object()->slug : 0 );
    }


    protected function get_current_page_url()
    {

        $queried_object = get_queried_object();
        $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        // Min/Max.
        if (isset($_GET['min_price'])) {
            $link = add_query_arg('min_price', wc_clean(wp_unslash($_GET['min_price'])), $link);
        }

        if (isset($_GET['max_price'])) {
            $link = add_query_arg('max_price', wc_clean(wp_unslash($_GET['max_price'])), $link);
        }

        // Order by.
        if (isset($_GET['orderby'])) {
            $link = add_query_arg('orderby', wc_clean(wp_unslash($_GET['orderby'])), $link);
        }

        /**
         * Search Arg.
         * To support quote characters, first they are decoded from &quot; entities, then URL encoded.
         */
        if (get_search_query()) {
            $link = add_query_arg('s', rawurlencode(htmlspecialchars_decode(get_search_query())), $link);
        }

        // Post Type Arg.
        if (isset($_GET['post_type'])) {
            $link = add_query_arg('post_type', wc_clean(wp_unslash($_GET['post_type'])), $link);

            // Prevent post type and page id when pretty permalinks are disabled.
            if (is_shop()) {
                $link = remove_query_arg('page_id', $link);
            }
        }

        // Min Rating Arg.
        if (isset($_GET['rating_filter'])) {
            $link = add_query_arg('rating_filter', wc_clean(wp_unslash($_GET['rating_filter'])), $link);
        }

        // All current filters.
        if ($_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes()) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found, WordPress.CodeAnalysis.AssignmentInCondition.Found
            foreach ($_chosen_attributes as $name => $data) {
                $filter_name = wc_attribute_taxonomy_slug($name);
                if (!empty($data['terms'])) {
                    $link = add_query_arg('filter_' . $filter_name, implode(',', $data['terms']), $link);
                }
                if ('or' === $data['query_type']) {
                    $link = add_query_arg('query_type_' . $filter_name, 'or', $link);
                }
            }
        }

        return apply_filters('woocommerce_widget_get_current_page_url', $link, $this);
    }

}

add_action('widgets_init', 'create_filterproduct_widget');
function create_filterproduct_widget()
{
    register_widget('FilterProduct');
}