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
            ["label" => __("days", "sdevs_wea"), "value" => "days"],
            ["label" => __("weeks", "sdevs_wea"), "value" => "weeks"],
            ["label" => __("months", "sdevs_wea"), "value" => "months"],
            ["label" => __("years", "sdevs_wea"), "value" => "years"],
        ];
        $subscrpt_time = 1;
        $subscrpt_timing = null;
        $subscrpt_trial_time = null;
        $subscrpt_trial_timing = null;
        $subscrpt_cart_txt = "subscribe";
        $subscrpt_user_cancell = "yes";

        $screen = get_current_screen();
        if ($screen->parent_base == "edit") {
            $post_meta = get_post_meta(get_the_ID(), 'subscrpt_general', true);
            if (!empty($post_meta) && is_array($post_meta)) {
                $subscrpt_time = $post_meta['time'];
                $subscrpt_timing = $post_meta["type"];
                $subscrpt_trial_time = $post_meta["trial_time"];
                $subscrpt_trial_timing = $post_meta["trial_type"];
                $subscrpt_cart_txt = $post_meta["cart_txt"];
                $subscrpt_user_cancell = $post_meta['user_cancell'];
            }
        }
?>
        <div class="option_group show_if_subscription" style="padding: 10px;">
            <strong style="margin: 10px;"><?php _e("Subscription Settings", "sdevs_wea"); ?></strong>
            <p class="form-field subscrpt_time_field">
                <label for="subscrpt_time"><?php _e("Users will pay every", "sdevs_wea"); ?></label>
                <input type="number" class="short" style="width: 48%;margin-right: 5px;" name="subscrpt_time" id="subscrpt_time" value="<?php echo $subscrpt_time; ?>" min="1" />
                <select style="width: 48%;" name="subscrpt_timing" id="subscrpt_timing">
                    <?php foreach ($timing_types as $timing_type) : ?>
                        <option value="<?php echo $timing_type["value"]; ?>" <?php if ($subscrpt_timing == $timing_type["value"]) echo "selected"; ?>><?php echo $timing_type["label"]; ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="description"><?php _e('Set the length of each recurring subscription period to daily, weekly, monthly or annually.', 'sdevs_wea'); ?></small>
            </p>
            <p class="form-field subscrpt_trial_field">
                <label for="subscrpt_trial_time"><?php _e("Offer a free trial of", "sdevs_wea"); ?></label>
                <input type="number" class="short" style="width: 48%;margin-right: 5px;" name="subscrpt_trial_time" id="subscrpt_trial_time" value="<?php echo $subscrpt_trial_time; ?>" />
                <select style="width: 48%;" name="subscrpt_trial_timing" id="subscrpt_trial_timing">
                    <?php foreach ($timing_types as $timing_type) : ?>
                        <option value="<?php echo $timing_type["value"]; ?>" <?php if ($subscrpt_trial_timing == $timing_type["value"]) echo "selected"; ?>><?php echo $timing_type["label"]; ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="description"><?php _e('You can offer a free trial of this subscription. In this way the user can purchase the subscription and will pay when the trial period expires.', 'sdevs_wea'); ?></small>
            </p>
            <?php
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
        if (!isset($_POST['subscrpt_time'])) return;
        $subscrpt_enable = $_POST["subscrpt_enable"] ? true : false;
        $subscrpt_time = sanitize_text_field($_POST["subscrpt_time"]);
        $subscrpt_timing = sanitize_text_field($_POST["subscrpt_timing"]);
        $subscrpt_trial_time = sanitize_text_field($_POST["subscrpt_trial_time"]);
        $subscrpt_trial_timing = sanitize_text_field($_POST["subscrpt_trial_timing"]);
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
