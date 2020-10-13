<?php

namespace springdevs\EasyGmap;

/**
 * Scripts and Styles Class
 */
class Assets
{
    /**
     * Assets constructor.
     */
    function __construct()
    {
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'register'], 5);
        } else {
            add_action('wp_enqueue_scripts', [$this, 'register'], 5);
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register()
    {
        $this->register_scripts($this->get_scripts());
        $this->register_styles($this->get_styles());
    }

    /**
     * Register scripts
     *
     * @param array $scripts
     *
     * @return void
     */
    private function register_scripts($scripts)
    {
        foreach ($scripts as $handle => $script) {
            $deps      = isset($script['deps']) ? $script['deps'] : false;
            $in_footer = isset($script['in_footer']) ? $script['in_footer'] : false;
            $version   = isset($script['version']) ? $script['version'] : SDEVS_EASYGMAP_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, $in_footer);
        }
    }

    /**
     * Register styles
     *
     * @param array $styles
     *
     * @return void
     */
    public function register_styles($styles)
    {
        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;

            wp_register_style($handle, $style['src'], $deps, SDEVS_EASYGMAP_VERSION);
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        $plugin_js_assets_path = SDEVS_EASYGMAP_ASSETS . '/js/';
        $map_url = "https://maps.googleapis.com/maps/api/js?key=" . get_option("gmap_api_key") . "&libraries=places";

        $scripts = [
            "google-map" => [
                "src" => $map_url,
                "in_footer" => true
            ],
            "autocomplete-js" => [
                "src" => $plugin_js_assets_path . "autocomplete.js",
                "in_footer" => true
            ],
            "gmaps-js" => [
                "src" => $plugin_js_assets_path . "gmaps.js",
                "in_footer" => true
            ]
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles()
    {
        $plugin_css_assets_path = SDEVS_EASYGMAP_ASSETS . '/css/';

        $styles = [];

        return $styles;
    }
}
