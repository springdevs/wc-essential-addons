<?php

namespace SpringDevs\WcSubscription\Frontend;

use SpringDevs\WcSubscription\Illuminate\Action;
use SpringDevs\WcSubscription\Illuminate\Helper;

/**
 * Thankyou class
 * control Thank You page
 */
class Thankyou
{
    public function __construct()
    {
        add_action('woocommerce_before_thankyou', array($this, 'subscription_thank_you'));
        add_action('woocommerce_order_details_after_order_table', array($this, 'display_subscrpt_details'));
    }

    public function subscription_thank_you($order_id)
    {
        if (!$order_id) return;
        $post_meta = get_post_meta($order_id, "_order_subscrpt_data", true);
        if (!empty($post_meta) && is_array($post_meta) && $post_meta['status']) return;
        $order_subscrpt_products = [];
        $order_subscrpt_full_data = [];
        $order = wc_get_order($order_id);
        $post_status = 'active';
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

            case "failed";
                $post_status = "cancelled";
                break;

            default;
                $post_status = "active";
                break;
        }
        $cart_items = $order->get_items();
        foreach ($cart_items as $cart_item) {
            $conditional_key = apply_filters('subscrpt_filter_checkout_conditional_key', $cart_item['product_id']);
            $post_meta = get_post_meta($conditional_key, 'subscrpt_general', true);
            if (is_array($post_meta) && $post_meta['enable']) :
                $is_renew = isset($cart_item['_renew_subscrpt']);
                $time = $post_meta['time'] == 1 ? null : $post_meta['time'];
                $type = Helper::get_typos($post_meta['time'], $post_meta["type"]);
                $subtotal_price_html = wc_price($cart_item['subtotal']) . " / " . $time . " " . $type;
                $total_price_html = wc_price($cart_item['total']) . " / " . $time . " " . $type;
                $start_date = time();
                $trial = null;
                $has_trial = Helper::Check_Trial($cart_item['product_id']);
                if (!empty($post_meta['trial_time']) && $post_meta['trial_time'] > 0 && !$is_renew && $has_trial) {
                    $trial = $post_meta['trial_time'] . " " . Helper::get_typos($post_meta['trial_time'], $post_meta['trial_type']);
                    $start_date = strtotime($trial);
                }
                $_subscrpt_order_general = [
                    "order_id" => $order_id,
                    "stats" => "Parent Order",
                    "product_id" => $cart_item['product_id'],
                    "qty" => $cart_item->get_quantity(),
                    "subtotal" => $cart_item['subtotal'],
                    "total" => $cart_item['total'],
                    "subtotal_price_html" => $subtotal_price_html,
                    "total_price_html" => $total_price_html,
                    "trial" => $trial,
                    "start_date" => $start_date,
                    "next_date" => strtotime($post_meta['time'] . " " . $type, $start_date)
                ];
                $args = [
                    "post_title" => "Subscription",
                    "post_type" => "subscrpt_order",
                    "post_status" => $post_status
                ];
                $post_id = 0;
                $unexpire_data = ["post" => $post_id, "product" => $cart_item['product_id']];
                $unexpire_data = apply_filters('subscrpt_filter_checkout_all_ids', $unexpire_data);

                if ($is_renew && $post_status != "cancelled") {
                    $expired_items = get_user_meta(get_current_user_id(), '_subscrpt_expired_items', true);
                    if (!is_array($expired_items)) $expired_items = [];
                    foreach ($expired_items as $expired_item) {
                        if ($expired_item['product'] == $cart_item['product_id']) {
                            $unexpire_data['post'] = $expired_item['post'];
                            $comment_id = wp_insert_comment([
                                "comment_agent" => "simple-subscriptions",
                                "comment_author" => "simple-subscriptions",
                                "comment_content" => __('The order ' . $order_id . ' has been created for the subscription', 'sdevs_wea'),
                                "comment_post_ID" => $unexpire_data['post'],
                                "comment_type" => "order_note"
                            ]);
                            update_comment_meta($comment_id, 'subscrpt_activity', __('Renewal Order', 'sdevs_wea'));
                        }
                    }
                }
                if ($unexpire_data['post'] == 0 || !$is_renew || get_the_title($unexpire_data['post']) == "") {
                    $post_id = wp_insert_post($args);
                    $comment_id = wp_insert_comment([
                        "comment_agent" => "simple-subscriptions",
                        "comment_author" => "simple-subscriptions",
                        "comment_content" => __('Subscription successfully created.	order is ' . $order_id, 'sdevs_wea'),
                        "comment_post_ID" => $post_id,
                        "comment_type" => "order_note"
                    ]);
                    update_comment_meta($comment_id, 'subscrpt_activity', __('New Subscription', 'sdevs_wea'));
                    $unexpire_data['post'] = $post_id;
                }
                $post_id = $unexpire_data['post'];
                $args["post_title"] = "Subscription #{$post_id}";
                $args["ID"] = $unexpire_data['post'];
                $_subscrpt_order_general['post_id'] = $post_id;
                if ($is_renew) $_subscrpt_order_general['stats'] = 'Renew Order';
                $_subscrpt_order_general = apply_filters('subscrpt_filter_checkout_data', $_subscrpt_order_general);
                update_post_meta($unexpire_data['post'], "_subscrpt_order_general", $_subscrpt_order_general);
                $order_history = get_post_meta($unexpire_data['post'], '_subscrpt_order_history', true);
                if (!is_array($order_history)) $order_history = [];
                array_push($order_history, $_subscrpt_order_general);
                update_post_meta($unexpire_data['post'], '_subscrpt_order_history', $order_history);
                Action::status($post_status, get_current_user_id(), $unexpire_data);
                array_push($order_subscrpt_products, $post_id);
                array_push($order_subscrpt_full_data, $_subscrpt_order_general);
                wp_update_post($args);
            endif;
        }
        $order_subscrpt = [
            "status" => true,
            "posts" => $order_subscrpt_products
        ];
        update_post_meta($order_id, "_order_subscrpt_data", $order_subscrpt);
        update_post_meta($order_id, "_order_subscrpt_full_data", $order_subscrpt_full_data);
    }

    public function display_subscrpt_details($order)
    {
        $post_meta = get_post_meta($order->get_id(), "_order_subscrpt_full_data", true);
        if (!empty($post_meta) && is_array($post_meta) && count($post_meta) > 0) :
?>
            <h2 class="woocommerce-order-details__title"><?php _e('Related Subscriptions', 'sdevs_wea'); ?></h2>
            <?php
            foreach ($post_meta as $subscrpt_meta) :
                if (!empty($subscrpt_meta) && is_array($subscrpt_meta)) :
                    $post = $subscrpt_meta['post_id'];
                    $trial_status = $subscrpt_meta['trial'] == null ? false : true;
            ?>
                    <table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
                        <thead>
                            <tr>
                                <th class="woocommerce-table__product-name product-name"><?php echo get_the_title($post); ?></th>
                                <th class="woocommerce-table__product-table product-total"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="woocommerce-table__line-item order_item">
                                <td class="woocommerce-table__product-name product-name">
                                    <a href="<?php the_permalink($subscrpt_meta['product_id']); ?>"><?php echo get_the_title($subscrpt_meta['product_id']); ?></a>
                                    <strong class="product-quantity">×&nbsp;<?php echo $subscrpt_meta['qty']; ?></strong>
                                </td>
                                <td class="woocommerce-table__product-total product-total"></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th scope="row"><?php _e('Status', 'sdevs_wea') ?>:</th>
                                <td><?php echo get_post_status($post); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Recurring amount', 'sdevs_wea'); ?>:</th>
                                <td class="woocommerce-table__product-total product-total">
                                    <?php echo $subscrpt_meta['subtotal_price_html']; ?>
                                </td>
                            </tr>
                            <?php if ($trial_status == null) { ?>
                                <tr>
                                    <th scope="row"><?php _e('Next billing on', 'sdevs_wea'); ?>:</th>
                                    <td><?php echo date('F d, Y', $subscrpt_meta['next_date']); ?></td>
                                </tr>
                            <?php } else { ?>
                                <tr>
                                    <th scope="row"><?php _e('Trial', 'sdevs_wea'); ?>:</th>
                                    <td><?php echo $subscrpt_meta['trial']; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e('First billing on', 'sdevs_wea'); ?>:</th>
                                    <td><?php echo date('F d, Y', $subscrpt_meta['start_date']); ?></td>
                                </tr>
                            <?php } ?>
                        </tfoot>
                    </table>
<?php
                endif;
            endforeach;
        endif;
    }
}
