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
        add_action('save_post_shop_coupon', [$this, 'save_coupon_data']);
    }

    public function save_coupon_data($post_id)
    {
        $url_coupon = isset($_POST["sdwac_url_coupon"]) ? true : false;
        $auto_coupon = isset($_POST["sdwac_auto_coupon"]) ? true : false;
        $sdwac_coupon_data = [
            "url_coupon" => $url_coupon,
            "auto_coupon" => $auto_coupon
        ];
        update_post_meta($post_id, "sdwac_coupon_panel", $sdwac_coupon_data);
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
        $url_coupon = "yes";
        $auto_coupon = "yes";
        $post_meta = get_post_meta(get_the_ID(), 'sdwac_coupon_panel', true);
        $coupon_meta = get_post_meta(get_the_ID(), '_sdwac_coupon_meta', true);
        $product_list_type = null;
        if (!empty($coupon_meta) && is_array($coupon_meta)) {
            $product_list_type = isset($coupon_meta['list']) ? $coupon_meta['list'] : null;
        }
        if (!empty($post_meta) && is_array($post_meta)) {
            $url_coupon = $post_meta['url_coupon'] ? "yes" : false;
            $auto_coupon = $post_meta['auto_coupon'] ? "yes" : false;
        }
?>
        <div id="sdwac_coupon_tabs_screen" class="panel woocommerce_options_panel">
            <input type="hidden" name="sdwac_coupon_admin_nonce" value="<?php echo wp_create_nonce('sdwac_coupon_admin_nonce'); ?>">
            <?php

            woocommerce_wp_select([
                "id" => "sdwac_product_lists",
                "label" => "Product Lists",
                "options" => [
                    'inList' => 'In List',
                    'noList' => 'Not In List',
                ],
                "value" => $product_list_type
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
            ?>
        </div>
<?php
    }
}
