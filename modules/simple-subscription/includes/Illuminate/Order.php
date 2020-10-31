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
        add_action('woocommerce_admin_order_item_headers', [$this, 'admin_order_item_header']);
        add_action('woocommerce_admin_order_item_values', [$this, 'admin_order_item_value'], 10, 2);
        add_action('woocommerce_before_order_itemmeta', [$this, 'add_order_item_data'], 10, 3);
        // add_filter('woocommerce_order_amount_item_subtotal', function ($subtotal, $order, $item) {
        //     $product = wc_get_product($item['product_id']);
        //     if (!$product->is_type('simple')) return $subtotal;
        //     $post_meta = get_post_meta($item['product_id'], 'subscrpt_general', true);
        //     if (is_array($post_meta) && $post_meta['enable']) :
        //         $time = $post_meta['time'] == 1 ? null : $post_meta['time'];
        //         $type = Helper::get_typos($post_meta['time'], $post_meta["type"]);
        //         $trial = null;
        //         $has_trial = Helper::Check_Trial($item['product_id']);
        //         if (!empty($post_meta['trial_time']) && $post_meta['trial_time'] > 0 && $has_trial) {
        //             $trial = "<br/><small> + Get " . $post_meta['trial_time'] . " " . Helper::get_typos($post_meta['trial_time'], $post_meta['trial_type']) . " free trial!</small>";
        //         }
        //         $price_html = $subtotal . " / " . $time . " " . $type . $trial;
        //         return $price_html;
        //     else :
        //         return $subtotal;
        //     endif;
        // }, 10, 3);
        add_action('woocommerce_order_status_changed', [$this, "order_status_changed"]);
        // add_action('subscrpt_gateway_stripe_process_response', function ($response, $order) {
        //     $order_meta = get_post_meta($order->get_id(), '_order_subscrpt_data', true);
        //     if (empty($order_meta) || !is_array($order_meta) || !$order_meta['status']) return;
        //     $post_id = $order_meta['posts'][0];
        //     $post_meta = get_post_meta($post_id, '_subscrpt_order_general', true);
        //     $product_meta = get_post_meta($post_meta['product_id'], 'subscrpt_general', true);
        //     $type = Helper::get_typos($product_meta['time'], $product_meta["type"]);
        //     if ('succeeded' === $response->status) {
        //         $post_meta['start_date'] = time();
        //         $post_meta['next_date'] = strtotime($product_meta['time'] . " " . $type);
        //         update_post_meta($post_id, '_subscrpt_order_general', $post_meta);
        //         $data = ["post" => $post_id, "product" => $post_meta['product_id']];
        //         if (isset($post_meta['variation_id'])) $data['variation'] = $post_meta['variation_id'];
        //         $order_history = get_post_meta($post_id, '_subscrpt_order_history', true);
        //         if (!is_array($order_history)) $order_history = [];
        //         array_push($order_history, $post_meta);
        //         update_post_meta($post_id, '_subscrpt_order_history', $order_history);
        //     }
        // }, 10, 2);
    }

    public function format_order_price($subtotal, $item, $order)
    {
        $product = wc_get_product($item['product_id']);
        if (!$product->is_type('simple')) return $subtotal;
        $order_data = get_post_meta($order->get_id(), '_order_subscrpt_full_data', true);
        if (!is_array($order_data)) $order_data = [];
        foreach ($order_data as $post_meta) {
            if (is_array($post_meta) && isset($post_meta['stats']) && $item['product_id'] == $post_meta['product_id']) :
                $trial = null;
                $has_trial = isset($post_meta['trial']) && strlen($post_meta['trial']) > 2 ? true : false;
                $signup_fee_html = null;
                if ($has_trial) {
                    $trial = "<br/><small> + Get " . $post_meta['trial'] . " " . " free trial!</small>";
                    if (isset($post_meta['signup_fee']) && $post_meta['signup_fee'] > 0) $signup_fee_html = "<br/> + Signup fee of " . wc_price($post_meta['signup_fee']);
                }
                $price_html = $post_meta['subtotal_price_html'] . $signup_fee_html . $trial;
                return $price_html;
            endif;
        }
        return $subtotal;
    }

    public function admin_order_item_header($order)
    {
?>
        <th class="item_recurring sortable" data-sort="float"><?php esc_html_e('Recurring', 'sdevs_wea'); ?></th>
    <?php
    }

    public function admin_order_item_value($product, $item)
    {
        $subtotal = "-";
        $order_data = get_post_meta($item->get_order_id(), '_order_subscrpt_full_data', true);
        if (!is_array($order_data)) $order_data = [];
        foreach ($order_data as $post_meta) {
            if (is_array($post_meta) && isset($post_meta['stats']) && !is_null($product) && $product->get_id() == $post_meta['product_id']) :
                $subtotal = $post_meta['subtotal_price_html'];
            endif;
        }
    ?>
        <td class="item_recurring" width="1%" data-sort-value="<?php echo esc_attr($subtotal); ?>">
            <div class="view">
                <?php echo $subtotal; ?>
            </div>
        </td>
<?php
    }

    public function add_order_item_data($item_id, $item, $product)
    {
        $order_data = get_post_meta($item->get_order_id(), '_order_subscrpt_full_data', true);
        if (!is_array($order_data)) $order_data = [];
        foreach ($order_data as $post_meta) {
            if (is_array($post_meta) && isset($post_meta['stats']) && $product->get_id() == $post_meta['product_id']) :
                $has_trial = isset($post_meta['trial']) && strlen($post_meta['trial']) > 2 ? true : false;
                if ($has_trial) {
                    if (isset($post_meta['signup_fee']) && $post_meta['signup_fee'] > 0) echo "<small> + Signup fee of " . wc_price($post_meta['signup_fee']) . '</small><br/>';
                    echo "<small> + Get " . $post_meta['trial'] . " " . " free trial!</small>";
                }
            endif;
        }
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
                $acdata = ["post" => $post, "product" => $post_meta['product_id']];
                if (isset($post_meta['variation_id'])) $acdata['variation'] = $post_meta['variation_id'];
                Action::status($post_status, $order->get_user_id(), $acdata);
            }
        }
    }
}
