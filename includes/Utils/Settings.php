<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('Sdevs_Woo_Settings')) :

    /**
     * Settings class
     */
    class Sdevs_Woo_Settings extends \WC_Settings_Page
    {
        public function __construct()
        {
            $this->id    = 'wcma';
            $this->label = __('WCMA Settings', 'my-textdomain');

            add_filter('woocommerce_settings_tabs_array',        array($this, 'add_settings_page'), 20);
            add_action('woocommerce_settings_' . $this->id,      array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
            add_action('woocommerce_sections_' . $this->id,      array($this, 'output_sections'));
        }

        public function get_sections()
        {
            $sections = [];

            return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
        }
    }

endif;

return new Sdevs_Woo_Settings();
