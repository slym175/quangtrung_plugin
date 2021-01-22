<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */
$claGeneral = new ClaGeneral();
$code = $_REQUEST['code'];
if (isset($code) && $code) {
    global $wpdb;
    $table = $wpdb->prefix . 'baohanh';
    $sql = "SELECT * FROM {$table} WHERE `bh_code` = '$code'";
    $data = $wpdb->get_row($wpdb->prepare($sql), ARRAY_A);
}
?>

<?php if (isset($data) && $data):
    $product = wc_get_product( $data['product_id'] );
    $time = DateTime::createFromFormat('d-m-Y', display_time_expire_warrranty($data['created_at'], $data['time_bh'], $data['type_time']));
    $status = 0;
    if ($time !== false && $time->getTimestamp() >= time()) {
        $status = 1;
    }

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Thông tin bảo hành sản phẩm</h1>
        <div id="poststuff">
            <div id="post-body-content" style="width: 60%">
                <table class="form-table" role="presentation">
                    <tbody>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Mã bảo hành</label>
                        </th>
                        <td>
                            <span><?= $code ?></span>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Mã đơn hàng</label>
                        </th>
                        <td>
                            <span><?= $data['order_id'] ?></span>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Sản phẩm</label>
                        </th>
                        <td>
                            <a href="<?= get_permalink( $data['product_id'] ) ?>" target="_blank"><?= $product->get_name() ? $product->get_name() : 'Missing data' ?></a>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Tên khách hàng</label>
                        </th>
                        <td>
                            <span><?= $data['customer_name'] ?></span>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Số điện thoại</label>
                        </th>
                        <td>
                            <a href="tel:<?= $data['phone'] ?>"><?= $data['phone'] ?></a>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Thời gian bảo hành</label>
                        </th>
                        <td>
                            <span><?= $data['time_bh'].' '.get_type_time_bh($data['type_time'])?></span>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Ngày kích hoạt bảo hành</label>
                        </th>
                        <td>
                            <span><?= date('d-m-Y',$data['created_at']) ?></span>
                        </td>
                    </tr>
                    <tr class="form-field form-required term-name-wrap">
                        <th scope="row">
                            <label for="name">Trạng thái</label>
                        </th>
                        <td>
                            <span class="stt-<?= $status ?>"><?= ($status == 1) ? 'Kích hoạt' : 'Hết hạn' ?></span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br class="clear">
        </div>
    </div>
    <style>
        .stt-0{
            color: red;
            font-weight: bold;
        }
        .stt-1{
            color: green;
            font-weight: bold;
        }
    </style>
<?php else: ?>
    <div class="wrap" style="text-align: center">
        <h1 class="wp-heading-inline">Trang không tồn tại</h1>
    </div>
<?php endif; ?>
