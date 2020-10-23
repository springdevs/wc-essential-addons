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

    jQuery(document).on("woocommerce_variations_loaded", () => {
        let total_variations = JSON.parse(jQuery(".woocommerce_variations").attr("data-total"));
        for (let index = 0; index < total_variations; index++) {
            let element = document.getElementById("subscrpt_enable[" + index + "]");
            if (element && element.checked) {
                jQuery("div#show_if_subscription_" + index).show();
            } else {
                jQuery("div#show_if_subscription_" + index).hide();
            }
        }
    });
});

function hellochange(index) {
    if (document.getElementById("subscrpt_enable[" + index + "]").checked) {
        jQuery("div#show_if_subscription_" + index).show();
    } else {
        jQuery("div#show_if_subscription_" + index).hide();
    }
}