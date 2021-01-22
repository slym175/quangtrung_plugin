<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */

add_action('admin_init', 'hhs_add_meta_boxes', 1);
function hhs_add_meta_boxes()
{
    add_meta_box('repeatable-fields-gift', 'Quà tặng', 'qt_repeatable_gift_display', 'product', 'normal', 'default');
    add_meta_box('repeatable-fields-promotional', 'Ưu đãi khác', 'qt_repeatable_promotional_display', 'product', 'normal', 'default');
}

function qt_repeatable_gift_display()
{
    global $post;

    $repeatable_fields = get_post_meta($post->ID, 'gift_fields', true);

    wp_nonce_field('hhs_repeatable_meta_box_nonce', 'hhs_repeatable_meta_box_nonce');
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            add_select2('.gift','mishagetposts','','',3);
            $('#add-row').on('click', function () {
                var row = $('.gift-clone').clone(true);
                row.removeClass('empty-row screen-reader-text gift-clone');
                row.addClass('row-active');
                row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
                jQuery('.row-active').find('.gift_clone').addClass('gift');
                add_select2('.gift','mishagetposts','','',3);
                return false;
            });

            $('.remove-row').on('click', function () {
                $(this).parents('tr').remove();
                return false;
            });
        });
    </script>

    <table id="repeatable-fieldset-one" width="100%">
        <thead>
        <tr>
            <th width="50%">Sản phẩm</th>
            <th width="20%">Ngày bắt đầu</th>
            <th width="20%">Ngày kết thúc</th>
            <th width="10%"></th>
        </tr>
        </thead>
        <tbody>
        <?php

        if ($repeatable_fields) :
            foreach ($repeatable_fields as $field) {
                $title = get_the_title( $field['gift'] );
                $title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
                ?>
                <tr class="row-active">
                    <td class="gift_field">
                        <select class="gift" name="gift[]" multiple="multiple" style="width:100%;">
                                <option value="<?= $field['gift'] ?>" selected="selected"><?= $title ?></option>
                        </select>
                    </td>

                    <td>
                        <input type="date" class="widefat" name="start_date[]"
                               value="<?= (isset($field['start_date']) && $field['start_date']) ? $field['start_date'] : '' ?>"/>
                    </td>

                    <td>
                        <input type="date" class="widefat" name="end_date[]"
                               value="<?= (isset($field['end_date']) && $field['end_date']) ? $field['end_date'] : '' ?>"/>
                    </td>

                    <td><a class="button remove-row" href="#">Xóa</a></td>
                </tr>
                <?php
            }
        else :
            // show a blank one
            ?>
            <tr class="row-active">
                <td class="gift_field">
                    <select class="gift" name="gift[]" multiple="multiple" style="width:100%;"></select>
                </td>

                <td>
                    <input type="date" class="widefat" name="start_date[]" value="<?= date('Y-m-d',time()) ?>">
                </td>

                <td>
                    <input type="date" class="widefat" name="end_date[]"/>
                </td>

                <td><a class="button remove-row" href="#">Xóa</a></td>
            </tr>
        <?php endif; ?>

        <!-- empty hidden one for jQuery -->
        <tr class="empty-row screen-reader-text gift-clone">
            <td class="gift_field">
                <select class="gift_clone" name="gift[]" multiple="multiple" style="width:100%;"></select>
            </td>

            <td>
                <input type="date" class="widefat" name="start_date[]" value="<?= date('Y-m-d',time()) ?>">
            </td>

            <td>
                <input type="date" class="widefat" name="end_date[]"/>
            </td>

            <td><a class="button remove-row" href="#">Xóa</a></td>
        </tr>
        </tbody>
    </table>

    <p><a id="add-row" class="button" href="#">Thêm quà tặng</a></p>
    <style>
        .gift_field .select2-selection {
            height: 30px;
        }

        .gift_field .select2-container .select2-search--inline .select2-search__field {
            margin-top: 0px !important;
            padding: 0 !important;
        }

        .gift_field .select2-container--default .select2-selection--multiple .select2-selection__rendered li {
            margin: 3px 5px 0 0 !important;
        }
        .promotional_fields input{
            width: 100%;
        }
    </style>
    <?php
}

add_action('save_post', 'hhs_repeatable_meta_box_save');
function hhs_repeatable_meta_box_save($post_id)
{
    if (!isset($_POST['hhs_repeatable_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['hhs_repeatable_meta_box_nonce'], 'hhs_repeatable_meta_box_nonce'))
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'gift_fields', true);
    $new = array();

    $gift = $_POST['gift'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $count = count($gift);

    for ($i = 0; $i < $count; $i++) {
        if ($gift[$i] != '') :
            $new[$i]['gift'] = stripslashes(strip_tags($gift[$i]));
            $new[$i]['start_date'] = stripslashes(strip_tags($start_date[$i]));
            $new[$i]['end_date'] = stripslashes(strip_tags($end_date[$i]));
        endif;
    }

    if (!empty($new) && $new != $old)
        update_post_meta($post_id, 'gift_fields', $new);
    elseif (empty($new) && $old)
        delete_post_meta($post_id, 'gift_fields', $old);
}

add_action('wp_ajax_mishagetposts', 'rudr_get_posts_ajax_callback');
function rudr_get_posts_ajax_callback()
{
    $s = $_GET['q'];
    $return = array();
    global $wpdb;
    $search_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type='gift' AND post_title LIKE '%s' limit 10", '%' . $wpdb->esc_like($s) . '%'));

    if ($search_results) {
        foreach ($search_results as $result) {
            $title = (mb_strlen($result->post_title) > 50) ? mb_substr($result->post_title, 0, 49) . '...' : $result->post_title;
            $return[] = array($result->ID, $title);
        }
    }
    echo json_encode($return);
    die;
}

?>


<?php
function qt_repeatable_promotional_display()
{
    global $post;

    $promotional_repeatable_fields = get_post_meta($post->ID, 'promotional_fields', true);

    wp_nonce_field('qt_repeatable_meta_box_nonce', 'qt_repeatable_meta_box_nonce');
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('#add-row-pro').on('click', function () {
                var row = $('.promotional-clone').clone(true);
                row.removeClass('empty-row screen-reader-text promotional-clone');
                row.insertBefore('#repeatable-fieldset-one-pro tbody>tr:last');
                return false;
            });

            $('.remove-row-pro').on('click', function () {
                $(this).parents('tr').remove();
                return false;
            });
        });
    </script>

    <table id="repeatable-fieldset-one-pro" width="100%">
        <thead>
        <tr>
            <th width="60%">Tiêu đề</th>
            <th width="30%">Đường dẫn</th>
            <th width="10%"></th>
        </tr>
        </thead>
        <tbody>
        <?php

        if ($promotional_repeatable_fields) :
            foreach ($promotional_repeatable_fields as $field) {?>
                <tr class="row-active">
                    <td class="promotional_fields">
                        <input type="text" name="title[]" id="" value="<?= $field['title'] ?>" />
                    </td>

                    <td>
                        <input type="text" class="widefat" name="link[]"
                               value="<?= (isset($field['link']) && $field['link']) ? $field['link'] : '' ?>"/>
                    </td>

                    <td><a class="button remove-row-pro" href="#">Xóa</a></td>
                </tr>
                <?php
            }
        else :
            // show a blank one
            ?>
            <tr class="row-active">
                <td class="promotional_fields">
                    <input type="text" name="title[]" id="" />
                </td>

                <td>
                    <input type="text" class="widefat" name="link[]">
                </td>

                <td><a class="button remove-row-pro" href="#">Xóa</a></td>
            </tr>
        <?php endif; ?>

        <!-- empty hidden one for jQuery -->
        <tr class="empty-row screen-reader-text promotional-clone">
            <td class="promotional_fields">
                <input type="text" name="title[]" id="" />
            </td>

            <td>
                <input type="text" class="widefat" name="link[]">
            </td>

            <td><a class="button remove-row-pro" href="#">Xóa</a></td>
        </tr>
        </tbody>
    </table>

    <p><a id="add-row-pro" class="button" href="#">Thêm ưu đãi</a></p>
    <?php
}

add_action('save_post', 'qt_repeatable_promotional_save');
function qt_repeatable_promotional_save($post_id)
{
    if (!isset($_POST['qt_repeatable_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['qt_repeatable_meta_box_nonce'], 'qt_repeatable_meta_box_nonce'))
        return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!current_user_can('edit_post', $post_id))
        return;

    $old = get_post_meta($post_id, 'promotional_fields', true);
    $new = array();

    $title = $_POST['title'];
    $link = $_POST['link'];

    $count = count($title);

    for ($i = 0; $i < $count; $i++) {
        if ($title[$i] != '') :
            $new[$i]['title'] = stripslashes(strip_tags($title[$i]));
            $new[$i]['link'] = stripslashes(strip_tags($link[$i]));
        endif;
    }

    if (!empty($new) && $new != $old)
        update_post_meta($post_id, 'promotional_fields', $new);
    elseif (empty($new) && $old)
        delete_post_meta($post_id, 'promotional_fields', $old);
}
?>