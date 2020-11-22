<?php

// don't call the file directly
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Sdevs_Easy_Gmap class
 *
 * @class Sdevs_Easy_Gmap The class that holds the entire Easy_Gmap plugin
 */
final class Sdevs_Easy_Gmap
{
    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.1';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the Sdevs_Easy_Gmap class
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
     * Initializes the Easy_Gmap() class
     *
     * Checks for an existing Easy_Gmap() instance
     * and if it doesn't find one, creates it.
     *
     * @return Easy_Gmap|bool
     */
    public static function init()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new Sdevs_Easy_Gmap();
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
        define('SDEVS_EASYGMAP_VERSION', self::version);
        define('SDEVS_EASYGMAP_FILE', __FILE__);
        define('SDEVS_EASYGMAP_PATH', dirname(SDEVS_EASYGMAP_FILE));
        define('SDEVS_EASYGMAP_INCLUDES', SDEVS_EASYGMAP_PATH . '/includes');
        define('SDEVS_EASYGMAP_URL', plugins_url('', SDEVS_EASYGMAP_FILE));
        define('SDEVS_EASYGMAP_ASSETS', SDEVS_EASYGMAP_URL . '/assets');
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
            $this->container['admin'] = new \springdevs\EasyGmap\Admin();
        }

        if ($this->is_request('frontend')) {
            $this->container['frontend'] = new \springdevs\EasyGmap\Frontend();
        }

        if ($this->is_request('ajax')) {
            // require_once EASY_GMAP_INCLUDES . '/class-ajax.php';
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
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes()
    {
        if ($this->is_request('ajax')) {
            $this->container['ajax'] =  new \springdevs\EasyGmap\Ajax();
        }
        $this->container['assets'] = new \springdevs\EasyGmap\Assets();
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
} // Easy_Gmap

/**
 * Initialize the main plugin
 *
 * @return \Easy_Gmap|bool
 */
function sdevs_easy_gmap()
{
    return Sdevs_Easy_Gmap::init();
}

/**
 *  kick-off the plugin
 */
sdevs_easy_gmap();
