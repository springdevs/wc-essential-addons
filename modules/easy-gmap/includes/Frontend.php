<?php

namespace springdevs\EasyGmap;

use springdevs\EasyGmap\Frontend\Shortcode;

/**
 * Frontend handler class
 */
class Frontend
{
    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
        new Shortcode;
    }

    public function register_scripts()
    {
        wp_localize_script(
            'gmaps-js',
            'gmap_helper_obj',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
        wp_enqueue_script("google-map");
        wp_enqueue_script('gmaps-js');
    }
}
