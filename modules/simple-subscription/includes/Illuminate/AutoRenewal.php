<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * AutoRenewal [ helper class ]
 * @package SpringDevs\WcSubscription\Illuminate
 */
class AutoRenewal
{
    public function __construct()
    {
        add_action('subscrpt_when_product_expired', [$this, 'product_expired_action'], 10, 4);
    }

    public function product_expired_action($subscription_id, $product_id, $all_data, $early_renew)
    {
        $is_auto_renew = get_post_meta($subscription_id, '_subscrpt_auto_renew', true);
        if (sdevs_has_pro_version() && get_option('subscrpt_manual_renew', '1') != '1' && $is_auto_renew == 0) return;
        $post_meta = get_post_meta($subscription_id, "_subscrpt_order_general", true);
        if (empty($post_meta) && !is_array($post_meta)) return;
        $original_order_id = $post_meta['order_id'];
        $old_order = wc_get_order($post_meta['order_id']);
        if ($old_order->get_status() != 'completed') return;
        $new_order = wc_create_order([
            'customer_id'   => $old_order->get_user_id(),
            'parent'        => $post_meta['order_id']
        ]);
        $order_id = $new_order->get_id();
        $variation_id = isset($post_meta['variation_id']) ? $post_meta['variation_id'] : 0;
        $product = wc_get_product($product_id);
        $product_name = $product->get_name();
        if ($product->is_type('variable') && $variation_id != 0) {
            $variation = new \WC_Product_Variation($variation_id);
            $product_name = $variation->get_formatted_name();
        }
        $new_order->add_product(
            $product,
            $post_meta['qty'],
            ['name' => $product_name, 'variation_id' => $variation_id]
        );

        update_post_meta($order_id, '_order_shipping',         get_post_meta($original_order_id, '_order_shipping', true));
        update_post_meta($order_id, '_order_discount',         get_post_meta($original_order_id, '_order_discount', true));
        update_post_meta($order_id, '_cart_discount',          get_post_meta($original_order_id, '_cart_discount', true));
        update_post_meta($order_id, '_order_tax',              get_post_meta($original_order_id, '_order_tax', true));
        update_post_meta($order_id, '_order_shipping_tax',     get_post_meta($original_order_id, '_order_shipping_tax', true));
        update_post_meta($order_id, '_order_total',            get_post_meta($original_order_id, '_order_total', true));

        update_post_meta($order_id, '_order_key',              'wc_' . apply_filters('woocommerce_generate_order_key', uniqid('order_')));
        update_post_meta($order_id, '_customer_user',          get_post_meta($original_order_id, '_customer_user', true));
        update_post_meta($order_id, '_order_currency',         get_post_meta($original_order_id, '_order_currency', true));
        update_post_meta($order_id, '_prices_include_tax',     get_post_meta($original_order_id, '_prices_include_tax', true));
        update_post_meta($order_id, '_customer_ip_address',    get_post_meta($original_order_id, '_customer_ip_address', true));
        update_post_meta($order_id, '_customer_user_agent',    get_post_meta($original_order_id, '_customer_user_agent', true));

        //3 Add Billing Fields

        update_post_meta($order_id, '_billing_city',           get_post_meta($original_order_id, '_billing_city', true));
        update_post_meta($order_id, '_billing_state',          get_post_meta($original_order_id, '_billing_state', true));
        update_post_meta($order_id, '_billing_postcode',       get_post_meta($original_order_id, '_billing_postcode', true));
        update_post_meta($order_id, '_billing_email',          get_post_meta($original_order_id, '_billing_email', true));
        update_post_meta($order_id, '_billing_phone',          get_post_meta($original_order_id, '_billing_phone', true));
        update_post_meta($order_id, '_billing_address_1',      get_post_meta($original_order_id, '_billing_address_1', true));
        update_post_meta($order_id, '_billing_address_2',      get_post_meta($original_order_id, '_billing_address_2', true));
        update_post_meta($order_id, '_billing_country',        get_post_meta($original_order_id, '_billing_country', true));
        update_post_meta($order_id, '_billing_first_name',     get_post_meta($original_order_id, '_billing_first_name', true));
        update_post_meta($order_id, '_billing_last_name',      get_post_meta($original_order_id, '_billing_last_name', true));
        update_post_meta($order_id, '_billing_company',        get_post_meta($original_order_id, '_billing_company', true));

        //4 Add Shipping Fields

        update_post_meta($order_id, '_shipping_country',       get_post_meta($original_order_id, '_shipping_country', true));
        update_post_meta($order_id, '_shipping_first_name',    get_post_meta($original_order_id, '_shipping_first_name', true));
        update_post_meta($order_id, '_shipping_last_name',     get_post_meta($original_order_id, '_shipping_last_name', true));
        update_post_meta($order_id, '_shipping_company',       get_post_meta($original_order_id, '_shipping_company', true));
        update_post_meta($order_id, '_shipping_address_1',     get_post_meta($original_order_id, '_shipping_address_1', true));
        update_post_meta($order_id, '_shipping_address_2',     get_post_meta($original_order_id, '_shipping_address_2', true));
        update_post_meta($order_id, '_shipping_city',          get_post_meta($original_order_id, '_shipping_city', true));
        update_post_meta($order_id, '_shipping_state',         get_post_meta($original_order_id, '_shipping_state', true));
        update_post_meta($order_id, '_shipping_postcode',      get_post_meta($original_order_id, '_shipping_postcode', true));

        if ($old_order->get_payment_method() == 'stripe') {
            update_post_meta($order_id, '_stripe_customer_id',     get_post_meta($original_order_id, '_stripe_customer_id', true));
            update_post_meta($order_id, '_stripe_source_id',       get_post_meta($original_order_id, '_stripe_source_id', true));
            update_post_meta($order_id, '_stripe_charge_captured', get_post_meta($original_order_id, '_stripe_charge_captured', true));
        }

        // $new_order->add_coupon('Fresher', '10', '2'); // accepted $couponcode, $couponamount,$coupon_tax
        $new_order->set_payment_method($old_order->get_payment_method()); // stripe
        $new_order->set_payment_method_title($old_order->get_payment_method_title()); // Credit Card (Stripe)
        $new_order->calculate_totals();

        $value_id = isset($post_meta['variation_id']) ? $post_meta['variation_id'] : $post_meta['product_id'];
        $product_meta = get_post_meta($value_id, 'subscrpt_general', true);
        $type = subscrpt_get_typos($product_meta['time'], $product_meta["type"]);
        $post_meta['order_id'] = $new_order->get_id();
        if (!$early_renew) {
            if (time() <= $post_meta['next_date']) {
                $post_meta['next_date'] = strtotime($product_meta['time'] . " " . $type, $post_meta['next_date']);
            } else {
                $post_meta['next_date'] = strtotime($product_meta['time'] . " " . $type);
            }
        }
        $post_meta['stats'] = 'Renew Order';
        update_post_meta($subscription_id, "_subscrpt_order_general", $post_meta);
        update_post_meta($order_id, "_order_subscrpt_data", ["status" => true, "posts" => [$subscription_id]]);
        $order_history = get_post_meta($subscription_id, '_subscrpt_order_history', true);
        if (!is_array($order_history)) $order_history = [];
        array_push($order_history, $post_meta);
        update_post_meta($subscription_id, '_subscrpt_order_history', $order_history);
        $comment_id = wp_insert_comment([
            "comment_agent" => "simple-subscriptions",
            "comment_author" => "simple-subscriptions",
            "comment_content" => __('The order ' . $new_order->get_id() . ' has been created for the subscription', 'sdevs_wea'),
            "comment_post_ID" => $subscription_id,
            "comment_type" => "order_note"
        ]);
        update_comment_meta($comment_id, 'subscrpt_activity', __('Renewal Order', 'sdevs_wea'));
        do_action("subscrpt_after_create_renew_order", $new_order, $old_order, $subscription_id, $post_meta, $product_meta, $early_renew);
    }
}
