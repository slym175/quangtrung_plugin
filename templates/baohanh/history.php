<?php
/**
 * Created by trungduc.vnu@gmail.com.
 */
$code = $_REQUEST['code'];
if (isset($code) && $code) {
    global $wpdb;
    $table = $wpdb->prefix . 'baohanh';
    $sql = "SELECT * FROM {$table} WHERE `bh_code` = '$code'";
    $data = $wpdb->get_row($wpdb->prepare($sql), ARRAY_A);
    if (isset($data) && $data) {
        $table_item = $wpdb->prefix . 'baohanh_items';
        $sql_item = "SELECT * FROM {$table_item} WHERE `bh_code` = '$code'";
        $items = $wpdb->get_results($sql_item, ARRAY_A);
    }
}
?>
<?php if (isset($data) && $data): ?>
    <div class="wrap" id="print_bh">
        <h1 class="wp-heading-inline">Lịch sử bảo hành</h1>
        <a href="<?= admin_url() ?>admin.php?page=baohanh&action=create&code=<?= $code ?>" class="page-title-action">Thêm
            mới</a>
        <div style="margin-top: 5px"><h4>Mã bảo hành: <strong style="color: green"><?= $code ?></strong></h4></div>
        <div id="poststuff">
            <div id="post-body-content">

                <table class="wp-list-table widefat fixed striped table-view-list baohanh">
                    <thead>
                    <tr>
                        <th scope="col" id="code" class="manage-column column-code column-primary">ID</th>
                        <th scope="col" id="code" class="manage-column column-code column-primary">Mã đơn hàng</th>
                        <th scope="col" id="order_id" class="manage-column column-order_id">Sản phẩm</th>
                        <th scope="col" id="phone" class="manage-column column-phone sortable asc">Ngày bảo hành
                        </th>
                        <th scope="col" id="customer_name" class="manage-column column-customer_name">Ngày trả
                            hàng
                        </th>
                    </tr>
                    </thead>

                    <tbody id="the-list" data-wp-lists="list:baohanh">
                    <?php if (isset($items) && $items): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <?= $item['id'] ?>
                                    <div class="row-actions print-actions">
                                        <span class="edit">
                                            <a href="<?= admin_url() ?>admin.php?page=baohanh&action=edit&id=<?= $item['id'] ?>">Chỉnh sửa</a>
                                            | </span>
                                        <span class="print">
                                            <a href="javascript:void(0)" onclick="printDiv(this)"
                                               data-url="<?= admin_url('admin-ajax.php') ?>"
                                               data-id="<?= $item['id'] ?>">
                                                <i class="fa fa-print"></i>In phiếu biên nhận
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                <td>#<?= $data['order_id'] ?></td>
                                <td><?= $data['product_id'] ?></td>
                                <td><?= date('d-m-Y', $item['created_at']) ?></td>
                                <td><?= date('d-m-Y', $item['time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="no-items">
                            <td class="colspanchange" colspan="4">Không tìm thấy thông tin lịch sử bảo hành.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="wrap" style="text-align: center">
        <h1 class="wp-heading-inline">Trang không tồn tại</h1>
    </div>
<?php endif; ?>
