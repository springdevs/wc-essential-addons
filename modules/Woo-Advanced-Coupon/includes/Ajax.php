<?php

namespace springdevs\WooAdvanceCoupon;

/**
 * Ajax Handler
 */
class Ajax
{

	function __construct()
	{
		add_action('wp_ajax_sdwac_coupon_product_search', [$this, 'sdwac_coupon_product_search']);
		add_action('wp_ajax_sdwac_coupon_get_filters', [$this, 'sdwac_coupon_get_filters']);
		add_action('wp_ajax_sdwac_coupon_save_filters', [$this, 'sdwac_coupon_save_filters']);
		add_action('wp_ajax_sdwac_coupon_get_main', [$this, 'sdwac_coupon_get_main']);
		add_action('wp_ajax_sdwac_coupon_get_discounts', [$this, 'sdwac_coupon_get_discounts']);
		add_action('wp_ajax_sdwac_coupon_get_rules', [$this, 'sdwac_coupon_get_rules']);
		add_action('wp_ajax_sdwac_coupon_get_woocoupons', [$this, 'coupon_get_woocoupons']);
		add_action('wp_ajax_sdwac_coupon_get_sdwac_coupon_panel', [$this, 'sdwac_coupon_get_sdwac_coupon_panel']);
	}

	public function sdwac_coupon_get_sdwac_coupon_panel()
	{
		$post_id = (int)$_POST["post_id"];
		if (is_int($post_id)) {
			$post_meta = get_post_meta($post_id, "sdwac_coupon_panel", true);
			wp_send_json($post_meta);
		}
	}

	public function coupon_get_woocoupons()
	{
		$args = [
			"post_type" => "woocoupon",
			'post_status' => 'publish'
		];
		$posts = get_posts($args);
		$filter_Posts = [];
		foreach ($posts as $post) {
			array_push($filter_Posts, [
				"label" => $post->post_title,
				"value" => $post->ID
			]);
		}
		wp_send_json($filter_Posts);
	}

	public function sdwac_coupon_get_rules()
	{
		$post_id = (int)$_POST["post_id"];
		if (is_int($post_id)) {
			$post_meta = get_post_meta($post_id, "_sdwac_coupon_meta", true);
			if (isset($post_meta['rules']) && isset($post_meta['relation']) && is_array($post_meta['rules'])) wp_send_json(["relation" => $post_meta['relation'], "rules" => $post_meta['rules']]);
		}
	}

	public function sdwac_coupon_get_discounts()
	{
		$post_id = (int)$_POST["post_id"];
		if (is_int($post_id)) {
			$post_meta = get_post_meta($post_id, "_sdwac_coupon_meta", true);
			if (isset($post_meta['discounts']) && is_array($post_meta['discounts'])) wp_send_json($post_meta['discounts']);
		}
	}

	public function sdwac_coupon_get_main()
	{
		$post_id = (int)$_POST["post_id"];
		if (is_int($post_id)) {
			$post_meta = get_post_meta($post_id, "sdwac_coupon_main", true);
			$discount_type = [
				"product" => ["label" => "Product Adjustment", "has_label" => false],
				"cart" => ["label" => "Cart Adjustment", "has_label" => true],
				"bulk" => ["label" => "Bulk Discount", "has_label" => true]
			];
			$data = [
				"post_meta" => $post_meta,
				"discount_type" => apply_filters("sdwac_coupon_discount_type", $discount_type)
			];
			wp_send_json($data);
		}
	}

	public function sdwac_coupon_product_search()
	{
		$args = [
			"post_type" => "product",
			'post_status' => 'publish',
			"s" => sanitize_text_field($_POST["queryData"])
		];
		$posts = get_posts($args);
		$filter_Posts = [];
		foreach ($posts as $post) {
			array_push($filter_Posts, [
				"label" => $post->post_title,
				"value" => $post->ID
			]);
		}

		if (isset($_POST["option"])) {
			foreach ($_POST["option"] as $option) {
				$filter_Posts = array_filter($filter_Posts, function ($post) use ($option) {
					return ($post["value"] != $option["value"]);
				});
			}
		}

		wp_send_json($filter_Posts);
	}

	public function sdwac_coupon_get_filters()
	{
		$post_id = (int)$_POST["post_id"];
		if (is_int($post_id)) {
			$post_meta = get_post_meta($post_id, "sdwac_coupon_filters", true);
			$filters_data = [
				["label" => "All Products", "value" => "all_products", "has_item" => false, "items" => null],
				[
					"label" => "Products", "value" => "products", "has_item" => true,
					"items" => ["action" => "sdwac_coupon_product_search", "label" => "Select Products"]
				]
			];
			$send_data = [
				"post_meta" => $post_meta,
				"filters_data" => apply_filters("sdwac_coupon_filters", $filters_data)
			];
			wp_send_json($send_data);
		}
	}

	public function sdwac_coupon_save_filters()
	{
		if (!wp_verify_nonce($_POST["sdwac_coupon_nonce"], "sdwac_coupon_with_ajax")) {
			wp_die(__('Sorry !! You cannot permit to access.', 'sdevs_wea'));
		}
		$post_id = (int)$_POST["post_id"];
		if (is_int($post_id)) {
			$sdwac_couponfilters = [];
			foreach ($_POST["sdwac_couponfilters"] as $sdwac_coupon_filter) {
				if (!isset($sdwac_coupon_filter["items"])) {
					$sdwac_coupon_filter["items"] = [];
				}
				array_push($sdwac_couponfilters, $sdwac_coupon_filter);
			}
			update_post_meta($post_id, "sdwac_coupon_filters", $sdwac_couponfilters);
			wp_send_json(["message" => "Updated SuccessFully", "status" => "success"]);
		}
	}
}
