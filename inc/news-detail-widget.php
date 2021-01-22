<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

class NewsDetailWidget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'news_detail_category',
            'Bài viết, video liên quan',
            array('description' => 'Danh sách bài viết, video liên quan')
        );
    }

    function form($instance)
    {
        $default = array(
            'title' => '',
            'post_type' => ''
        );
        $instance = wp_parse_args((array)$instance, $default);
        $post_type = esc_attr($instance['post_type']);
        $attributes = [
            'post' => 'Bài viết',
            'video' => 'Video'
        ];

        echo '<p>Chọn loại tin</p><select name="' . $this->get_field_name('post_type') . '" id="" class="form-control">';
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
        if (is_single()) {
            global $post;
            $category_ids = wp_get_post_categories($post->ID);
            $related = get_posts(array('category__in' => $category_ids, 'numberposts' => 5, 'post__not_in' => array($post->ID)));
        }
        ?>
        <div class="title-style-noicon title-icon ">
            <h2>TIN TỨC LIÊN QUAN</h2>
        </div>
        <?php
        if ($related) foreach ($related as $post) {
            setup_postdata($post); ?>
            <div class="post-style-3 wow fadeInUp" data-wow-delay="200ms">
                <div class="box-post">
                    <div class="img-box">
                        <a href="<?= the_permalink() ?>"> <img src="<?= get_the_post_thumbnail_url('',array(150,150)) ?>"></a>
                    </div>
                    <div class="title-post">
                        <h5><a href="<?= the_permalink() ?>"><?= the_title() ?></a></h5>
                    </div>
                </div>
            </div>
        <?php } wp_reset_query(); ?>
        <?php

    }

}

add_action('widgets_init', 'create_new_detail_widget');
function create_new_detail_widget()
{
    register_widget('NewsDetailWidget');
}