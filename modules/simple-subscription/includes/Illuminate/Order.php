<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * Class Order
 * @package SpringDevs\WcSubscription\Illuminate
 */
class Order
{
    public function __construct()
    {
        add_filter('woocommerce_order_formatted_line_subtotal', array($this, 'format_order_price'), 10, 3);
        add_action('woocommerce_order_status_changed', [$this, "order_status_changed"]);
    }

    public function format_order_price($subtotal, $item, $order)
    {
        $product = wc_get_product($item['product_id']);
        if (!$product->is_type('simple')) return $subtotal;
        $post_meta = get_post_meta($item['product_id'], 'subscrpt_general', true);
        if (is_array($post_meta) && $post_meta['enable']) :
            $time = $post_meta['time'] == 1 ? null : $post_meta['time'];
            $type = Helper::get_typos($post_meta['time'], $post_meta["type"]);
            $trial = null;
            $has_trial = Helper::Check_Trial($item['product_id']);
            if (!empty($post_meta['trial_time']) && $post_meta['trial_time'] > 0 && $has_trial) {
                $trial = "<br/><small> + Get " . $post_meta['trial_time'] . " " . Helper::get_typos($post_meta['trial_time'], $post_meta['trial_type']) . " free trial!</small>";
            }
            $price_html = $subtotal . " / " . $time . " " . $type . $trial;
            return $price_html;
        else :
            return $subtotal;
        endif;
    }

    public function order_status_changed($order_id)
    {
        $order = new \WC_Order($order_id);
        $post_status = "active";

        switch ($order->get_status()) {
            case "pending";
                $post_status = "pending";
                break;

            case "on-hold";
                $post_status = "pending";
                break;

            case "completed";
                $post_status = "active";
                break;

            case "cancelled";
                $post_status = "cancelled";
                break;

            case "refunded";
                $post_status = "cancelled";
                break;

            case "failed";
                $post_status = "cancelled";
                break;

            default;
                $post_status = "active";
                break;
        }
        $order_meta = get_post_meta($order_id, "_order_subscrpt_data", true);
        if (empty($order_meta) || !is_array($order_meta)) return;
        if (!$order_meta['status']) return;
        foreach ($order_meta['posts'] as $post) {
            if (get_the_title($post) != "") {
                wp_update_post([
                    "ID" => $post,
                    "post_status" => $post_status
                ]);
                $post_meta = get_post_meta($post, "_subscrpt_order_general", true);
                Action::status($post_status, $order->get_user_id(), ["post" => $post, "product" => $post_meta['product_id']]);
            }
        }
    }
}
