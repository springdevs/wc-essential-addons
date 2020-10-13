<?php

namespace springdevs\WooAdvanceCoupon\Frontend\Helpers;

/**
 * Coupon Apply class
 */
class Apply
{
    /**
     * Apply Discount
     * $coupon is sdwac_coupon
     **/
    public function apply_discount($coupon)
    {
        $cart = WC()->cart;
        $sdwac_coupon_main = get_post_meta($coupon, "sdwac_coupon_main", true);
        if (!$sdwac_coupon_main) {
            return false;
        }
        $sdwac_coupon_discounts = get_post_meta($coupon, "sdwac_coupon_discounts", true);
        $sdwac_coupon_coupon_type = $sdwac_coupon_main["type"];
        $discount_amount = 0;
        $discount_label = get_option("sdwac_first_time_purchase_coupon_label");
        if ($sdwac_coupon_coupon_type != "product") {
            if (isset($sdwac_coupon_main["label"]) || !empty($sdwac_coupon_main["label"] || !$sdwac_coupon_main["label"] == '')) {
                $discount_label = $sdwac_coupon_main["label"];
            }
        }
        if ($sdwac_coupon_coupon_type == "cart") {
            switch ($sdwac_coupon_discounts["type"]) {
                case 'percentage':
                    $discount_total = ($sdwac_coupon_discounts["value"] / 100) * $cart->subtotal;
                    break;
                case 'fixed':
                    $discount_total = $sdwac_coupon_discounts["value"];
                    break;
            }
            $discount_amount += $discount_total;
        } else if ($sdwac_coupon_coupon_type == "product") {
            $first_coupon          = get_option("sdwac_first_time_purchase_coupon");
            $sdwac_coupon_first_coupon_main = false;
            if ($first_coupon != 0) {
                $sdwac_coupon_first_coupon_main = get_post_meta($first_coupon, "sdwac_coupon_main", true);
                if ($sdwac_coupon_first_coupon_main) {
                    if ($sdwac_coupon_first_coupon_main["type"] == "product") {
                        $sdwac_coupon_first_coupon_main = true;
                    } else {
                        $sdwac_coupon_first_coupon_main = false;
                    }
                } else {
                    update_option("sdwac_first_time_purchase_coupon", 0);
                }
            }
            if ($sdwac_coupon_first_coupon_main) {
                WC()->session->set("sdwac_product_coupon", [
                    "first_coupon" => "yes"
                ]);
            } else {
                $items = [];
                array_push($items, $coupon);
                WC()->session->set("sdwac_product_coupon", [
                    "first_coupon" => "no",
                    "items" => $items
                ]);
            }
            return false;
        } else if ($sdwac_coupon_coupon_type == "bulk") {
            foreach ($sdwac_coupon_discounts as $sdwac_coupon_discount) {
                if ($sdwac_coupon_discount["min"] <= $cart->subtotal && $sdwac_coupon_discount["max"] >= $cart->subtotal) {
                    switch ($sdwac_coupon_discount["type"]) {
                        case 'percentage':
                            $discount_total = ($sdwac_coupon_discount["value"] / 100) * $cart->subtotal;
                            break;
                        case 'fixed':
                            $discount_total = $sdwac_coupon_discount["value"];
                            break;
                    }
                    $discount_amount += $discount_total;
                }
            }
        }
        return [
            "label" => $discount_label,
            "amount" => $discount_amount
        ];
    }
}
