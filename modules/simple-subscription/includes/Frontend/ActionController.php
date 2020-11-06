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
        } elseif ($action == 'early-renew') {
            $post_meta = get_post_meta($subscrpt_id, '_subscrpt_order_general', true);
            $data = ["post" => $subscrpt_id, "product" => $post_meta['product_id']];
            if (isset($post_meta['variation_id'])) $data['variation'] = $post_meta['variation_id'];
            Action::status("renew", get_current_user_id(), $data);
        } elseif ($action == 'renew-on') {
            update_post_meta($subscrpt_id, "_subscrpt_auto_renew", 1);
        } elseif ($action == 'renew-off') {
            update_post_meta($subscrpt_id, "_subscrpt_auto_renew", 0);
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
            $data = ["post" => $subscrpt_id, "product" => $post_meta['product_id']];
            if (isset($post_meta['variation_id'])) $data['variation'] = $post_meta['variation_id'];
            Action::status($action, get_current_user_id(), $data);
        }
        echo "<script>location.href = '" . get_permalink(wc_get_page_id('myaccount')) . "view-subscrpt/" . $subscrpt_id . "';</script>";
    }

    public function RenewProduct($subscrpt_id)
    {
        $post_meta = get_post_meta($subscrpt_id, "_subscrpt_order_general", true);
        $variation_id = 0;
        if (isset($post_meta['variation_id'])) $variation_id = $post_meta['variation_id'];
        WC()->cart->add_to_cart(
            $post_meta['product_id'],
            1,
            $variation_id,
            [],
            ['renew_subscrpt' => true]
        );
        wc_add_notice(__('Product added to cart', 'sdevs_wea'), 'success');
        $this->redirect(wc_get_cart_url());
    }

    public function redirect($url)
    {
?>
        <script>
            window.location.href = '<?php echo $url; ?>';
        </script>
<?php
    }
}
