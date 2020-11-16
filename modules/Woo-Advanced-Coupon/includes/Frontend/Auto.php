<?php

namespace springdevs\WooAdvanceCoupon\Frontend;

/**
 * Class Auto
 * auto coupon feature
 */
class Auto
{
    public function __construct()
    {
        if (is_admin()) return;
        add_action('wp_head', [$this, "auto_coupon_on_cart"]);
    }

    /**
     * woocommerce auto coupon function
     **/
    public function auto_coupon_on_cart()
    {
        do_action('sdwac_coupon_before_wp_loaded');
        $this->first_order();
        $this->auto_coupon();
        do_action('sdwac_coupon_after_wp_loaded');
    }

    /**
     * control first purchase features
     **/
    public function first_order()
    {
        if (!is_user_logged_in()) return;
        $user    = wp_get_current_user();
        $user_id = $user->ID;
        $coupon  = get_option("sdwac_first_time_purchase_coupon");
        if ($coupon == 0) return;
        $args   = ['customer_id' => $user_id];
        $orders = wc_get_orders($args);
        $coupon_code = wc_get_coupon_code_by_id($coupon);
        $applied_coupons = WC()->cart->get_applied_coupons();
        if (count($orders) == 0) {
            if (!in_array($coupon_code, $applied_coupons)) {
                $validate = Validation::check($coupon_code);
                if ($validate) WC()->cart->apply_coupon($coupon_code);
            }
        } elseif (in_array($coupon_code, $applied_coupons)) {
            WC()->cart->remove_coupon($coupon_code);
            wc_add_notice("Coupon has been Removed !! It's not avaiable for you !!", 'error');
        }
    }

    /**
     * Auto Coupon
     **/
    public function auto_coupon()
    {
        $args = array(
            'posts_per_page' => -1,
            'order'          => 'asc',
            'post_type'      => 'shop_coupon',
            'post_status'    => 'publish',
        );
        $coupons             = get_posts($args);
        $filtered_coupons    = $this->filter_coupon($coupons);
        $applied_coupons     = WC()->cart->get_applied_coupons();
        foreach ($filtered_coupons as $coupon) {
            $coupon_code = wc_get_coupon_code_by_id($coupon->ID);
            $coupon_meta = get_post_meta($coupon->ID, "sdwac_coupon_panel", true);
            $validate = Validation::check($coupon_code);
            if ($validate && $coupon_meta['auto_coupon'] && !in_array($coupon_code, $applied_coupons)) {
                WC()->cart->apply_coupon($coupon_code);
            }
        }
    }

    /**
     * filter woocoupon
     * @return filter_posts
     **/
    public function filter_coupon($posts)
    {
        $filter_posts = [];
        $first_coupon = get_option("sdwac_first_time_purchase_coupon");
        foreach ($posts as $post) if ($first_coupon != $post->ID) array_push($filter_posts, $post);
        return $filter_posts;
    }
}
