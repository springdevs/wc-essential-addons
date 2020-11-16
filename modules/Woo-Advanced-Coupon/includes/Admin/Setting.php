<?php

namespace springdevs\WooAdvanceCoupon\Admin;

/**
 * Setting class
 * Woocommerce Settings Tabs
 */
class Setting
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'setting_menu_item']);
        add_action('admin_init', [$this, 'coupon_register_settings']);
    }

    public function setting_menu_item()
    {
        add_options_page('WooCoupon settings', 'WooCoupon settings', 'manage_options', 'woocoupon_settings', [$this, 'settings_content']);
    }

    /**
     * register settings options
     **/
    public function coupon_register_settings()
    {
        register_setting('woocoupon_settings', 'sdwac_first_time_purchase_coupon');
        register_setting('woocoupon_settings', 'sdwac_price_cut_from');
        register_setting('woocoupon_settings', 'sdwac_multi');
        register_setting('woocoupon_settings', 'sdwac_url');
    }

    public function settings_content()
    {
        $args = array(
            'posts_per_page' => -1,
            'order'          => 'asc',
            'post_type'      => 'shop_coupon',
            'post_status'    => 'publish',
        );
        $coupons     = get_posts($args);
        $sdwac_coupon_coupons = ["0" => "Select Discount"];
        foreach ($coupons as $data) $sdwac_coupon_coupons[$data->ID] = $data->post_title;
?>
        <div class="wrap">
            <h1><?php _e('WooCoupon Settings', 'sdevs_wea'); ?></h1>
            <p><?php _e('These settings can effect both coupons', 'sdevs_wea'); ?></p>
            <form method="post" action="options.php">
                <?php settings_fields('woocoupon_settings'); ?>
                <?php do_settings_sections('woocoupon_settings'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="sdwac_first_time_purchase_coupon">
                                    <?php _e('Coupon for first Purchase', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td class="forminp forminp-select">
                                <select name="sdwac_first_time_purchase_coupon" id="sdwac_first_time_purchase_coupon" required>
                                    <?php foreach ($sdwac_coupon_coupons as $key => $value) : ?>
                                        <option value="<?php echo $key; ?>" <?php if ($key == get_option("sdwac_first_time_purchase_coupon")) {
                                                                                echo "selected";
                                                                            } ?>><?php echo $value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('Select a discount from here which you want to enable for new customers', 'sdevs_wea'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sdwac_price_cut_from">
                                    <?php _e('Price Cut From', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <select name="sdwac_price_cut_from" id="sdwac_price_cut_from" required>
                                    <option value="regular" <?php if ('regular' == get_option("sdwac_price_cut_from")) {
                                                                echo "selected";
                                                            } ?>>Regular price</option>
                                    <option value="sale" <?php if ('sale' == get_option("sdwac_price_cut_from")) {
                                                                echo "selected";
                                                            } ?>>Sale price</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sdwac_multi">
                                    <?php _e('Multi Coupon', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <select name="sdwac_multi" id="sdwac_multi" required>
                                    <option value="yes" <?php if ('yes' == get_option("sdwac_multi")) {
                                                            echo "selected";
                                                        } ?>>Yes</option>
                                    <option value="no" <?php if ('no' == get_option("sdwac_multi")) {
                                                            echo "selected";
                                                        } ?>>No</option>
                                </select>
                                <p class="description"><?php _e('Set "no" , if you never want to apply Multi coupon in cart', 'sdevs_wea'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sdwac_url">
                                    <?php _e('Coupon Url slug Name', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="sdwac_url" id="sdwac_url" value="<?php echo esc_attr(get_option('sdwac_url')); ?>" required />
                                <p class="description"><?php echo get_home_url() . '/?<b>' . get_option('sdwac_url') . '</b>=coupon_code'; ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
<?php
    }
}
