<?php

namespace springdevs\custompft;

/**
 * 
 * Handle Ajax Requests
 * 
 * */
class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_custompft_get_data', [$this, 'custompft_get_data']);
    }

    public function custompft_get_data()
    {
        $post_id = (int)$_POST["post_id"];
        if (is_int($post_id)) {
            $faqs = get_post_meta($post_id, "custompft_faqs", true);
            wp_send_json($faqs);
        }
    }
}
