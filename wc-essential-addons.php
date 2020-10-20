<?php
/*
Plugin Name: Missing Addons for WooCommerce
Plugin URI: https://wordpress.org/plugins/wc-essential-addons/
Description: Supercharge your WooCommerce powered store!
Version: 1.0.3
Author: SpringDevs
Author URI: https://springdevs.com/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: sdevs_wea
Domain Path: /languages
*/

/**
 * Copyright (c) 2020 SpringDevs (email: contact@springdevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if (!defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once __DIR__ . '/vendor/autoload.php';

/**
 * sdevs_wea_Main class
 *
 * @class sdevs_wea_Main The class that holds the entire sdevs_wea_Main plugin
 */
final class sdevs_wea_Main
{
    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.3';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the Springdevs_Wma_Main class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct()
    {
        $this->define_constants();
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        $this->run_first();
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes the Springdevs_Wma_Main() class
     *
     * Checks for an existing Springdevs_Wma_Main() instance
     * and if it doesn't find one, creates it.
     *
     * @return sdevs_wea_Main|bool
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new sdevs_wea_Main();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container)) {
            return $this->container[$prop];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants()
    {
        define('SDEVS_WEA_ASSETS_VERSION', self::version);
        define('SDEVS_WEA_ASSETS_FILE', __FILE__);
        define('SDEVS_WEA_ASSETS_PATH', dirname(SDEVS_WEA_ASSETS_FILE));
        define('SDEVS_WEA_ASSETS_INCLUDES', SDEVS_WEA_ASSETS_PATH . '/includes');
        define('SDEVS_WEA_ASSETS_URL', plugins_url('', SDEVS_WEA_ASSETS_FILE));
        define('SDEVS_WEA_ASSETS_ASSETS', SDEVS_WEA_ASSETS_URL . '/assets');
    }

    /**
     * Load the plugin after all plugins are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->checkPlugin();
        $this->includes();
        $this->init_hooks();
        $this->freemius_setup();
        do_action('sdevs_maw_loaded');
    }

    public function freemius_setup()
    {
        global $sdevs_maw;
        if (!isset($sdevs_maw)) {
            require_once dirname(__FILE__) . '/freemius/start.php';
            $sdevs_maw = fs_dynamic_init(array(
                'id'                  => '6934',
                'slug'                => 'httpswordpressorgpluginswc-essential-addons',
                'type'                => 'plugin',
                'public_key'          => 'pk_041cc3b1a6d4418773fc2a50b050a',
                'is_premium'          => true,
                'premium_suffix'      => 'Single Site',
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'springdevs-modules',
                    'contact'        => false,
                    'support'        => false,
                ),
                'secret_key'          => 'sk_WnMg=bfmVfSt&aI}^Jwo{e3KEdHpq',
            ));
        }
        return $sdevs_maw;
    }

    /**
     * run these code always first
     */
    public function run_first()
    {
        new \SpringDevs\WcEssentialAddons\Update();
        $this->getModules();
    }

    /**
     * Check if WooCommerce Exixts
     */
    public function checkPlugin()
    {
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            deactivate_plugins(plugin_basename(__FILE__));
            add_action('admin_notices', [$this, 'deactivation_notice']);
        }
    }

    /**
     * Display Deactivation Notices
     **/
    public function deactivation_notice()
    {
        echo '<div class="notice notice-error is-dismissible">
             <p><small><code>Missing Addons for WooCommerce </code></small> plugin is <b>Deactivated !!</b> It\'s require <small><code>WooCommerce</code></small> plugin</p>
         </div>';
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate()
    {
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            wp_die("Woocommerce is not activated !!", "Require plugin is not activated");
            exit;
        }
        $installer = new \SpringDevs\WcEssentialAddons\Installer();
        $installer->run();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate()
    {
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        if ($this->is_request('admin')) {
            $this->container['admin'] = new \SpringDevs\WcEssentialAddons\Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new \SpringDevs\WcEssentialAddons\Frontend();
        }

        if ($this->is_request('ajax')) {
            // require_once SPRINGDEVS_WMA_ASSETS_INCLUDES . '/class-ajax.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks()
    {
        add_action('init', [$this, 'init_classes']);

        // Localize our plugin
        add_action('init', [$this, 'localization_setup']);

        add_action('init', [$this, 'modules_setup']);
    }

    /**
     * Modules setup
     **/
    public function modules_setup()
    {
        $this->setModules();
    }

    /**
     * set Addons
     **/
    public function setModules()
    {
        $modules = [
            "simple-subscription" => [
                "name" => "Subscription",
                "desc" => __("Allow your customers to order once and get their products and services every month/week.", "sdevs_wea"),
                "class" => "Sdevs_Wc_Subscription",
                "file_path" => __DIR__ . '/modules/simple-subscription/simple-subscription.php'
            ],
            "simple-booking" => [
                "name" => "Booking",
                "desc" => __("Show available dates, time in a simple dropdown, take booking for products and services.", "sdevs_wea"),
                "class" => "Sdevs_Wc_Booking",
                "file_path" => __DIR__ . '/modules/simple-booking/simple-booking.php'
            ],
            "Woo-Advanced-Coupon" => [
                "name" => "Coupons",
                "desc" => __(" Create gift vouchers, store credits, special discounts based on the amount spent, etc.", "sdevs_wea"),
                "class" => "sdwac_coupon_main",
                "file_path" =>  __DIR__ . '/modules/Woo-Advanced-Coupon/woo-advance-coupon.php'
            ],
            "easy-gmap" => [
                "name" => "Google Map",
                "desc" => __("Embed a Google Map on your site to show your store location.", "sdevs_wea"),
                "class" => "Sdevs_Easy_Gmap",
                "file_path" => __DIR__ . '/modules/easy-gmap/easy-gmap.php'
            ],
            "product-faq-tab" => [
                "name" => "FAQ Tabs",
                "desc" => __("Show frequently asked questions in a nice and organized fashion.", "sdevs_wea"),
                "class" => "Sdevs_Custompft_Main",
                "file_path" => __DIR__ . '/modules/product-faq-tab/product-faq-tab.php'
            ],
        ];
        $filter_modules = apply_filters("sdevs_wma_modules", $modules);
        update_option("sdevs_wea_modules", $filter_modules);
    }

    /**
     * Get All Addons
     **/
    public function getModules()
    {
        $active_modules = get_option("sdevs_wea_activated_modules", []);
        foreach ($active_modules as $key => $value) {
            if (file_exists($value['file_path'])) {
                include_once $value['file_path'];
            }
        }
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new WpAdroit\springdevs_wma\Ajax();
        }

        $this->container['api']    = new \SpringDevs\WcEssentialAddons\Api();
        $this->container['assets'] = new \SpringDevs\WcEssentialAddons\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup()
    {
        load_plugin_textdomain('sdevs_wea', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /**
     * What type of request is this?
     *
     * @param string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();

            case 'ajax':
                return defined('DOING_AJAX');

            case 'rest':
                return defined('REST_REQUEST');

            case 'cron':
                return defined('DOING_CRON');

            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
        }
    }
} // sdevs_wea_Main

/**
 * Initialize the main plugin
 *
 * @return \sdevs_wea_Main|bool
 */
function sdevs_wea_main()
{
    return sdevs_wea_Main::init();
}

/**
 *  kick-off the plugin
 */
sdevs_wea_main();
