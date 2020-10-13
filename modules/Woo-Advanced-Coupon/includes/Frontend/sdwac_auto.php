<?php

namespace springdevs\WooAdvanceCoupon\Frontend;

use springdevs\WooAdvanceCoupon\Frontend\Helpers\Apply;
use springdevs\WooAdvanceCoupon\Frontend\Helpers\Validator;

/**
 * Class sdwac_coupon_auto
 * control auto coupon system
 */
class sdwac_auto
{

	public function __construct()
	{
		if (is_admin()) {
			return;
		}
		add_action('wp_loaded', [$this, "sdwac_coupon_auto_coupon_on_cart"]);
		add_action('woocommerce_cart_calculate_fees', [$this, "sdwac_coupon_auto_coupon_on_cart"]);
		add_filter("woocommerce_product_get_price", [$this, "sdwac_coupon_change_price"], 10, 2);
	}

	/**
	 * woocommerce auto coupon function
	 **/
	public function sdwac_coupon_auto_coupon_on_cart()
	{
		do_action('sdwac_coupon_before_wp_loaded');
		$this->sdwac_coupon_first_order();
		$this->sdwac_coupon_auto_coupon();
		do_action('sdwac_coupon_after_wp_loaded');
	}

	/**
	 * sdwac_coupon Auto Coupon
	 **/
	public function sdwac_coupon_auto_coupon()
	{
		$args       = [
			"post_type"   => "woocoupon",
			'post_status' => 'publish'
		];
		$posts      = get_posts($args);
		$woocoupons = $this->sdwac_coupon_filter_woocoupn($posts);
		$first_coupon          = get_option("sdwac_first_time_purchase_coupon");
		if (count($woocoupons) == 0) {
			if ($first_coupon == 0) {
				WC()->session->__unset("sdwac_product_coupon");
			}
		}
		foreach ($woocoupons as $woocoupon) {
			$sdwac_coupon_main = get_post_meta($woocoupon->ID, "sdwac_coupon_main", true);

			if ($sdwac_coupon_main && $sdwac_coupon_main["type"] != "product") {
				if ($first_coupon == 0) {
					WC()->session->__unset("sdwac_product_coupon");
				}
			}
			$validate = Validator::check(null, null, $woocoupon->ID);
			if ($validate) {
				$apply                 = new Apply;
				$res_data              = $apply->apply_discount($woocoupon->ID);
				$sdwac_coupon_woo_setting_multi = get_option("sdwac_multi");
				if ($res_data) {
					$cart = WC()->cart;
					if ($sdwac_coupon_woo_setting_multi == "yes") {
						$cart->add_fee($res_data["label"], -$res_data["amount"]);
					} else {
						$cart->add_fee($res_data["label"], -$res_data["amount"]);
						break;
					}
				}
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

		$sdwac_coupon_coupons = [];
		$args        = array(
			'posts_per_page' => -1,
			'order'          => 'asc',
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
		);
		$coupons     = get_posts($args);
		$first_coupon = get_option("sdwac_first_time_purchase_coupon");

		if (count($coupons) != 0) {
			foreach ($coupons as $coupon) {
				$post_meta = get_post_meta($coupon->ID, "sdwac_coupon_panel", true);
				if ($post_meta != '') {
					if (!empty($post_meta["list_id"]) || $post_meta["list_id"] != '') {
						array_push($sdwac_coupon_coupons, $post_meta["list_id"]);
					}
				}
			}
			foreach ($posts as $post) {
				if (!in_array($post->ID, $sdwac_coupon_coupons) && $first_coupon != $post->ID) {
					array_push($filter_posts, $post);
				}
			}
		} else {
			foreach ($posts as $post) {
				if ($first_coupon != $post->ID) {
					array_push($filter_posts, $post);
				}
			}
		}

		return $filter_posts;
	}

	/**
	 * control first purchase features
	 **/
	public function sdwac_coupon_first_order()
	{
		if (!is_user_logged_in()) {
			return;
		}

		$user    = wp_get_current_user();
		$user_id = $user->ID;
		$coupon  = get_option("sdwac_first_time_purchase_coupon");
		if ($coupon == 0 && isset(WC()->session)) {
			$session_data = WC()->session->get("sdwac_product_coupon");
			if ($session_data != null && $session_data["first_coupon"] == "yes") {
				WC()->session->__unset("sdwac_product_coupon");
				return;
			}
			return;
		}
		$args   = array(
			'customer_id' => $user_id
		);
		$orders = wc_get_orders($args);
		if (count($orders) == 0) {
			$validate = Validator::check(null, null, $coupon);
			if ($validate) {
				$apply    = new Apply;
				$res_data = $apply->apply_discount($coupon);
				if ($res_data) {
					$cart = WC()->cart;
					$cart->add_fee($res_data["label"], -$res_data["amount"]);
				}
			}
		}
	}

	/**
	 * woocommerce product change price
	 **/
	public function sdwac_coupon_change_price($price, $product)
	{
		$data = WC()->session->get("sdwac_product_coupon");
		if ($data) {
			if ($data["first_coupon"] === "yes") {
				$coupon          = get_option("sdwac_first_time_purchase_coupon");
				if ($coupon == 0) {
					return $price;
				}
				$sdwac_coupon_main        = get_post_meta($coupon, "sdwac_coupon_main", true);
				if (!$sdwac_coupon_main) {
					return $price;
				}
				$sdwac_coupon_coupon_type = $sdwac_coupon_main["type"];
				$sdwac_coupon_discounts   = get_post_meta($coupon, "sdwac_coupon_discounts", true);
				$sdwac_coupon_filters     = get_post_meta($coupon, "sdwac_coupon_filters", true);
				if ($sdwac_coupon_coupon_type == "product") {
					foreach ($sdwac_coupon_filters as $sdwac_coupon_filter) {
						if ($sdwac_coupon_filter["type"] == "products") {
							foreach ($sdwac_coupon_filter["items"] as $sdwac_couponproducts) {
								if ($sdwac_couponproducts["value"] == $product->get_id()) {
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
						} elseif ($sdwac_coupon_filter["type"] == "all_products") {
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
			} else {
                add_filter("woocommerce_product_variation_get_price", [$this, "sdwac_coupon_variable_change_price"], 10, 2);
				return $this->sdwac_coupon_auto_product_coupon($price, $product);
			}
		}
		return $price;
	}

	/*
	 * automatically change product price
	 */
	public function sdwac_coupon_auto_product_coupon($price, $product)
	{
		$data = WC()->session->get("sdwac_product_coupon");
		foreach ($data["items"] as $woocoupon) {
			$validate = Validator::check(null, null, $woocoupon);
			if (!$validate) {
				return $price;
			}
			$sdwac_coupon_main        = get_post_meta($woocoupon, "sdwac_coupon_main", true);
			$sdwac_coupon_coupon_type = $sdwac_coupon_main["type"];
			$sdwac_coupon_discounts = get_post_meta($woocoupon, "sdwac_coupon_discounts", true);
			$sdwac_coupon_filters     = get_post_meta($woocoupon, "sdwac_coupon_filters", true);

			if ($sdwac_coupon_coupon_type == "product") {
				foreach ($sdwac_coupon_filters as $sdwac_coupon_filter) {
					if ($sdwac_coupon_filter["type"] == "products") {
						foreach ($sdwac_coupon_filter["items"] as $sdwac_couponproducts) {
							if ($sdwac_couponproducts["value"] == $product->get_id()) {
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
						return $price;
					} elseif ($sdwac_coupon_filter["type"] == "all_products") {
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
			} else {
				return $price;
			}
		}
		return $price;
	}

	public function sdwac_coupon_variable_change_price($price, $product)
	{
		$data = WC()->session->get("sdwac_product_coupon");
		if (!is_array($data)) return $price;
		foreach ($data["items"] as $woocoupon) {
			$validate = Validator::check(null, null, $woocoupon);
			if (!$validate) {
				return $price;
			}
			$sdwac_coupon_main        = get_post_meta($woocoupon, "sdwac_coupon_main", true);
			$sdwac_coupon_coupon_type = $sdwac_coupon_main["type"];
			$sdwac_coupon_discounts = get_post_meta($woocoupon, "sdwac_coupon_discounts", true);
			$sdwac_coupon_filters     = get_post_meta($woocoupon, "sdwac_coupon_filters", true);

			if ($sdwac_coupon_coupon_type == "product") {
				foreach ($sdwac_coupon_filters as $sdwac_coupon_filter) {
					if ($sdwac_coupon_filter["type"] == "products") {
						foreach ($sdwac_coupon_filter["items"] as $sdwac_couponproducts) {
							if ($sdwac_couponproducts["value"] == $product->get_parent_id()) {
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
						return $price;
					} elseif ($sdwac_coupon_filter["type"] == "all_products") {
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
			} else {
				return $price;
			}
		}
		return $price;
	}
}
