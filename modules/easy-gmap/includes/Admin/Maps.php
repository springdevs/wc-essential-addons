<?php

namespace springdevs\EasyGmap\Admin;

/**
 * The Maps class
 */
class Maps
{
    public function __construct()
    {
        add_action('init', [$this, 'gmap_post_type'], 0);
        add_filter('manage_gmap_posts_columns', [$this, 'gmap_custom_columns']);
        add_action('manage_gmap_posts_custom_column', [$this, 'gmap_columns_data'], 10, 2);
        add_action('add_meta_boxes', array($this, "gmap_metaboxes"));
        add_action('save_post', [$this, 'gmap_save_meta_post']);
    }

    /**
     * Custom column data
     **/
    public function gmap_columns_data($column, $post_id)
    {
        if ($column == "gmap_shortcode") {
            echo "<pre class='sdwac_coupon_pre_column'>[EasyGmap id=\"" . $post_id . "\"]</pre>";
        } else if ($column == "gmap_shortcode_locations") {
            echo "<pre class='sdwac_coupon_pre_column'>[EasyLocation id=\"" . $post_id . "\"]</pre>";
        }
    }

    /**
     * Custom column
     **/
    public function gmap_custom_columns($columns)
    {
        $columns['gmap_shortcode'] = __('Map ShortCode', 'sdevs_wea');
        $columns['gmap_shortcode_locations'] = __('Location ShortCode', 'sdevs_wea');
        $new = array();
        $gmap_shortcode = $columns['gmap_shortcode'];
        $gmap_shortcode_locations = $columns['gmap_shortcode_locations'];
        unset($columns['gmap_shortcode']);

        foreach ($columns as $key => $value) {
            if ($key == 'date') {
                $new['gmap_shortcode'] = $gmap_shortcode;
                $new['gmap_shortcode_locations'] = $gmap_shortcode_locations;
            }
            $new[$key] = $value;
        }

        return $new;
    }

    /**
     * save post meta
     **/
    public function gmap_save_meta_post($post_id)
    {
        if (!isset($_POST["gmaplocation"])) {
            return;
        }
        if (!wp_verify_nonce($_POST["gmap_post_nonce"], "gmap_post_nonce")) {
            wp_die(__('Sorry !! You cannot permit to access.', 'sdevs_wea'));
        }

        $height = (int)$_POST["height"];
        $zoom = (int)$_POST["zoom"];

        if (is_int($height) && is_int($zoom)) :
            $streetViewControl = isset($_POST["streetViewControl"]) ? true : false;
            $draggable = isset($_POST["draggable"]) ? true : false;
            $mapTypeControl = isset($_POST["mapTypeControl"]) ? true : false;
            $zoomControl = isset($_POST["zoomControl"]) ? true : false;
            $rotateControl = isset($_POST["rotateControl"]) ? true : false;

            update_post_meta($post_id, "gmap_maps",  sanitize_text_field($_POST["gmaplocation"]));
            update_post_meta($post_id, "gmap_in_settings", [
                "height" => $height,
                "zoom" => $zoom,
                "streetViewControl" => $streetViewControl,
                "draggable" => $draggable,
                "mapTypeControl" => $mapTypeControl,
                "zoomControl" => $zoomControl,
                "rotateControl" => $rotateControl
            ]);
        endif;
    }

    /**
     * Register meta box's
     **/
    public function gmap_metaboxes()
    {
        add_meta_box(
            'gmap_post_info',
            __('Select Locations', 'sdevs_wea'),
            [$this, 'gmap_metabox_screen'],
            'gmap',
            'normal',
            'default'
        );
        add_meta_box(
            'gmap_setting_info',
            __('Map Settings', 'sdevs_wea'),
            [$this, 'gmap_settings_screen'],
            'gmap',
            'normal',
            'default'
        );
    }

    /**
     * Settings screen
     **/
    public function gmap_settings_screen()
    {
        $action = get_current_screen()->action;
        $post_meta = [];
        $streetViewControl = true;
        $draggable = true;
        $mapTypeControl = true;
        $zoomControl = true;
        $rotateControl = true;
        if (!$action) {
            $post_id = (int) $_GET["post"];
            $post_meta = get_post_meta($post_id, "gmap_in_settings", true);
            $streetViewControl = $post_meta["streetViewControl"];
            $draggable = $post_meta["draggable"];
            $mapTypeControl = $post_meta["mapTypeControl"];
            $zoomControl = $post_meta["zoomControl"];
            $rotateControl = $post_meta["rotateControl"];
        }
?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="height">Height ( px )</label></th>
                    <td><input name="height" type="number" id="height" value="<?php echo ($post_meta != "" && !empty($post_meta)) ? $post_meta["height"] : null; ?>" placeholder="Put Map Height" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="zoom"> Zoom Level </label></th>
                    <td>
                        <select name="zoom" id="zoom">
                            <option value="0">Auto</option>
                            <?php for ($i = 1; $i < 20; $i++) : ?>
                                <option value="<?php echo $i; ?>" <?php if ($post_meta != "" && !empty($post_meta) && $i == $post_meta["zoom"]) {
                                                                        echo "selected";
                                                                    } ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="streetViewControl">Enable streetViewControl</label></th>
                    <td><input type="checkbox" name="streetViewControl" id="streetViewControl" <?php if ($streetViewControl) {
                                                                                                    echo "checked";
                                                                                                } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="draggable">Enable draggable</label></th>
                    <td><input type="checkbox" name="draggable" id="draggable" <?php if ($draggable) {
                                                                                    echo "checked";
                                                                                } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="mapTypeControl">Enable mapTypeControl</label></th>
                    <td><input type="checkbox" name="mapTypeControl" id="mapTypeControl" <?php if ($mapTypeControl) {
                                                                                                echo "checked";
                                                                                            } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="zoomControl">Enable zoomControl</label></th>
                    <td><input type="checkbox" name="zoomControl" id="zoomControl" <?php if ($zoomControl) {
                                                                                        echo "checked";
                                                                                    } ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="rotateControl">Enable rotateControl</label></th>
                    <td><input type="checkbox" name="rotateControl" id="rotateControl" <?php if ($rotateControl) {
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
        $nonce = wp_create_nonce("gmap_post_nonce");
        $action = get_current_screen()->action;
        $post_meta = [];
        if (!$action) {
            $post_id = get_the_ID();
            $post_meta = get_post_meta($post_id, "gmap_maps", true);
            if (empty($post_meta) || $post_meta == "") {
                $post_meta = [];
            }
        } ?>
        <input type="hidden" name="gmap_post_nonce" value="<?php echo $nonce; ?>" />
        <table class="form-table">
            <tbody>
                <tr>
                    <td>
                        <fieldset>
                            <?php
                            $args = array(
                                'post_type' => 'location',
                                'posts_per_page' => -1,
                            );
                            $query = new \WP_Query($args);
                            if ($query->have_posts()) {
                                while ($query->have_posts()) {
                                    $query->the_post();
                            ?>
                                    <label for="gmaplocation<?php the_ID(); ?>">
                                        <input type="checkbox" name="gmaplocation[]" id="gmaplocation<?php the_ID(); ?>" value="<?php the_ID(); ?>" <?php if (in_array(get_the_ID(), $post_meta)) {
                                                                                                                                                        echo "checked";
                                                                                                                                                    } ?>><?php the_title(); ?>
                                    </label>
                                    <br>
                            <?php
                                }
                            } ?>
                        </fieldset>
                    </td>
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
            "name" => __("Gmaps", "sdevs_wea"),
            "singular_name" => __("gmap", "sdevs_wea"),
            'name_admin_bar'        => __('Gmap\'s', 'sdevs_wea'),
            'archives'              => __('Item Archives', 'sdevs_wea'),
            'attributes'            => __('Item Attributes', 'sdevs_wea'),
            'parent_item_colon'     => __('Parent Gmap:', 'sdevs_wea'),
            'all_items'             => __('Gmaps', 'sdevs_wea'),
            'add_new_item'          => __('Add New Gmap', 'sdevs_wea'),
            'add_new'               => __('Add Gmap', 'sdevs_wea'),
            'new_item'              => __('New Gmap', 'sdevs_wea'),
            'edit_item'             => __('Edit Gmap', 'sdevs_wea'),
            'update_item'           => __('Update Gmap', 'sdevs_wea'),
            'view_item'             => __('View Gmap', 'sdevs_wea'),
            'view_items'            => __('View Gmaps', 'sdevs_wea'),
            'search_items'          => __('Search Gmap', 'sdevs_wea'),
        );

        $args = array(
            "label" => __("Gmaps", "sdevs_wea"),
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
            "rewrite" => array("slug" => "gmap", "with_front" => true),
            "query_var" => true,
            "supports" => array("title"),
        );

        register_post_type("gmap", $args);
    }
}
