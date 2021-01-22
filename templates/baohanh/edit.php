<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */
$id = $_REQUEST['id'];
global $wpdb;

if (isset($id) && $id) {
    $table = $wpdb->prefix . 'baohanh_items';
    $sql = "SELECT * FROM {$table} WHERE `id` = $id";
    $data = $wpdb->get_row($wpdb->prepare($sql), ARRAY_A);
}
$allowUpload = true;
$img_upload = false;
if ($_POST) {
    if (isset($_FILES['attachment']['error']) && $_FILES['attachment']['error'] == 0) {
        $file = $_FILES['attachment'];
        $target_dir = get_home_path() . "/wp-content/uploads/baohanh/";
        $target_file = $target_dir . basename($_FILES["attachment"]["name"]);

        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        $allowtypes = array('jpg', 'png', 'jpeg');

        if (!in_array($imageFileType, $allowtypes)) {
            echo "Chỉ được upload các định dạng JPG, PNG, JPEG, GIF";
            $allowUpload = false;
        }

        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $img_upload = "/wp-content/uploads/baohanh/" . $_FILES["attachment"]["name"];
        } else {
            echo "Not uploaded because of error #".$_FILES["attachment"]["error"];
            $allowUpload = false;
        }
    }

    if ($allowUpload) {
        $args = array(
            'status' => $_POST['status'],
            'tem' => $_POST['tem'],
            'description' => $_POST['description'],
            'time' => dateTimeToTimestamp($_POST['time']),
            'created_at' => time(),
        );
        if ($img_upload) {
            $args['attachment'] = $img_upload;
        }
        global $wpdb;
        $table = $wpdb->prefix . 'baohanh_items';
        $wpdb->update(
            $table,
            $args,
            array('id' => $id)
        );
        wp_redirect(admin_url('/admin.php?page=baohanh&action=history&code=' . $data['bh_code']));
    }
}
?>
<?php if (isset($data) && $data): ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Chỉnh sửa lịch sử bảo hành</h1>
        <div id="poststuff">
            <div id="post-body-content" style="width: 60%">
                <form method="post" id="create-submit" enctype="multipart/form-data">
                    <div class="bh-field">
                        <div class="tk-label">
                            <label for="field_status">Tình trạng máy</label>
                        </div>
                        <div class="tk-input">
                            <input type="text" id="field_status" name="status" value="<?= $data['status'] ?>">
                            <p class="error"></p>
                        </div>
                    </div>
                    <div class="bh-field">
                        <div class="tk-label">
                            <label for="field_tem">Tình trạng tem bảo hành</label>
                        </div>
                        <div class="tk-input">
                            <input type="text" id="field_tem" name="tem" value="<?= $data['tem'] ?>">
                            <p class="error"></p>
                        </div>
                    </div>
                    <div class="bh-field">
                        <div class="tk-label">
                            <label for="field_description">Mô tả</label>
                        </div>
                        <div class="tk-input">
                            <textarea id="field_description" name="description"
                                      rows="6"><?= ($data['description']) ? $data['description'] : '' ?></textarea>
                        </div>
                    </div>
                    <div class="bh-field">
                        <div class="tk-label">
                            <label for="field_time">Thời gian trả hàng</label>
                        </div>
                        <div class="tk-input">
                            <input name="time" id="field_time" type="date" value="<?= date('Y-m-d', $data['time']) ?>">
                            <p class="error"></p>
                        </div>
                    </div>
                    <div class="bh-field">
                        <div class="tk-label">
                            <label for="field_description">Ảnh xác thực</label>
                        </div>
                        <div class="tk-input">
                            <input name="attachment" type="file">
                        </div>
                        <?php if ($data['attachment']): ?>
                            <div class="tk-attachment">
                                <img src="<?= $data['attachment'] ?>" alt="">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-success" onclick="onSubmit()">Xác nhận</button>
                </form>
            </div>
        </div>
    </div>
    <style>
        .tk-attachment {
            width: 150px;
        }

        .tk-attachment img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .btn-success {
            color: #fff;
            background-color: #5cb85c;
            border: 1px solid #5cb85c;
            border-radius: 4px;
            padding: 10px;
        }

        .error {
            color: red;
            margin: unset;
        }

        .bh-field {
            padding: 5px 0px;
        }

        .tk-label {
            vertical-align: top;
            margin: 0 0 10px;
        }

        .tk-label label {
            display: block;
            font-weight: bold;
            margin: 0 0 3px;
            padding: 0;
        }

        .tk-input input[type="text"], .tk-input textarea, .tk-input input[type="date"] {
            width: 100%;
            padding: 4px 8px;
            margin: 0;
            box-sizing: border-box;
            font-size: 14px;
            line-height: 1.4;
        }
    </style>
    <script>
        function validate() {
            var status = jQuery('#field_status').val();
            var field_tem = jQuery('#field_tem').val();
            var field_time = jQuery('#field_time').val();
            var flag = true;
            if (status == '') {
                jQuery('#field_status').parent().find('.error').text('Tình trạng máy không được để trống');
                flag = false;
            } else {
                jQuery('#field_status').parent().find('.error').text('');
            }

            if (field_tem == '') {
                jQuery('#field_tem').parent().find('.error').text('Tem bảo hành không được để trống');
                flag = false;
            } else {
                jQuery('#field_tem').parent().find('.error').text('');
            }

            if (field_time == '') {
                jQuery('#field_time').parent().find('.error').text('Thời gian trả hàng không được để trống');
                flag = false;
            } else {
                jQuery('#field_time').parent().find('.error').text('');
            }
            return flag;
        }

        function onSubmit() {
            event.preventDefault();
            var check = validate();
            if (check) {
                jQuery("#create-submit").submit();
            }
        }
    </script>
<?php else: ?>
    <div class="wrap" style="text-align: center">
        <h1 class="wp-heading-inline">Trang không tồn tại</h1>
    </div>
<?php endif; ?>
