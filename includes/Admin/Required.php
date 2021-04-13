<?php

namespace SpringDevs\WcEssentialAddons\Admin;

/**
 * Install & Activate required plugins
 *
 * Class Menu
 */
class Required
{
    private $plugin_file = true;

    public function __construct()
    {
        add_action('init', [$this, 'check_plugins']);
        // add_action('init', [$this, 'install_tutor_plugin']);
    }

    public function check_plugins()
    {
        $plugin_file = WP_PLUGIN_DIR . '/woocommerce/woocommerce.php';

        if (!file_exists($plugin_file)) $this->plugin_file = false;

        if (!file_exists($plugin_file) || !is_plugin_active('woocommerce/woocommerce.php')) {
            add_action('admin_notices', [$this, 'install_plugin_notice']);
        }
    }

    public function install_plugin_notice()
    {
        if ($this->plugin_file) {
            $id = 'sdevs-activate-plugin';
            $label = __('Activate Woocommerce', 'sdevs_wea');
        } else {
            $id = 'sdevs-install-plugin';
            $label = __('Install Woocommerce', 'sdevs_wea');
        }
?>
        <div class="notice notice-error sdevs-install-plugin">
            <div class="sdevs-notice-icon">
                <img src="<?php echo SDEVS_WEA_ASSETS_ASSETS . '/images/logo.png'; ?>" alt="">
            </div>
            <div class="sdevs-notice-content">
                <h2>Thanks for using Missing Addons for WooCommerce
                </h2>
                <p>You must have <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">Woocommerce </a> installed and activated on this website in order to use this plugin.</p>
                <a href="https://springdevs.com/docs/" target="_blank">Learn more</a>
            </div>
            <div class="sdevs-install-notice-button">
                <a id="<?php echo $id; ?>" class="sdevs-button sdevs-primary-button" href="javascript:void(0);"><svg xmlns="http://www.w3.org/2000/svg" class="sdevs-loading-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg> <?php echo $label; ?></a>
            </div>
        </div>
<?php
    }
}
