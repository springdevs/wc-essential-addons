jQuery(document).ready(($) => {
    $("#sdwac_coupon_discount_box").hide();
    let type_field = $("select#discount_type");
    let type_field_value = $("select#discount_type").val();

    if (type_field_value == 'sdwac_product_percent' || type_field_value == 'sdwac_product_fixed') {
        $(".sdwac_product_lists_field").show();
        $("#sdwac_coupon_rules_box").hide();
    } else {
        $(".sdwac_product_lists_field").hide();
        $("#sdwac_coupon_rules_box").show();
    }

    if (type_field_value == 'sdwac_bulk') {
        $("#sdwac_coupon_discount_box").show();
        $(".coupon_amount_field").hide();
    } else {
        $("#sdwac_coupon_discount_box").hide();
        $(".coupon_amount_field").show();
    }

    type_field.change(() => {
        type_field_value = type_field.val();
        if (type_field_value == 'sdwac_product_percent' || type_field_value == 'sdwac_product_fixed') {
            $(".sdwac_product_lists_field").show();
            $("#sdwac_coupon_rules_box").hide();
        } else {
            $(".sdwac_product_lists_field").hide();
            $("#sdwac_coupon_rules_box").show();
        }
        if (type_field_value == 'sdwac_bulk') {
            $("#sdwac_coupon_discount_box").show();
            $(".coupon_amount_field").hide();
        } else {
            $("#sdwac_coupon_discount_box").hide();
            $(".coupon_amount_field").show();
        }
    });
});