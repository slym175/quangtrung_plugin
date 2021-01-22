<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

class XemNhieuWidget extends WP_Widget
{

    function __construct()
    {
        parent::__construct(
            'xem_nhieu',
            'Bài viết, video xem nhiều'
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

        $post_most_viewed = new WP_Query(array(
            'post_type' => $post_type,
            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'posts_per_page' => 5,
        ));
        ?>
        <?php if ($post_most_viewed->have_posts()): ?>
        <div class="title-style-noicon title-icon ">
            <h2 style="text-transform: uppercase;"><?= $attributes[$post_type] ?> xem nhiều</h2>
        </div>
        <?php if ($post_type == 'video'): ?>
            <div class="video-page-12 video-page-12-style-2">
                <?php while ($post_most_viewed->have_posts()) : $post_most_viewed->the_post() ?>
                    <div class="item_video" onclick="setView(this)" data-url="<?= admin_url('admin-ajax.php') ?>"
                         data-id="<?= get_the_ID() ?>">
                        <div class="img">
                            <img src="<?= get_the_post_thumbnail_url('', array(150, 150)) ?>">
                            <?php $iframe = get_post_meta(get_the_ID(), 'iframe', true) ?>
                            <div class="play_run">
                                <a data-fancybox="gallery" href="<?= $iframe ?>">
                                    <span>
                                        <div class="ping ring">
                                        </div>
                                        <img src="<?= THEME_URL_URI . '/assets/images/playtsmall.png' ?>">
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div class="box-content box-content-video text-justify">
                            <h5>
                                <a href="<?= $iframe ?>" data-fancybox="gallery" title="<?= the_title() ?>"
                                   class="popup-video"><?= the_title() ?></a></h5>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="post-style-3 wow fadeInUp" data-wow-delay="200ms">
                <?php while ($post_most_viewed->have_posts()) : $post_most_viewed->the_post() ?>
                    <div class="box-post">
                        <div class="img-box">
                            <a href="<?= the_permalink() ?>"> <img src="<?= get_the_post_thumbnail_url('', array(150, 150)) ?>"></a>
                        </div>
                        <div class="title-post">
                            <h5><a href="<?= the_permalink() ?>"><?= the_title() ?></a></h5>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
        <?php

    }

}

add_action('widgets_init', 'create_xem_nhieu_widget');
function create_xem_nhieu_widget()
{
    register_widget('XemNhieuWidget');
}