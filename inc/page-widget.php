<?php

class ListPage extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'list_page',
            'List Page',
            array('description' => 'Widget hiển thị danh sách page')
        );
    }

    function form($instance)
    {

        $default = array(
            'title' => 'Tiêu đề widget',
            'page_ids' => ''
        );
        $instance = wp_parse_args((array)$instance, $default);
        $title = esc_attr($instance['title']);
        $post_number = esc_attr($instance['page_ids']);

        echo '<p>Nhập tiêu đề <input type="text" class="widefat" name="' . $this->get_field_name('title') . '" value="' . $title . '"/></p>';
        echo '<p>Danh sách ID của page <input type="text" class="widefat" name="' . $this->get_field_name('page_ids') . '" value="' . $post_number . '" placeholder="' . $post_number . '" /><small>ID của trang được ngăn cách bởi dấu phẩy. Ví dụ: 123,345</small></p>';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['page_ids'] = strip_tags($new_instance['page_ids']);
        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $post_number = $instance['page_ids'];

        $arr = [
            'post_type' => 'page',
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
            <?php $cla_active =  ($actual_link == get_permalink()) ? 'activep' : ''; ?>
                <p class="default <?=$cla_active ?>"><i class="fas fa-chevron-right" aria-hidden="true"></i><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>

            <?php endwhile;
        endif;

    }

}

add_action('widgets_init', 'create_randompost_widget');
function create_randompost_widget()
{
    register_widget('ListPage');
}