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
        register_setting('subscrpt_settings', 'subscrpt_active_role');
        register_setting('subscrpt_settings', 'subscrpt_unactive_role');
        do_action('subscrpt_register_settings', 'subscrpt_settings');
    }

    public function settings_content()
    {
?>
        <div class="wrap">
            <?php settings_errors(); ?>
            <h1><?php _e('Subscription Settings', 'sdevs_wea'); ?></h1>
            <p><?php _e('These settings can effect Subscription', 'sdevs_wea'); ?></p>
            <form method="post" action="options.php">
                <?php settings_fields('subscrpt_settings'); ?>
                <?php do_settings_sections('subscrpt_settings'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="subscrpt_active_role">
                                    <?php _e('Subscriber Default Role', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <select name="subscrpt_active_role" id="subscrpt_active_role">
                                    <?php wp_dropdown_roles(get_option('subscrpt_active_role', 'subscriber')); ?>
                                </select>
                                <p class="description"><?php _e('When a subscription is activated, either manually or after a successful purchase, new users will be assigned this role.', 'sdevs_wea'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="subscrpt_unactive_role">
                                    <?php _e('Subscriber Unactive Role', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <select name="subscrpt_unactive_role" id="subscrpt_unactive_role">
                                    <?php wp_dropdown_roles(get_option('subscrpt_unactive_role', 'customer')); ?>
                                </select>
                                <p class="description"><?php _e("If a subscriber's subscription is manually cancelled or expires, will be assigned this role.", "sdevs_wea"); ?></p>
                            </td>
                        </tr>
                        <?php do_action("subscrpt_setting_fields"); ?>
                    </tbody>
                </table>

                <?php submit_button(); ?>

            </form>
        </div>
<?php
    }
}
