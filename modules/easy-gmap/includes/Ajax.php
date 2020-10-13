<?php

namespace springdevs\EasyGmap;

/**
 * The ajax class
 */
class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_get_gmap_data', [$this, "get_map_data"]);
    }

    public function get_map_data()
    {
        $post_id = (int)$_POST["post_id"];
        $post_metas = get_post_meta($post_id, "gmap_maps", true);
        $settings = get_post_meta($post_id, "gmap_in_settings", true);
        $location_data = [];
        foreach ($post_metas as $location_id) {
            $location_title = get_post($location_id)->post_title;
            $locations = get_post_meta($location_id, "gmap_locations", true);
            array_push($location_data, [$location_title, $locations]);
        }
        $final_data = ["map_data" => $location_data, "settings" => $settings];
        wp_send_json($final_data);
    }
}
