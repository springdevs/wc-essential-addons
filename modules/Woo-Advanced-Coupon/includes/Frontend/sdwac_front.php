<?php

namespace springdevs\WooAdvanceCoupon\Frontend;

use springdevs\WooAdvanceCoupon\Frontend\Helpers\Validator;
use WC_Coupon;

/**
 * Class sdwac_coupon_front
 * control front coupon system when user manuelly apply coupon
 */
class sdwac_front
{
    public $discount_amount;

    public function __construct()
    {
        // check coupon is valid 
        add_filter("woocommerce_coupon_is_valid", [$this, "sdwac_coupon_woocommerce_coupon_is_valid"], 10, 2);
        // OverWrite Coupon Amount
        add_action('woocommerce_cart_calculate_fees', [$this, "sdwac_coupon_coupon_amount_overwrite"]);
        // display products price with regular price
        add_filter('woocommerce_cart_product_price', [$this, "sdwac_coupon_filter_cart_product_pricing"], 10, 2);
        // woocommerce set product price as product adjustment
        add_filter("woocommerce_product_get_price", [$this, "sdwac_coupon_update_product_price"], 10, 2);
        // woocommerce change product coupon html
        add_filter('woocommerce_cart_totals_coupon_html', [$this, "sdwac_coupon_change_product_coupon_html"], 30, 3);
        // woocommerce discount show or hide
        add_filter('woocommerce_get_price_html', [$this, "sdwac_coupon_product_price_html"], 100, 2);
        // woocommerce grouped products discount
        add_filter('woocommerce_grouped_price_html', [$this, "sdwac_coupon_group_product_discount_html"], 10, 3);
    }

    /**
     * WooCommerce Grouped Product Price HTML
     **/
    public function sdwac_coupon_group_product_discount_html($price, $product, $child_prices)
    {
        $sdwac_coupon_woo_setting_show_product_discount = get_option("sdwac_show_product_discount");
        $child_products = $product->get_children();
        $regular_prices = [];
        foreach ($child_products as $child_product) {
            $child_product = wc_get_product($child_product);
            array_push($regular_prices, $child_product->get_regular_price());
        }
        $min_regular_price = min($regular_prices);
        $max_regular_price = max($regular_prices);
        $min_sale_price = min($child_prices);
        $max_sale_price = max($child_prices);

        $price_html = "<del>" . wc_price($min_regular_price) . " – " . wc_price($max_regular_price) . "</del><br/>" . wc_price($min_sale_price) . " – " . wc_price($max_sale_price);
        if ($sdwac_coupon_woo_setting_show_product_discount == "no" || $min_regular_price == $min_sale_price || $max_regular_price == $max_sale_price) {
            return wc_price($min_sale_price) . " – " . wc_price($max_sale_price);
        } else {
            return $price_html;
        }
        return $price;
    }

    /**
     * woocommerce discount show or hide
     **/
    public function sdwac_coupon_product_price_html($price, $product)
    {
        $sdwac_coupon_woo_setting_show_product_discount = get_option("sdwac_show_product_discount");
        if ($product->is_type('variable')) {
            $min_regular_price = $product->get_variation_price('min');
            $max_regular_price = $product->get_variation_price('max');
            $discount = $product->get_sale_price();
            if ($discount == "") {
                return $price;
            }

            if ($discount == 0) {
                $new_discount = $this->sdwac_coupon_variable_product_discount($product, $min_regular_price, $max_regular_price);
                if ($new_discount != 0) {
                    $min_sale_price = $min_regular_price - $new_discount[0];
                    $max_sale_price = $max_regular_price - $new_discount[1];
                } else {
                    $min_sale_price = $min_regular_price + $discount;
                    $max_sale_price = $max_regular_price + $discount;
                }
            } else {
                $min_sale_price = $min_regular_price + $discount;
                $max_sale_price = $max_regular_price + $discount;
            }

            $price_html = "<del>" . wc_price($min_regular_price) . " – " . wc_price($max_regular_price) . "</del><br/>" . wc_price($min_sale_price) . " – " . wc_price($max_sale_price);

            if ($sdwac_coupon_woo_setting_show_product_discount == "no") {
                return wc_price($min_sale_price) . " – " . wc_price($max_sale_price);
            } else {
                return $price_html;
            }
        } else if ($product->is_type('simple')) {
            if ($sdwac_coupon_woo_setting_show_product_discount == "no") {
                return wc_price($product->get_price());
            } else {
                return $price;
            }
        }
        return $price;
    }

    /**
     * @return discount
     **/
    public function sdwac_coupon_variable_product_discount($product, $min_regular_price, $max_regular_price)
    {
        $data = WC()->session->get("sdwac_coupon_product_coupon");
        if ($data) {
            if ($data["first_coupon"] === "no") {
                foreach ($data["items"] as $woocoupon) {
                    $validate = Validator::check(null, null, $woocoupon);
                    if (!$validate) {
                        return 0;
                    }
                    $sdwac_coupon_main        = get_post_meta($woocoupon, "sdwac_coupon_main", true);
                    $sdwac_coupon_coupon_type = $sdwac_coupon_main["type"];
                    $sdwac_coupon_discounts = get_post_meta($woocoupon, "sdwac_coupon_discounts", true);
                    $sdwac_coupon_filters     = get_post_meta($woocoupon, "sdwac_coupon_filters", true);

                    if ($sdwac_coupon_coupon_type == "product") {
                        $min_discount = 0;
                        $max_discount = 0;
                        foreach ($sdwac_coupon_filters as $sdwac_coupon_filter) {
                            if ($sdwac_coupon_filter["type"] == "products") {
                                foreach ($sdwac_coupon_filter["items"] as $sdwac_couponproducts) {
                                    if ($sdwac_couponproducts["value"] == $product->get_id()) {
                                        switch ($sdwac_coupon_discounts["type"]) {
                                            case 'percentage':
                                                $min_discount = ($sdwac_coupon_discounts["value"] / 100) * (float)$min_regular_price;
                                                $max_discount = ($sdwac_coupon_discounts["value"] / 100) * (float)$max_regular_price;
                                                break;
                                            case 'fixed':
                                                $min_discount = $sdwac_coupon_discounts["value"];
                                                $max_discount = $sdwac_coupon_discounts["value"];
                                                break;
                                        }
                                        return [$min_discount, $max_discount];
                                    }
                                }
                                return 0;
                            } elseif ($sdwac_coupon_filter["type"] == "all_products") {
                                switch ($sdwac_coupon_discounts["type"]) {
                                    case 'percentage':
                                        $min_discount = ($sdwac_coupon_discounts["value"] / 100) * (float)$min_regular_price;
                                        $max_discount = ($sdwac_coupon_discounts["value"] / 100) * (float)$max_regular_price;
                                        break;
                                    case 'fixed':
                                        $min_discount = $sdwac_coupon_discounts["value"];
                                        $max_discount = $sdwac_coupon_discounts["value"];
                                        break;
                                }
                                return [$min_discount, $max_discount];
                            }
                        }
                    }
                }
            }
        }
        return 0;
    }



    /**
     * WooCommerce display cart price with <del>#...</del>
     *
     **/
    public function sdwac_coupon_filter_cart_product_pricing($formatted_price, $product)
    {
        $sdwac_coupon_woo_setting_show_product_discount = get_option("sdwac_show_product_discount");
        if ($sdwac_coupon_woo_setting_show_product_discount == "no") {
            return $formatted_price;
        }
        $_product = wc_get_product($product->get_id());
        if ($formatted_price != wc_price($_product->get_regular_price())) {
            return $formatted_price . '<br /><del>' . wc_price($_product->get_regular_price()) . '</del>';
        } else {
            return $formatted_price;
        }
    }

    /**
     * WooCommerce update cart subtotal
     *
     **/
    public function sdwac_coupon_cart_subtotal($subtotal, $compound, $cart)
    {
        $store_credit = $this->discount_amount;
        if ($store_credit > 0) {
            $cart->set_discount_total($store_credit);
            $cart->set_total($cart->get_subtotal() - $store_credit);
        }
        return $subtotal;
    }

    /**
     * WooCommerce coupon overwrite
     *
     **/
    public function sdwac_coupon_coupon_amount_overwrite()
    {
        $coupons = WC()->cart->get_applied_coupons();
        $cart = WC()->cart;
        $cartProducts = $cart->get_cart();
        $store_coupons = [];
        $discount_amount = 0;
        foreach ($coupons as $coupon) {
            $couponData = new WC_Coupon($coupon);
            $post_id = $couponData->get_id();
            $post_meta = get_post_meta($post_id, "sdwac_coupon_panel", true);
            if (empty($post_meta["list_id"])) {
                return;
                exit;
            }
            $sdwac_coupon_id = $post_meta["list_id"];
            $sdwac_coupon_main = get_post_meta($sdwac_coupon_id, "sdwac_coupon_main", true);
            $sdwac_coupon_discounts = get_post_meta($sdwac_coupon_id, "sdwac_coupon_discounts", true);
            $sdwac_coupon_filters = get_post_meta($post_meta["list_id"], "sdwac_coupon_filters", true);
            if (!$sdwac_coupon_main || !$sdwac_coupon_discounts || !$sdwac_coupon_filters) {
                return;
                exit;
            }
            $sdwac_coupon_coupon_type = $sdwac_coupon_main["type"];
            if ($sdwac_coupon_coupon_type == "cart") {
                switch ($sdwac_coupon_discounts["type"]) {
                    case 'percentage':
                        $discount_total = ($sdwac_coupon_discounts["value"] / 100) * $cart->subtotal;
                        break;
                    case 'fixed':
                        $discount_total = $sdwac_coupon_discounts["value"];
                        break;
                }
                if ($post_meta["overwrite_discount"] === null) {
                    $store_coupons[$coupon] = $couponData->get_amount();
                    $cart->add_fee($sdwac_coupon_main["label"] ? $sdwac_coupon_main["label"] : "Cart Discount", -$discount_total);
                    $discount_amount += $couponData->get_amount();
                } else {
                    $store_coupons[$coupon] = $discount_total;
                }
                $discount_amount += $discount_total;
            } else if ($sdwac_coupon_coupon_type == "product") {
                if ($post_meta["overwrite_discount"] === null) {
                    $discount_total = $couponData->get_amount();
                    $store_coupons[$coupon] = $discount_total;
                    $discount_amount += $discount_total;
                } else {
                    // wp_register_style('dummy-handle', false);
                    // wp_enqueue_style('dummy-handle');
                    // wp_add_inline_style(
                    //     'dummy-handle',
                    //     '.coupon-' . $coupon . ' { display: none; }'
                    // );
                    $store_coupons[$coupon] = "product discount";
                    $discount_amount += .001;
                }
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
                        if ($post_meta["overwrite_discount"] === null) {
                            $store_coupons[$coupon] = $couponData->get_amount();
                            $cart->add_fee($sdwac_coupon_main["label"] ? $sdwac_coupon_main["label"] : "Bulk Discount", -$discount_total);
                            $discount_amount += $couponData->get_amount();
                        } else {
                            $store_coupons[$coupon] = $discount_total;
                        }
                        $discount_amount += $discount_total;
                    }
                }
            }
        }

        $store_keys = [];
        foreach ($store_coupons as $key => $value) {
            array_push($store_keys, $key);
        }
        $cart->applied_coupons = $store_keys;
        $cart->coupon_discount_totals = $store_coupons;
        $this->discount_amount = $discount_amount;
        add_filter('woocommerce_cart_subtotal', [$this, "sdwac_coupon_cart_subtotal"], 10, 3);
    }

    /**
     * WooCommerce update product price if sdwac_coupon_coupon is product adjustment
     *
     **/
    public function sdwac_coupon_update_product_price($price, $product)
    {
        if (is_admin()) {
            return $price;
        }
        $coupons = WC()->cart->applied_coupons;
        if (empty($coupons)) {
            return $price;
        }
        $cartProducts = WC()->cart->get_cart();
        foreach ($coupons as $coupon) {
            $couponData = new WC_Coupon($coupon);
            $post_id = $couponData->get_id();
            $post_meta = get_post_meta($post_id, "sdwac_coupon_panel", true);
            if (empty($post_meta["list_id"])) {
                return $price;
            }
            $sdwac_coupon_id = $post_meta["list_id"];
            $sdwac_coupon_main = get_post_meta($sdwac_coupon_id, "sdwac_coupon_main", true);
            if (!$sdwac_coupon_main) {
                return $price;
            }
            $sdwac_coupon_discounts = get_post_meta($sdwac_coupon_id, "sdwac_coupon_discounts", true);
            $sdwac_coupon_filters = get_post_meta($post_meta["list_id"], "sdwac_coupon_filters", true);
            if ($sdwac_coupon_main["type"] != "product") {
                return $price;
            }
            foreach ($sdwac_coupon_filters as $sdwac_coupon_filter) {
                if ($sdwac_coupon_filter["type"] == "products") {
                    foreach ($cartProducts as $cartProduct) {
                        if ($cartProduct["data"]->get_id() == $product->get_id()) {
                            switch ($sdwac_coupon_discounts["type"]) {
                                case 'percentage':
                                    $discount = ($sdwac_coupon_discounts["value"] / 100) * (float)$product->get_regular_price();
                                    break;
                                case 'fixed':
                                    $discount = $sdwac_coupon_discounts["value"];
                                    break;
                            }
                            $amount = ((float)$product->get_regular_price() - $discount);
                            $product->set_sale_price($amount);
                            return $amount;
                        }
                    }
                } else if ($sdwac_coupon_filter["type"] == "all_products") {
                    foreach ($cartProducts as $cartProduct) {
                        $discount = 0;
                        switch ($sdwac_coupon_discounts["type"]) {
                            case 'percentage':
                                $discount = ($sdwac_coupon_discounts["value"] / 100) * (float)$product->get_regular_price();
                                break;
                            case 'fixed':
                                $discount = $sdwac_coupon_discounts["value"];
                                break;
                        }
                        $amount = ((float)$product->get_regular_price() - $discount);
                        $product->set_sale_price($amount);
                        return $amount;
                    }
                }
            }
        }
        return $price;
    }

    /**
     * change product coupon html
     *
     * @return coupon_html
     **/
    public function sdwac_coupon_change_product_coupon_html($coupon_html, $coupon, $discount_amount_html)
    {
        $post_meta = get_post_meta($coupon->get_id(), "sdwac_coupon_panel", true);
        if (empty($post_meta["list_id"])) {
            return $coupon_html;
        }
        $sdwac_coupon_main = get_post_meta($post_meta["list_id"], "sdwac_coupon_main", true);
        $sdwac_coupon_discounts = get_post_meta($post_meta["list_id"], "sdwac_coupon_discounts", true);

        if ($sdwac_coupon_main["type"] === "product") {
            if ($sdwac_coupon_discounts["type"] == "percentage") {
                $discount_amount_html = '[on products] <span class="woocommerce-Price-amount amount">' . $sdwac_coupon_discounts["value"] . '%</span>';
            } else {
                $discount_amount_html = '[on products] <span class="woocommerce-Price-amount amount">' . get_woocommerce_currency_symbol() . ' ' . $sdwac_coupon_discounts["value"] . '</span>';
            }
            $coupon_html          = $discount_amount_html . ' <a class="woocommerce-remove-coupon" href="' . esc_url(add_query_arg('remove_coupon', urlencode($coupon->get_code()), defined('WOOCOMMERCE_CHECKOUT') ? wc_get_checkout_url() : wc_get_cart_url())) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr($coupon->get_code()) . '">' . __('[Remove]', 'sdevs_wea') . '</a>';
        }
        return $coupon_html;
    }


    /**
     * WooCommerce Custom Validator
     *
     **/
    public function sdwac_coupon_woocommerce_coupon_is_valid($valid, $coupon)
    {
        if (!$valid)
            return false;

        $validator = Validator::check($coupon->get_code(), $coupon->get_id());

        if ($validator)
            return true;
        else
            return false;
    }
}
