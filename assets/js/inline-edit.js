function changeStatus(el) {
    var id = jQuery(el).data('id')
    var url = jQuery(el).data('url')
    var status = jQuery(el).parent('.quick-edit-section').find('.gitf_status').val()
    console.log(url);

    jQuery.ajax({
        type : "post", //Phương thức truyền post hoặc get
        dataType : "html", //Dạng dữ liệu trả về xml, json, script, or html
        url : url, //Đường dẫn chứa hàm xử lý dữ liệu. Mặc định của WP như vậy
        data : {
            action: "update_list_registration_gift", //Tên action
            id : id, //Biến truyền vào xử lý. $_POST['website']
            status : status
        },
        context: this,
        beforeSend: function(){
            //Làm gì đó trước khi gửi dữ liệu vào xử lý
            jQuery(el).empty().append('<span class="spinner is-active" style="display: inline-block"></span>')
        },
        success: function(response) {
            //Làm gì đó khi dữ liệu đã được xử lý
            if(response) {
                alert(response);
                jQuery(el).empty().append('Sửa')
            }
            else {
                alert('Đã có lỗi xảy ra');
            }
        },
        error: function( jqXHR, textStatus, errorThrown ){
            //Làm gì đó khi có lỗi xảy ra
            console.log( 'The following error occured: ' + textStatus, errorThrown );
        }
    })
}