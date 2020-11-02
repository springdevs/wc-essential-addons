<?php

namespace springdevs\WooAdvanceCoupon\Frontend;

use springdevs\WooAdvanceCoupon\Frontend\Helpers\Validator;

/**
 * Class sdwac_coupon_auto
 * control auto coupon system
 */
class sdwac_auto
{
	public function __construct()
	{
		if (is_admin()) return;
		add_action('wp_head', [$this, "auto_coupon_on_cart"]); //wp_loaded
		add_action('woocommerce_cart_calculate_fees', [$this, "auto_coupon_on_cart"]);
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
		$coupons     		= get_posts($args);
		$filtered_coupons 	= $this->sdwac_coupon_filter_woocoupn($coupons);
		$applied_coupons = WC()->cart->get_applied_coupons();
		foreach ($filtered_coupons as $coupon) {
			$coupon_code = wc_get_coupon_code_by_id($coupon->ID);
			$coupon_meta = get_post_meta($coupon->ID, "sdwac_coupon_panel", true);
			$validate = Validator::check($coupon->ID, $coupon_meta['list_id']);
			if ($validate && $coupon_meta['auto_coupon'] && !in_array($coupon_code, $applied_coupons)) {
				WC()->cart->apply_coupon($coupon_code);
			}
		}
	}

	/**
	 * filter woocoupon
	 * @return filter_posts
	 **/
	public function sdwac_coupon_filter_woocoupn($posts)
	{
		$filter_posts = [];
		$first_coupon = get_option("sdwac_first_time_purchase_coupon");
		foreach ($posts as $post) {
			if ($first_coupon != $post->ID) {
				array_push($filter_posts, $post);
			}
		}
		return $filter_posts;
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
		$args   = array(
			'customer_id' => $user_id
		);
		$orders = wc_get_orders($args);
		$coupon_code = wc_get_coupon_code_by_id($coupon);
		$applied_coupons = WC()->cart->get_applied_coupons();
		if (count($orders) == 0) {
			$post_meta = get_post_meta($coupon, 'sdwac_coupon_panel', true);
			if (!$post_meta && !in_array($coupon_code, $applied_coupons)) WC()->cart->apply_coupon($coupon_code);
			$validate = Validator::check($coupon, $post_meta['list_id']);
			if ($validate && !in_array($coupon_code, $applied_coupons)) WC()->cart->apply_coupon($coupon_code);
		}
		if (in_array($coupon_code, $applied_coupons)) {
			WC()->cart->remove_coupon($coupon_code);
			wc_add_notice("Coupon has been Removed !! It's not avaiable for you !!", 'error');
		}
	}
}
