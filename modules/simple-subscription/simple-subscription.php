<?php

// don't call the file directly
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Sdevs_Wc_Subscription class
 *
 * @class Sdevs_Wc_Subscription The class that holds the entire Sdevs_Wc_Subscription plugin
 */
final class Sdevs_Wc_Subscription
{
    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the Sdevs_Wc_Subscription class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct()
    {
        $this->define_constants();

        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    /**
     * Initializes the Sdevs_Wc_Subscription() class
     *
     * Checks for an existing Sdevs_Wc_Subscription() instance
     * and if it doesn't find one, creates it.
     *
     * @return Sdevs_Wc_Subscription|bool
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new Sdevs_Wc_Subscription();
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
        define('WCSUBSCRIPTION_ASSETS_VERSION', self::version);
        define('WCSUBSCRIPTION_ASSETS_FILE', __FILE__);
        define('WCSUBSCRIPTION_ASSETS_PATH', dirname(WCSUBSCRIPTION_ASSETS_FILE));
        define('WCSUBSCRIPTION_ASSETS_INCLUDES', WCSUBSCRIPTION_ASSETS_PATH . '/includes');
        define('WCSUBSCRIPTION_TEMPLATES', WCSUBSCRIPTION_ASSETS_PATH . '/templates/');
        define('WCSUBSCRIPTION_ASSETS_URL', plugins_url('', WCSUBSCRIPTION_ASSETS_FILE));
        define('WCSUBSCRIPTION_ASSETS_ASSETS', WCSUBSCRIPTION_ASSETS_URL . '/assets');
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes()
    {
        if ($this->is_request('admin')) {
            $this->container['admin'] = new SpringDevs\WcSubscription\Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new SpringDevs\WcSubscription\Frontend();
        }

        if ($this->is_request('ajax')) {
            // require_once WCSUBSCRIPTION_ASSETS_INCLUDES . '/class-ajax.php';
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
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        if ($this->is_request('ajax')) {
            // $this->container['ajax'] =  new SpringDevs\WcSubscription\Ajax();
        }

        $this->container['api']    = new SpringDevs\WcSubscription\Api();
        $this->container['assets'] = new SpringDevs\WcSubscription\Assets();
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
} // Sdevs_Wc_Subscription

/**
 * Initialize the main plugin
 *
 * @return \Sdevs_Wc_Subscription|bool
 */
function sdevs_wc_subscription()
{
    return Sdevs_Wc_Subscription::init();
}

/**
 *  kick-off the plugin
 */
sdevs_wc_subscription();
