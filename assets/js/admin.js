function add_select2(element,action,key,value, minimumInputLength = 3){
    jQuery(element).select2({
        ajax: {
            url: ajaxurl, // AJAX URL is predefined in WordPress admin
            dataType: 'json',
            delay: 500, // delay in ms while typing when to perform a AJAX search
            data: function (params) {
                return {
                    q: params.term, // search query
                    key:key,
                    value:value,
                    action: action // AJAX action for admin-ajax.php
                };
            },
            processResults: function( data ) {
                var options = [];
                if ( data ) {

                    // data is the array of arrays, and each of them contains ID and the Label of the option
                    jQuery.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
                        options.push( { id: text[0], text: text[1]  } );
                    });

                }
                return {
                    results: options
                };
            },
            cache: true
        },
        minimumInputLength: minimumInputLength // the minimum of symbols to input before perform a search
    });
}

function printDiv(t)
{
    var url = jQuery(t).data('url');
    var id = jQuery(t).data('id');
    // print_selected('poststuff');
    jQuery.ajax({
        type: "post",
        dataType: "html",
        url: url,
        data: {
            action: 'printbh',
            id:id
        },
        beforeSend: function () {
            // Có thể thực hiện công việc load hình ảnh quay quay trước khi đổ dữ liệu ra
        },
        success: function (response) {
            print_selected(response);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('The following error occured: ' + textStatus, errorThrown);
        }
    });
}

function print_selected(html) {
    var newWin=window.open('','Print-Window');

    newWin.document.open();

    newWin.document.write('<html><body onload="window.print()">'+html+'</body></html>');

    newWin.document.close();

    setTimeout(function(){newWin.close();},10);
}

