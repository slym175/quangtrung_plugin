<?php

class ListNews extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'list_news',
            'List News',
            array('description' => 'Widget hiển thị danh sách tin tức')
        );
    }

    function form($instance)
    {

        $default = array(
            'title' => 'Tiêu đề widget',
            'new_ids' => ''
        );
        $instance = wp_parse_args((array)$instance, $default);
        $title = esc_attr($instance['title']);
        $post_number = esc_attr($instance['new_ids']);

        echo '<p>Nhập tiêu đề <input type="text" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $title . '"/></p>';
        echo '<p>Danh sách ID của tin tức <input type="text" class="widefat" name="' . $this->get_field_name('new_ids') . '" value="' . $post_number . '" placeholder="' . $post_number . '" /><small>ID của trang được ngăn cách bởi dấu phẩy. Ví dụ: 123,345</small></p>';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['new_ids'] = strip_tags($new_instance['new_ids']);
        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $post_number = $instance['new_ids'];

        $arr = [
            'post_type' => 'post',
            'post__in' => explode(',',$post_number),
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ];

        $pages = new WP_Query($arr);
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


        if ($pages->have_posts()):
            while ($pages->have_posts()) :
                $pages->the_post();
        ?>
                <a href="<?= the_permalink() ?>">
                    <img src="<?= get_the_post_thumbnail_url() ?>" class="img-fluid" alt="<?= the_title() ?>">
                    <p class="mt-2 text-dark font-14"><?= the_title() ?></p>
                </a>

            <?php endwhile;
        endif;

    }

}

add_action('widgets_init', 'create_post_widget');
function create_post_widget()
{
    register_widget('ListNews');
}