<?php

namespace SpringDevs\WcBooking\Illuminate;

/**
 * Class Order
 * @package SpringDevs\WcBooking\Illuminate
 */
class Order
{
    public function __construct()
    {
        add_action('woocommerce_order_status_changed', [$this, "order_status_changed"]);
    }

    public function order_status_changed($order_id)
    {
        $order = wc_get_order($order_id);
        $post_status = "complete";
        switch ($order->get_status()) {
            case "pending";
                $post_status = "unpaid";
                break;

            case "on-hold";
                $post_status = "confirmed";
                break;

            case "processing";
                $post_status = "paid";
                break;

            case "completed";
                $post_status = "complete";
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

            case "reconf";
                $post_status = "pending_conf";
                break;

            default;
                $post_status = "complete";
                break;
        }

        $order_meta = get_post_meta($order_id, '_booking_post_meta', true);
        if (!$order_meta || !is_array($order_meta) || !isset($order_meta['post_id'])) return;
        wp_update_post([
            "ID" => $order_meta['post_id'],
            "post_status" => $post_status
        ]);
    }
}
