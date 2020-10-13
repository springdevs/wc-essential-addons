<?php

namespace springdevs\WooAdvanceCoupon\Admin;

/**
 * sdwac_setting class
 * Woocommerce Settings Tabs
 */
class sdwac_Setting
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'sdwac_coupon_menu_items']);
        add_action('admin_init', [$this, 'sdwac_coupon_register_settings']);
    }

    public function sdwac_coupon_menu_items()
    {
        $post_type_link = 'edit.php?post_type=woocoupon';
        add_submenu_page($post_type_link, 'WooCoupon settings', 'Settings', "manage_options", 'woocoupon_settings', [$this, 'woocoupon_settings_content']);
    }

    /**
     * register settings options
     **/
    public function sdwac_coupon_register_settings()
    {
        register_setting('woocoupon_settings', 'sdwac_first_time_purchase_coupon');
        register_setting('woocoupon_settings', 'sdwac_first_time_purchase_coupon_label');
        register_setting('woocoupon_settings', 'sdwac_show_product_discount');
        register_setting('woocoupon_settings', 'sdwac_multi');
        register_setting('woocoupon_settings', 'sdwac_url');
    }

    public function woocoupon_settings_content()
    {
        $args = [
            "post_type" => "woocoupon",
            'post_status' => 'publish'
        ];
        $sdwac_coupon_data = get_posts($args);
        $sdwac_coupon_coupons = ["0" => "Select Discount"];
        foreach ($sdwac_coupon_data as $data) {
            $sdwac_coupon_coupons[$data->ID] = $data->post_title;
        }
?>
        <div class="wrap">
            <?php settings_errors(); ?>
            <h1>WooCoupon Settings</h1>
            <p>These settings can effect both coupons</p>
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
                                <label for="sdwac_first_time_purchase_coupon_label">
                                    <?php _e('First Purchase coupon label', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="sdwac_first_time_purchase_coupon_label" id="sdwac_first_time_purchase_coupon_label" value="<?php echo esc_attr(get_option('sdwac_first_time_purchase_coupon_label')); ?>" required />
                                <p class="description"><?php _e('Display Label on cart', 'sdevs_wea'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="sdwac_show_product_discount">
                                    <?php _e('Show Product Discount', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <select name="sdwac_show_product_discount" id="sdwac_show_product_discount" required>
                                    <option value="yes" <?php if ('yes' == get_option("sdwac_show_product_discount")) {
                                                            echo "selected";
                                                        } ?>>Yes</option>
                                    <option value="no" <?php if ('no' == get_option("sdwac_show_product_discount")) {
                                                            echo "selected";
                                                        } ?>>No</option>
                                </select>
                                <p class="description"><?php _e('Set "no" , if you want to hide product discount', 'sdevs_wea'); ?></p>
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
