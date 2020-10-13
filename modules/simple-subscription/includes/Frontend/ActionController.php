<?php


namespace SpringDevs\WcSubscription\Frontend;

use SpringDevs\WcSubscription\Illuminate\Action;

/**
 * Class ActionController
 * @package SpringDevs\WcSubscription\Frontend
 */
class ActionController
{
    public function __construct()
    {
        add_action("before_single_subscrpt_content", [$this, "control_action_subscrpt"]);
    }

    public function control_action_subscrpt()
    {
        if (!(isset($_GET['subscrpt_id']) && isset($_GET['action']) && isset($_GET['wpnonce']))) return;
        $subscrpt_id = $_GET['subscrpt_id'];
        $action = $_GET['action'];
        $wpnonce = $_GET['wpnonce'];
        if (!wp_verify_nonce($wpnonce, "subscrpt_nonce")) wp_die(__('Sorry !! You cannot permit to access.', 'sdevs_wea'));
        if ($action == 'renew') {
            $this->RenewProduct($subscrpt_id);
        } else {
            $post_meta = get_post_meta($subscrpt_id, '_subscrpt_order_general', true);
            if ($action == 'cancelled') {
                $product_meta = get_post_meta($post_meta['product_id'], 'subscrpt_general', true);
                if ($product_meta['user_cancell'] == 'no') return;
            }
            wp_update_post([
                'ID' => $subscrpt_id,
                'post_status' => $action
            ]);
            Action::status($action, get_current_user_id(), ["post" => $subscrpt_id, "product" => $post_meta['product_id']]);
        }
    }

    public function RenewProduct($subscrpt_id)
    {
        $post_meta = get_post_meta($subscrpt_id, "_subscrpt_order_general", true);
        WC()->cart->add_to_cart(
            $post_meta['product_id'],
            1,
            0,
            [],
            ['renew_subscrpt' => true]
        );
    }
}
