<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * Class Subscriptions
 * @package SpringDevs\WcSubscription\Illuminate
 */
class Subscriptions
{
    public function __construct()
    {
        add_action("init", [$this, "create_post_type"]);
    }

    /**
     *  Create Custom Post Type : subscrpt_order
     */
    public function create_post_type()
    {
        $labels = array(
            "name" => __("Subscriptions", "sdevs_wea"),
            "singular_name" => __("Subscription", "sdevs_wea"),
            'name_admin_bar'        => __('Subscription\'s', 'sdevs_wea'),
            'archives'              => __('Item Archives', 'sdevs_wea'),
            'attributes'            => __('Item Attributes', 'sdevs_wea'),
            'parent_item_colon'     => __('Parent :', 'sdevs_wea'),
            'all_items'             => __('Subscriptions', 'sdevs_wea'),
            'add_new_item'          => __('Add New Subscription', 'sdevs_wea'),
            'add_new'               => __('Add Subscription', 'sdevs_wea'),
            'new_item'              => __('New Subscription', 'sdevs_wea'),
            'edit_item'             => __('Edit Subscription', 'sdevs_wea'),
            'update_item'           => __('Update Subscription', 'sdevs_wea'),
            'view_item'             => __('View Subscription', 'sdevs_wea'),
            'view_items'            => __('View Subscription', 'sdevs_wea'),
            'search_items'          => __('Search Subscription', 'sdevs_wea'),
        );

        $args = array(
            "label" => __("Subscriptions", "sdevs_wea"),
            "labels" => $labels,
            "description" => "",
            "public" => false,
            "publicly_queryable" => true,
            "show_ui" => true,
            "delete_with_user" => false,
            "show_in_rest" => true,
            "rest_base" => "",
            "rest_controller_class" => "WP_REST_Posts_Controller",
            "has_archive" => false,
            "show_in_menu" => false,
            "show_in_nav_menus" => true,
            "exclude_from_search" => false,
            "capability_type" => "post",
            "map_meta_cap" => true,
            'capabilities' => array(
                'create_posts' => false
            ),
            "hierarchical" => false,
            "rewrite" => array("slug" => "subscrpt_order", "with_front" => true),
            "query_var" => true,
            "supports" => array("title"),
        );

        register_post_type("subscrpt_order", $args);
    }
}
