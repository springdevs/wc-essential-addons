var handler = FS.Checkout.configure({
    plugin_id: '6934',
    plan_id: '11551',
    public_key: 'pk_041cc3b1a6d4418773fc2a50b050a',
    image: 'https://ps.w.org/wc-essential-addons/assets/icon-128x128.png'
});

jQuery(document).ready(function ($) {
    $('#plan-1').on('click', function () {
        getIframe(1);
    });
    $('#plan-2').on('click', function () {
        getIframe(3);
    });
    $('#plan-3').on('click', function () {
        getIframe("unlimited");
    });

    function getIframe(license) {
        handler.open({
            name: 'Missing Addons for WooCommerce',
            licenses: license,
            // You can consume the response for after purchase logic.
            purchaseCompleted: function (response) {
                // The logic here will be executed immediately after the purchase confirmation.                                // alert(response.user.email);
            },
            success: function (response) {
                // The logic here will be executed after the customer closes the checkout, after a successful purchase.                                // alert(response.user.email);
            }
        });
    }
});