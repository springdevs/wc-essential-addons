<?php

namespace springdevs\EasyGmap\Admin;

/**
 * The Locations class
 */
class Locations
{
    public function __construct()
    {
        add_action('init', [$this, 'gmap_post_type'], 0);
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
        add_action('add_meta_boxes', array($this, "gmap_metaboxes"));
        add_action('save_post', [$this, 'gmap_save_meta_post']);
    }

    /**
     * enqueue scripts
     **/
    public function register_scripts()
    {
        wp_enqueue_script("google-map");
        wp_enqueue_script('autocomplete-js');
    }

    /**
     * Save post meta
     **/
    public function gmap_save_meta_post($post_id)
    {
        if (!isset($_POST["gmap_lat"])) {
            return;
        }
        if (!wp_verify_nonce($_POST["gmap_location_nonce"], "gmap_location_nonce")) {
            wp_die(__('Sorry !! You cannot permit to access.', 'sdevs_wea'));
        }

        $lat = floatval($_POST["gmap_lat"]);
        $lng = floatval($_POST["gmap_lng"]);

        if (is_float($lat) && is_float($lng)) :
            $openInfoWindow = isset($_POST["openInfoWindow"]) ? true : false;
            $data = [
                "address" => sanitize_text_field($_POST["address"]),
                "lat" => $lat,
                "lng" => $lng,
                "openInfoWindow" => $openInfoWindow
            ];
            update_post_meta($post_id, "gmap_locations", $data);
        endif;
    }

    /**
     * Register meta box's
     **/
    public function gmap_metaboxes()
    {
        add_meta_box(
            'gmap_locations_info',
            __('Google Map Location Info', 'sdevs_wea'),
            [$this, 'gmap_metabox_screen'],
            'location',
            'normal',
            'default'
        );
        add_meta_box(
            'gmap_location_settings',
            __('Settings', 'sdevs_wea'),
            [$this, 'gmap_settings_screen'],
            'location',
            'normal',
            'default'
        );
    }

    /**
     * Settings
     **/
    public function gmap_settings_screen()
    {
        $action = get_current_screen()->action;
        $openInfoWindow = false;
        if (!$action) {
            $post_id = get_the_ID();
            $post_meta = get_post_meta($post_id, "gmap_locations", true);
            $openInfoWindow = $post_meta["openInfoWindow"];
        }
?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="openInfoWindow">Default Open InfoWindow</label></th>
                    <td><input type="checkbox" name="openInfoWindow" id="openInfoWindow" <?php if ($openInfoWindow) {
                                                                                                echo "checked";
                                                                                            } ?> /></td>
                </tr>
            </tbody>
        </table>
    <?php
    }

    /**
     * Metabox screen
     **/
    public function gmap_metabox_screen()
    {
        $nonce = wp_create_nonce("gmap_location_nonce");
        $action = get_current_screen()->action;
        if (!$action) {
            $post_id = get_the_ID();
            $post_meta = get_post_meta($post_id, "gmap_locations", true);
            if (!empty($post_meta) || $post_meta != "") {
                $address = $post_meta["address"];
                $lat = $post_meta["lat"];
                $lng = $post_meta["lng"];
            } else {
                $address = null;
                $lat = null;
                $lng = null;
            }
        } else {
            $address = null;
            $lat = null;
            $lng = null;
        }
    ?>
        <input type="hidden" name="gmap_location_nonce" value="<?php echo $nonce; ?>" />
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="searchLocation">Location</label></th>
                    <td><input name="address" type="text" id="searchLocation" placeholder="Search Location By Google" value="<?php echo $address; ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gmap_lat">Latitude</label></th>
                    <td><input name="gmap_lat" type="text" id="gmap_lat" placeholder="Lat of your location" value="<?php echo $lat; ?>" class="regular-text" readonly></td>
                </tr>
                <tr>
                    <th scope="row"><label for="gmap_lng">Longitude</label></th>
                    <td><input name="gmap_lng" type="text" id="gmap_lng" placeholder="Lng of your location" value="<?php echo $lng; ?>" class="regular-text" readonly></td>
                </tr>
            </tbody>
        </table>
<?php
    }

    /**
     * Register Custom Post Type
     *
     * @uses register_post_type()
     **/
    public function gmap_post_type()
    {
        $labels = array(
            "name" => __("Locations", "sdevs_wea"),
            "singular_name" => __("Location", "sdevs_wea"),
            'name_admin_bar'        => __('Location\'s', 'sdevs_wea'),
            'archives'              => __('Item Archives', 'sdevs_wea'),
            'attributes'            => __('Item Attributes', 'sdevs_wea'),
            'parent_item_colon'     => __('Parent Location:', 'sdevs_wea'),
            'all_items'             => __('Locations', 'sdevs_wea'),
            'add_new_item'          => __('Add New Location', 'sdevs_wea'),
            'add_new'               => __('Add Location', 'sdevs_wea'),
            'new_item'              => __('New Location', 'sdevs_wea'),
            'edit_item'             => __('Edit Location', 'sdevs_wea'),
            'update_item'           => __('Update Location', 'sdevs_wea'),
            'view_item'             => __('View Location', 'sdevs_wea'),
            'view_items'            => __('View Locations', 'sdevs_wea'),
            'search_items'          => __('Search Location', 'sdevs_wea'),
        );

        $args = array(
            "label" => __("Locations", "sdevs_wea"),
            "labels" => $labels,
            "description" => "",
            "public" => true,
            "publicly_queryable" => true,
            "show_ui" => true,
            "delete_with_user" => false,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => "gmaps",
            "show_in_nav_menus" => true,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            "hierarchical" => false,
            "rewrite" => array("slug" => "location", "with_front" => true),
            "query_var" => true,
            "supports" => array("title"),
        );

        register_post_type("location", $args);
    }
}
