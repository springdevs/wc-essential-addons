<?php

namespace SpringDevs\WcEssentialAddons;

/**
 * Ajax Handler
 */
class Ajax
{

    public function __construct()
    {
        add_action('wp_ajax_install_woocommerce_plugin', [$this, 'install_woocommerce_plugin']);
        add_action('wp_ajax_activate_woocommerce_plugin', [$this, 'activate_woocommerce_plugin']);
    }

    public function install_woocommerce_plugin()
    {

        include(ABSPATH . 'wp-admin/includes/plugin-install.php');
        include(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
        include_once(ABSPATH . 'wp-admin/includes/file.php');
        include_once(ABSPATH . 'wp-admin/includes/misc.php');

        if (!class_exists('Plugin_Upgrader')) {
            include(ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php');
        }
        if (!class_exists('Plugin_Installer_Skin')) {
            include(ABSPATH . 'wp-admin/includes/class-plugin-installer-skin.php');
        }

        $plugin = 'woocommerce';

        $api = plugins_api('plugin_information', array(
            'slug' => $plugin,
            'fields' => array(
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ),
        ));

        if (is_wp_error($api)) {
            wp_die($api);
        }

        $title = sprintf(__('Installing Plugin: %s'), $api->name . ' ' . $api->version);
        $nonce = 'install-plugin_' . $plugin;
        $url = 'update.php?action=install-plugin&plugin=' . urlencode($plugin);

        $upgrader = new \Plugin_Upgrader(new \Plugin_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api')));
        $upgrader->install($api->download_link);
        wp_send_json([
            'msg' => 'Installed successfully !!'
        ]);
    }

    public function activate_woocommerce_plugin()
    {
        activate_plugin('woocommerce/woocommerce.php');
        wp_send_json([
            'msg' => 'Activated successfully !!'
        ]);
    }
}
