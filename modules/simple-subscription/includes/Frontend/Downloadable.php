<?php

namespace SpringDevs\WcSubscription\Frontend;

/**
 * Downloadable class
 * control Download feature - woocommerce
 */
class Downloadable
{
    public function __construct()
    {
        add_filter('woocommerce_customer_get_downloadable_products', [$this, "check_download_items"], 10, 1);
        add_filter('woocommerce_order_get_downloadable_items', [$this, "check_download_items"], 10, 1);
    }

    public function check_download_items($downloads)
    {
        $expired_items = get_user_meta(get_current_user_id(), '_subscrpt_expired_items', true);
        $pending_items = get_user_meta(get_current_user_id(), '_subscrpt_pending_items', true);
        $cancelled_items = get_user_meta(get_current_user_id(), '_subscrpt_cancelled_items', true);

        if (!is_array($expired_items)) $expired_items = [];
        if (!is_array($pending_items)) $pending_items = [];
        if (!is_array($cancelled_items)) $cancelled_items = [];

        $expired_products = [];
        foreach ($expired_items as $expired_item) {
            array_push($expired_products, $expired_item['product']);
        }

        $pending_products = [];
        foreach ($pending_items as $pending_item) {
            array_push($pending_products, $pending_item['product']);
        }

        $cancelled_products = [];
        foreach ($cancelled_items as $cancelled_item) {
            array_push($cancelled_products, $cancelled_item['product']);
        }

        foreach ($downloads as $key => $download) {
            if (in_array($download['product_id'], $expired_products)) unset($downloads[$key]);
            if (in_array($download['product_id'], $pending_products)) unset($downloads[$key]);
            if (in_array($download['product_id'], $cancelled_products)) unset($downloads[$key]);
        }
        return $downloads;
    }
}
