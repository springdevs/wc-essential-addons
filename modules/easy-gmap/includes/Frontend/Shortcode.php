<?php

namespace springdevs\EasyGmap\Frontend;

/**
 * Class Shortcode
 * @package EasyGmap\Gmap\Frontend
 */
class Shortcode
{

    public function __construct()
    {
        add_shortcode('EasyGmap', [$this, 'render_maps']);
        add_shortcode('EasyLocation', [$this, 'render_locations']);
    }

    /**
     * Render maps
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function render_maps($atts, $content = '')
    {
        $post_id = $atts["id"];
        return '
        <input type="hidden" class="post_id" value="' . $post_id . '" />
        <div id="map-' . $post_id . '"></div>
        ';
    }

    /**
     * Render Location Lists
     *
     * @param array $atts
     * @param string $content
     *
     * @return string
     */
    public function render_locations($atts, $content = '')
    {
        if (isset($atts["class"])) {
            $class = $atts["class"];
        } else {
            $class = "gmap-location-lists";
        }
        $post_id = $atts["id"];
        $locations = [];
        $map_locations = get_post_meta($post_id, "gmap_maps", true);
        foreach ($map_locations as $map_location) {
            $location = get_post($map_location);
            array_push($locations, [
                "id" => $map_location,
                "title" => $location->post_title
            ]);
        }
        $data_html = "<ul class='" . $class . "'>";
        foreach ($locations as $location) {
            $data_html .= "<li id='location-" . $location["id"] . "'>" . $location["title"] . "</li>";
        }
        $data_html .= "</ul>";
        return $data_html;
    }
}
