<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

class CategoryWidget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'video_category',
            'Chủ đề',
            array('description' => 'Chủ đề tin tức, video')
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
        if ($post_type == 'post') {
            $taxonomy = 'category';
        } elseif ($post_type == 'video') {
            $taxonomy = 'video-category';
        } else {
            $taxonomy = '';
        }
        $categories = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => 0,
            'parent' => 0,
            'orderBy' => 'ASC',
            'order' => 'ID'
        ]);
        if(is_tax('video-category') || is_category()){
            $term_id = get_queried_object()->term_id;
        }
        ?>
        <div class="title-style-noicon title-icon ">
            <h2>CHỦ ĐỀ</h2>
        </div>
        <ul class="list-item wow fadeInUp" data-wow-delay="200ms">
            <?php if ($categories): ?>
                <?php foreach ($categories as $category): ?>
                    <li <?= (isset($term_id) && $term_id && $term_id == $category->term_id) ? 'class="active"' : '' ?>><a href="<?= get_term_link($category->term_id,$category->taxonomy) ?>"> <?= $category->name ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <?php

    }

}

add_action('widgets_init', 'create_category_widget');
function create_category_widget()
{
    register_widget('CategoryWidget');
}