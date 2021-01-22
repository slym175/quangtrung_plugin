<?php

class FilterPriceProduct extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'filter_price_product',
            'Filter Price Product',
            array('description' => 'Widget lọc giá sản phẩm')
        );
    }

    function form($instance)
    {

        $default = array(
            'title' => 'Tiêu đề widget',
        );
        $instance = wp_parse_args((array)$instance, $default);
        $title = esc_attr($instance['title']);

        echo '<p>Nhập tiêu đề </p><input type="text" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $title . '"/>';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

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

            $base_link = $this->get_current_page_url(); // Link hiện tại của url
            $filter_min_price = 'min_price';
            $filter_max_price = 'max_price';

            $attributes = [
                [
                    'name' => 'Dưới 1 triệu',
                    'url' => $this->get_link_price(0, 1000000, $base_link),
                    'min_price' => 0,
                    'max_price' => 1000000
                ],
                [
                    'name' => 'Từ 1 - 2 triệu',
                    'url' => $this->get_link_price(1000000, 2000000, $base_link),
                    'min_price' => 1000000,
                    'max_price' => 2000000
                ],
                [
                    'name' => 'Từ 2 - 5 triệu',
                    'url' => $this->get_link_price(2000000, 5000000, $base_link),
                    'min_price' => 2000000,
                    'max_price' => 5000000
                ],
                [
                    'name' => 'Từ 5 - 7 triệu',
                    'url' => $this->get_link_price(5000000, 7000000, $base_link),
                    'min_price' => 5000000,
                    'max_price' => 7000000
                ],
                [
                    'name' => 'Từ 7-10 triệu',
                    'url' => $this->get_link_price(7000000, 10000000, $base_link),
                    'min_price' => 7000000,
                    'max_price' => 10000000
                ],
                [
                    'name' => 'Trên 10 triệu',
                    'url' => $this->get_link_price(10000000, '', $base_link),
                    'min_price' => 10000000,
                    'max_price' => ''
                ],
            ];

            echo '<div class="check-box-item">';
            foreach ($attributes as $key => $term): ?>
                <?php
                $checked = (isset($_GET[$filter_min_price]) && $_GET[$filter_min_price] == $term['min_price'] && isset($_GET[$filter_max_price]) && $_GET[$filter_max_price] == $term['max_price']) ? true : false;
                if (isset($_GET[$filter_min_price]) && $_GET[$filter_min_price] == 10000000) {
                    if ($_GET[$filter_min_price] == $term['min_price']) {
                        $checked = true;
                    }
                }
                ?>
                <label class="check-cate">
                    <input type="checkbox" class="product-attribute" id="price<?= $key + 1 ?>"
                           name="price<?= $key + 1 ?>"
                           data-url="<?= $term['url'] ?>" <?= $checked ? 'checked' : '' ?>>
                    <?= $term['name'] ?>
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


        $base_link = $this->get_current_page_url(); // Link hiện tại của url
        $filter_min_price = 'min_price';
        $filter_max_price = 'max_price';

        $attributes = [
            [
                'name' => 'Dưới 1 triệu',
                'url' => $this->get_link_price(0, 1000000, $base_link),
                'min_price' => 0,
                'max_price' => 1000000
            ],
            [
                'name' => 'Từ 1 - 2 triệu',
                'url' => $this->get_link_price(1000000, 2000000, $base_link),
                'min_price' => 1000000,
                'max_price' => 2000000
            ],
            [
                'name' => 'Từ 2 - 5 triệu',
                'url' => $this->get_link_price(2000000, 5000000, $base_link),
                'min_price' => 2000000,
                'max_price' => 5000000
            ],
            [
                'name' => 'Từ 5 - 7 triệu',
                'url' => $this->get_link_price(5000000, 7000000, $base_link),
                'min_price' => 5000000,
                'max_price' => 7000000
            ],
            [
                'name' => 'Từ 7-10 triệu',
                'url' => $this->get_link_price(7000000, 10000000, $base_link),
                'min_price' => 7000000,
                'max_price' => 10000000
            ],
            [
                'name' => 'Trên 10 triệu',
                'url' => $this->get_link_price(10000000, '', $base_link),
                'min_price' => 10000000,
                'max_price' => ''
            ],
        ];

        foreach ($attributes as $key => $term): ?>
            <?php
            $checked = (isset($_GET[$filter_min_price]) && $_GET[$filter_min_price] == $term['min_price'] && isset($_GET[$filter_max_price]) && $_GET[$filter_max_price] == $term['max_price']) ? true : false;
            if (isset($_GET[$filter_min_price]) && $_GET[$filter_min_price] == 10000000) {
                if ($_GET[$filter_min_price] == $term['min_price']) {
                    $checked = true;
                }
            }
            ?>
            <div class="col-6 p-0">
                <div class="c-checkbox-inline">
                    <input type="checkbox" class="d-none product-attribute" id="price<?= $key + 1 ?>"
                           name="price<?= $key + 1 ?>"
                           data-url="<?= $term['url'] ?>" <?= $checked ? 'checked' : '' ?>>
                    <label class="c-checkbox-label" for="price<?= $key + 1 ?>"><?= $term['name'] ?></label>
                </div>
            </div>
        <?php endforeach;
    }

    protected function get_link_price($min, $max, $base_link)
    {
        $filter_min_price = 'min_price';
        $filter_max_price = 'max_price';
        if (isset($_GET[$filter_min_price]) && $_GET[$filter_min_price] == $min) {
            $base_link = remove_query_arg($filter_min_price, $base_link);
        } else {
            $base_link = (add_query_arg($filter_min_price, $min, $base_link));
        }
        if (isset($_GET[$filter_max_price]) && $_GET[$filter_max_price] == $max) {
            $base_link = remove_query_arg($filter_max_price, $base_link);
        }else{
            $base_link = (add_query_arg($filter_max_price, $max, $base_link));
        }

        if($max == ''){
            $base_link = remove_query_arg($filter_max_price, $base_link);
        }
        return $base_link;
    }

    protected function get_current_term_id()
    {
        return absint(is_tax() ? get_queried_object()->term_id : 0);
    }

    protected function get_current_term_slug()
    {
        return absint(is_tax() ? get_queried_object()->slug : 0);
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

add_action('widgets_init', 'create_filter_price_product_widget');
function create_filter_price_product_widget()
{
    register_widget('FilterPriceProduct');
}