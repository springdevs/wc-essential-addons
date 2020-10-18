<?php

namespace SpringDevs\WcSubscription\Admin;

/**
 * Product class
 * @package SpringDevs\WcSubscription\Admin
 */
class Product
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, "enqueue_assets"));
        add_filter("product_type_options", array($this, "add_product_type_options"));
        add_action('woocommerce_product_options_general_product_data', array($this, "subscription_forms"));
        add_action('save_post_product', array($this, "save_subscrpt_data"));
    }

    public function enqueue_assets()
    {
        wp_enqueue_script("sdevs_subscription_admin");
    }

    public function add_product_type_options($product_type_options)
    {
        $screen = get_current_screen();
        $value = "no";
        if ($screen->parent_base == "edit") {
            $post_meta = get_post_meta(get_the_ID(), 'subscrpt_general', true);
            $value = !empty($post_meta) && $post_meta["enable"] ? "yes" : "no";
        }

        $product_type_options["subscrpt_enable"] = [
            "id"            => "subscrpt_enable",
            "wrapper_class" => "show_if_simple",
            "label"         => __("Subscription", "sdevs_wea"),
            "description"   => __("Enable Subscriptions", "sdevs_wea"),
            "default"       => $value,
        ];

        return $product_type_options;
    }

    public function subscription_forms()
    {
        $timing_types = [
            "days" => "Daily",
            "weeks" => "Weekly",
            "months" => "Monthly",
            "years" => "Yealy"
        ];
        $subscrpt_timing = null;
        $subscrpt_cart_txt = "subscribe";
        $subscrpt_user_cancell = "yes";

        $screen = get_current_screen();
        if ($screen->parent_base == "edit") {
            $post_meta = get_post_meta(get_the_ID(), 'subscrpt_general', true);
            if (!empty($post_meta) && is_array($post_meta)) {
                $subscrpt_timing = $post_meta["type"];
                $subscrpt_cart_txt = $post_meta["cart_txt"];
                $subscrpt_user_cancell = $post_meta['user_cancell'];
            }
        }
?>
        <div class="option_group show_if_subscription hide" style="padding: 10px;">
            <strong style="margin: 10px;"><?php _e("Subscription Settings", "sdevs_wea"); ?></strong>
            <?php

            woocommerce_wp_select([
                "id" => "subscrpt_timing",
                "label" => __('Users will pay', 'sdevs_wea'),
                "value" => $subscrpt_timing,
                "options" => $timing_types,
                "description" => __('Set the length of each recurring subscription period to daily, weekly, monthly or annually.', 'sdevs_wea'),
                "desc_tip" => true
            ]);

            woocommerce_wp_text_input([
                "id" => "subscrpt_cart_txt",
                "label" => __('Add to Cart Text', 'sdevs_wea'),
                "type" => "text",
                "value" => $subscrpt_cart_txt,
                "description" => __('change Add to Cart Text default is "subscribe"', 'sdevs_wea'),
                "desc_tip" => true
            ]);

            woocommerce_wp_select([
                "id" => "subscrpt_user_cancell",
                "label" => __('Can User Cancell', 'sdevs_wea'),
                "value" => $subscrpt_user_cancell,
                "options" => [
                    'yes' => __('Yes', 'sdevs_wea'),
                    'no' => __('No', 'sdevs_wea'),
                ],
                "description" => __('if "Yes",then user can be cancelled."No" means cannot do this !!', 'sdevs_wea'),
                "desc_tip" => true
            ]);
            ?>
        </div>
<?php
    }

    public function save_subscrpt_data($post_id)
    {
        if (!isset($_POST['subscrpt_enable'])) return;
        $subscrpt_enable = $_POST["subscrpt_enable"] ? true : false;
        $subscrpt_time = 1;
        $subscrpt_timing = sanitize_text_field($_POST["subscrpt_timing"]);
        $subscrpt_trial_time = null;
        $subscrpt_trial_timing = null;
        $subscrpt_cart_txt = sanitize_text_field($_POST["subscrpt_cart_txt"]);
        $subscrpt_user_cancell = sanitize_text_field($_POST["subscrpt_user_cancell"]);
        $data = [
            "enable" => $subscrpt_enable,
            "time" => $subscrpt_time,
            "type" => $subscrpt_timing,
            "trial_time" => $subscrpt_trial_time,
            "trial_type" => $subscrpt_trial_timing,
            "cart_txt" => $subscrpt_cart_txt,
            "user_cancell" => $subscrpt_user_cancell
        ];
        update_post_meta($post_id, "subscrpt_general", $data);
    }
}
