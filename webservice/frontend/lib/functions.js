var $ = jQuery;

jQuery(document).on('click', '.reload-user-data', function () {
    jQuery(this).addClass('fa-spin');
    var purchased_code = jQuery(this).closest('tr').data('id');
    var site_url = jQuery(this).closest('tr').data('site');
    var item_id = jQuery(this).closest('tr').data('item');
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: '../config.php',
        data: "action=get_user_data&item_purchase_code=" + purchased_code + "&item_id=" + item_id + "&site_url=" + site_url,
        success: function (response) {
            location.reload();
        }
    });
});

function updateStatus(theme_status, purchased_code, site_url){
	jQuery.ajax({
        type: "POST",
        url: '../config.php',
        data: "action=update_theme_status&theme_puchase_code=" + purchased_code + "&status="+theme_status+"&site_url=" + site_url,
        success: function (response) {
            location.reload();
        }
    });
}