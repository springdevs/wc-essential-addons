<?php

namespace springdevs\WooAdvanceCoupon\Frontend;

/**
 * Class sdwac_coupon_url
 * control url apply coupon system
 */
class sdwac_url
{
    public function __construct()
    {
        // set coupon on session via GET method
        add_action('init', [$this, "set_coupon_url"]);
        // apply when add to cart
        add_action("woocommerce_add_to_cart", [$this, "apply_coupon"]);
        // apply coupon on session via GET method
        add_action("woocommerce_before_cart", [$this, "apply_coupon_via_url"]);
    }

    /**
     * WooCommerce set coupon on session
     *
     **/
    public function set_coupon_url()
    {
        $url = get_option("sdwac_url");
        if (isset($_GET[$url])) {
            $coupon_code = esc_attr($_GET[$url]);
            WC()->session->set('coupon_code', $coupon_code);
        }
    }

    /**
     * WooCommerce apply coupon when add-to-cart
     *
     **/
    public function apply_coupon()
    {
        $this->apply_coupon_via_url();
    }

    /**
     * WooCommerce apply coupon from session
     *
     **/
    public function apply_coupon_via_url()
    {
        $coupons = WC()->cart->get_applied_coupons();
        $code = WC()->session->get('coupon_code');
        $discounts = new \WC_Discounts(WC()->cart);
        $applied_coupons = WC()->cart->get_applied_coupons();
        if (is_wp_error($discounts) && !$discounts->is_coupon_valid($code) && in_array($code, $applied_coupons)) WC()->session->__unset('coupon_code');
        if ($code) {
            if (in_array($code, $coupons)) {
                WC()->session->__unset('coupon_code');
            } else {
                WC()->cart->apply_coupon($code);
                WC()->session->__unset('coupon_code');
            }
        }
    }
}
