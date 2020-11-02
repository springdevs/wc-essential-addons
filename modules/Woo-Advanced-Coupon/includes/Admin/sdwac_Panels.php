<?php

namespace springdevs\WooAdvanceCoupon\Admin;

/**
 * sdwac_Panels class
 * Woocommerce Custom Tabs
 */
class sdwac_Panels
{
    public function __construct()
    {
        add_filter('woocommerce_coupon_data_tabs', [$this, 'coupon_data_tabs'], 100, 1);
        add_filter('woocommerce_coupon_data_panels', [$this, 'coupon_tabs_screen']);
        add_action('save_post', [$this, 'save_coupon_data']);
    }

    public function save_coupon_data($post_id)
    {
        if (isset($_POST["post_type"])) {
            if (!isset($_POST["sdwac_coupon_feature"]) & $_POST["post_type"] != "shop_coupon") {
                return;
            }
            $woocoupon_id = sanitize_text_field($_POST["sdwac_coupon_feature"]);
            $overwrite_discount = isset($_POST["sdwac_overwrite_discount"]) ? true : false;
            $url_coupon = isset($_POST["sdwac_url_coupon"]) ? true : false;
            $auto_coupon = isset($_POST["sdwac_auto_coupon"]) ? true : false;
            $sdwac_coupon_data = [
                "list_id" => $woocoupon_id,
                "overwrite_discount" => $overwrite_discount,
                "url_coupon" => $url_coupon,
                "auto_coupon" => $auto_coupon
            ];
            update_post_meta($post_id, "sdwac_coupon_panel", $sdwac_coupon_data);
        }
    }

    public function coupon_data_tabs($tabs)
    {
        $tabs['sdwac_coupon_features'] = array(
            'label'     => __('Woo Coupon', 'sdevs_wea'),
            'class'  => 'sdwac_coupon_coupon_panel',
            'target'     => 'sdwac_coupon_tabs_screen'
        );
        return $tabs;
    }

    public function coupon_tabs_screen()
    {
        $woocoupon = null;
        $url_coupon = "yes";
        $auto_coupon = "yes";
        $overwrite_discount = "yes";
        $post_meta = get_post_meta(get_the_ID(), 'sdwac_coupon_panel', true);
        if (!empty($post_meta) && is_array($post_meta)) {
            $woocoupon = $post_meta['list_id'];
            $url_coupon = $post_meta['url_coupon'] ? "yes" : false;
            $auto_coupon = $post_meta['auto_coupon'] ? "yes" : false;
            $overwrite_discount = $post_meta['overwrite_discount'] ? "yes" : false;
        }
?>
        <div id="sdwac_coupon_tabs_screen" class="panel woocommerce_options_panel">
            <?php
            $args = [
                "post_type" => "woocoupon",
                'post_status' => 'publish'
            ];
            $posts = get_posts($args);
            $options = [];
            $options[null] = "Select Coupon";
            foreach ($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
            woocommerce_wp_select([
                "id" => "sdwac_coupon_feature",
                "label" => "Coupon Feature",
                "options" => $options,
                "value" => $woocoupon,
                "type" => "text"
            ]);

            woocommerce_wp_checkbox([
                "id" => "sdwac_url_coupon",
                "label" => __("Coupon By URL", "sdevs_wea"),
                "value" => "yes",
                "cbvalue" => $url_coupon
            ]);

            woocommerce_wp_checkbox([
                "id" => "sdwac_auto_coupon",
                "label" => __("Automatic Apply", "sdevs_wea"),
                "value" => "yes",
                "cbvalue" => $auto_coupon
            ]);

            woocommerce_wp_checkbox([
                "id" => "sdwac_overwrite_discount",
                "label" => __("Overwrite Discount", "sdevs_wea"),
                "value" => "yes",
                "cbvalue" => $overwrite_discount
            ]);
            ?>
        </div>
<?php
    }
}
