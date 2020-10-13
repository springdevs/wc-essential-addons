<?php

namespace springdevs\EasyGmap\Admin;

/**
 * The Settings class
 */
class Settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'gmap_setting_page']);
        add_action('admin_init', [$this, 'gmap_register_settings']);
    }

    public function gmap_setting_page()
    {
        add_submenu_page('gmaps', 'Gmap Settings', 'Settings', "manage_options", 'gmap_settings', [$this, 'gmap_settings_content']);
    }

    public function gmap_register_settings()
    {
        register_setting('gmap_settings', 'gmap_api_key');
    }

    public function gmap_settings_content()
    {
?>
        <div class="wrap">
            <?php settings_errors(); ?>
            <h1>Gmap Settings</h1>
            <p>Put Your Google API keys</p>
            <form method="post" action="options.php">
                <?php settings_fields('gmap_settings'); ?>
                <?php do_settings_sections('gmap_settings'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="gmap_api_key">
                                    <?php _e('Google Map API Key', 'sdevs_wea'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" name="gmap_api_key" id="gmap_api_key" value="<?php echo esc_attr(get_option('gmap_api_key')); ?>" required />
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
