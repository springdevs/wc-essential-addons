jQuery(document).ready(function ($) {
    $('a[href="admin.php?page=springdevs-freemius"]').attr('style', 'color: #33ff00;');
    $('.sdevs-loading-icon').hide();

    $('#sdevs-install-plugin').click(() => {
        install_woocommerce_plugin();
    });

    $('#sdevs-activate-plugin').click(() => {
        activate_woocommerce_plugin();
    });

    function install_woocommerce_plugin() {
        $.ajax({
            type: 'POST',
            url: sdmaw_helper_obj.ajax_url,
            data: { install_plugin: 'woocommerce', action: 'install_woocommerce_plugin' },
            beforeSend: function () {
                $('.sdevs-loading-icon').show();
            },
            success: function (data) {
                activate_woocommerce_plugin();
                // $('#sdevs-install-plugin').remove();
                // $('#tutor_install_msg').html(data);
            },
            complete: function () {
                $('.sdevs-loading-icon').hide();
            }
        });
    }

    function activate_woocommerce_plugin() {
        $.ajax({
            type: 'POST',
            url: sdmaw_helper_obj.ajax_url,
            data: { activate_plugin: 'woocommerce', action: 'activate_woocommerce_plugin' },
            beforeSend: function () {
                $('.sdevs-loading-icon').show();
            },
            success: function (data) {
                window.location.href = '';
                // $('#sdevs-install-plugin').remove();
                // $('#tutor_install_msg').html(data);
            },
            complete: function () {
                $('.sdevs-loading-icon').hide();
            }
        });
    }
});