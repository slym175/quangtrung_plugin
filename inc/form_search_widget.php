<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

class FormSearchWidget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'form_search',
            'Form Search',
            array('description' => 'Widget tìm kiếm tin tức, video')
        );
    }

    function form($instance)
    {

        $default = array(
            'title' => 'Tìm kiếm',
            'post_type' => ''
        );
        $instance = wp_parse_args((array)$instance, $default);
        $post_type = esc_attr($instance['post_type']);
        $attributes = [
            'post' => 'Bài viết',
            'video' => 'Video'
        ];

        echo '<p>Tìm kiếm theo</p><select name="' . $this->get_field_name('post_type') . '" id="" class="form-control">';
        foreach ($attributes as $key => $attribute) {
            $selected = ($key == $post_type) ? 'selected' : '';
            echo '<option value="' . $key . '" ' . $selected . '>' . $attribute . '</option>';
        }
        echo '</select>';

    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['post_type'] = strip_tags($new_instance['post_type']);
        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args);
        $post_type = $instance['post_type'];
        $attributes = [
            'post' => 'Bài viết',
            'video' => 'Video'
        ];
        ?>
        <div class="search wow fadeInUp" data-wow-delay="200ms">
            <form action="<?= home_url('/') ?>">
                <input type="hidden" name="post_type" value="<?= $post_type ?>">
                <input type="text" name="s" placeholder="Tìm kiếm <?= $attributes[$post_type] ?>...">
                <button type="submit"><span></span></button>
            </form>
        </div>
        <?php

    }

}

add_action('widgets_init', 'create_search_widget');
function create_search_widget()
{
    register_widget('FormSearchWidget');
}