<?php

if (!get_option("sdwac_first_time_purchase_coupon")) {
    add_option("sdwac_first_time_purchase_coupon", 0);
}

if (!get_option("sdwac_show_product_discount")) {
    add_option("sdwac_show_product_discount", "yes");
}

if (!get_option("sdwac_multi")) {
    add_option("sdwac_multi", "yes");
}

if (!get_option("sdwac_url")) {
    add_option("sdwac_url", "coupon");
}
