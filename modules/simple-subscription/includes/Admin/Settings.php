<?php

namespace SpringDevs\WcSubscription\Admin;

/**
 * Class Settings
 * @package SpringDevs\WcSubscription\Admin
 */
class Settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function admin_menu()
    {
        $post_type_link = 'edit.php?post_type=subscrpt_order';
        add_submenu_page($post_type_link, 'Subscription Settings', 'Settings', "manage_options", 'subscrpt_settings', [$this, 'settings_content']);
    }

    /**
     * register settings options
     **/
    public function register_settings()
    {
        register_setting('subscrpt_settings', 'subscrpt_demo');
    }

    public function settings_content()
    {
?>
        <div class="wrap">
            <?php settings_errors(); ?>
            <h1>Subscription Settings</h1>
            <p>These settings can effect Subscription</p>
            <form method="post" action="options.php">
                <?php settings_fields('subscrpt_settings'); ?>
                <?php do_settings_sections('subscrpt_settings'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="sdwac_first_time_purchase_coupon_label">
                                    <?php _e('Demo', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="sdwac_first_time_purchase_coupon_label" id="sdwac_first_time_purchase_coupon_label" required />
                                <p class="description"><?php _e('Display Label on cart', 'sdevs_wea'); ?></p>
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
