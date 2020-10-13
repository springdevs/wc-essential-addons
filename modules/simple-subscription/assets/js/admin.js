jQuery(document).ready(() => {
    let sdevs_enable_subscription = jQuery("input#subscrpt_enable");
    sdevs_enable_subscription.change(() => {
        if (sdevs_enable_subscription.is(":checked")) {
            jQuery(".show_if_subscription").show();
        } else {
            jQuery(".show_if_subscription").hide();
        }
    });
    if (sdevs_enable_subscription.is(":checked")) {
        jQuery(".show_if_subscription").show();
    } else {
        jQuery(".show_if_subscription").hide();
    }
});