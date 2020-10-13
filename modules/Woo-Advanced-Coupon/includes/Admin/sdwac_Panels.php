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
        add_filter('woocommerce_coupon_data_tabs', [$this, 'sdwac_coupon_data_tabs'], 100, 1);
        add_filter('woocommerce_coupon_data_panels', [$this, 'sdwac_coupon_tabs_screen']);
        add_action('save_post', [$this, 'sdwac_coupon_save_coupon_data']);
    }

    public function sdwac_coupon_save_coupon_data($post_id)
    {
        if (isset($_POST["post_type"])) {
            if (!isset($_POST["sdwac_coupon_feature"]) & $_POST["post_type"] != "shop_coupon") {
                return;
            }
            $sdwac_coupon_data = [
                "list_id" => sanitize_text_field($_POST["sdwac_coupon_feature"]),
                "overwrite_discount" => sanitize_text_field($_POST["overwrite_discount"])
            ];
            update_post_meta($post_id, "sdwac_coupon_panel", $sdwac_coupon_data);
        }
    }

    public function sdwac_coupon_data_tabs($tabs)
    {
        $tabs['sdwac_coupon_features'] = array(
            'label'     => __('Woo Coupon', 'sdevs_wea'),
            'class'  => 'sdwac_coupon_coupon_panel',
            'target'     => 'sdwac_coupon_tabs_screen'
        );
        return $tabs;
    }

    public function sdwac_coupon_tabs_screen()
    {
?>
        <div id="sdwac_coupon_tabs_screen" class="panel woocommerce_options_panel">
            <div id="post">
                <supertabs />
            </div>
        </div>
<?php
    }
}
