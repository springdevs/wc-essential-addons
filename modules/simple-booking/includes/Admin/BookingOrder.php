<?php


namespace SpringDevs\WcBooking\Admin;


/**
 * Class BookingOrder
 * @package SpringDevs\WcBooking\Admin
 */
class BookingOrder
{
    public function __construct()
    {
        add_action("init", [$this, "create_post_type"]);
        add_filter('post_row_actions', [$this, 'post_row_actions'], 10, 2);
        add_filter('manage_bookable_order_posts_columns', [$this, 'add_custom_columns']);
        add_action('manage_bookable_order_posts_custom_column', [$this, 'add_custom_columns_data'], 10, 2);
    }

    /**
     *Create Custom Post Type : bookable_order
     */
    public function create_post_type()
    {
        $labels = array(
            "name" => __("Bookings", "sdevs_wea"),
            "singular_name" => __("Booking", "sdevs_wea"),
            'name_admin_bar'        => __('Booking\'s', 'sdevs_wea'),
            'archives'              => __('Item Archives', 'sdevs_wea'),
            'attributes'            => __('Item Attributes', 'sdevs_wea'),
            'parent_item_colon'     => __('Parent :', 'sdevs_wea'),
            'all_items'             => __('Bookings', 'sdevs_wea'),
            'add_new_item'          => __('Add New Booking', 'sdevs_wea'),
            'add_new'               => __('Add Booking', 'sdevs_wea'),
            'new_item'              => __('New Booking', 'sdevs_wea'),
            'edit_item'             => __('Edit Booking', 'sdevs_wea'),
            'update_item'           => __('Update Booking', 'sdevs_wea'),
            'view_item'             => __('View Booking', 'sdevs_wea'),
            'view_items'            => __('View Booking', 'sdevs_wea'),
            'search_items'          => __('Search Booking', 'sdevs_wea'),
        );

        $args = array(
            "label" => __("Bookings", "sdevs_wea"),
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
            "rewrite" => array("slug" => "bookable_order", "with_front" => true),
            "query_var" => true,
            "supports" => array("title"),
        );

        register_post_type("bookable_order", $args);
    }

    /**
     * @param $unset_actions
     * @param $post
     * @return mixed
     */
    public function post_row_actions($unset_actions, $post)
    {
        global $current_screen;
        if ($current_screen->post_type != 'bookable_order')
            return $unset_actions;
        unset($unset_actions['inline hide-if-no-js']);
        unset($unset_actions['view']);
        return $unset_actions;
    }

    public function add_custom_columns($columns)
    {
        $columns['booked'] = __('Booked', 'sdevs_wea');
        $columns['order_id'] = __('Order', 'sdevs_wea');
        $columns['customer'] = __('Customer', 'sdevs_wea');
        $new = [];
        $order_id = $columns['order_id'];
        $booked = $columns['booked'];
        $customer = $columns['customer'];
        unset($columns['booked']);

        foreach ($columns as $key => $value) {
            if ($key == 'date') {
                $new['order_id'] = $order_id;
                $new['customer'] = $customer;
                $new['booked'] = $booked;
            }
            $new[$key] = $value;
        }

        return $new;
    }

    public function add_custom_columns_data($column, $post_id)
    {
        $post_meta = get_post_meta($post_id, "_booking_order_meta", true);
        $product = wc_get_product($post_meta["product_id"]);
        $order = wc_get_order($post_meta["order_id"]);
        if (!$order) return;
        $order_items = $order && is_array($order->get_items()) ? $order->get_items() : [];
        $attributes = [];
        foreach ($order_items as $key => $item) {
            foreach ($item->get_meta_data() as $data) {
                if ($data->key != "Date" && $data->key != "Time") $attributes[$data->key] = $data->value;
            }
        }
        if ($column == "booked") {
?>
            <a href="<?php the_permalink($post_meta["product_id"]); ?>"><?php echo $product->get_title(); ?></a>
            <br />
            <?php foreach ($attributes as $key => $value) : ?>
                <strong><?php echo $key; ?> : </strong> <?php echo $value; ?> <br />
            <?php endforeach; ?>
            <hr />
            <?php echo $post_meta["date"] . ' - ' . $post_meta["time"]; ?>
        <?php
        } elseif ($column == "customer") {
        ?>
            <?php echo $order->get_formatted_billing_full_name(); ?>
            <br />
            <a href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a>
            <br />
            Phone : <a href="tel:<?php echo $order->get_billing_phone(); ?>"><?php echo $order->get_billing_phone(); ?></a>
<?php
        } elseif ($column == "order_id") {
            echo "<a href=" . get_edit_post_link($post_meta["order_id"]) . " target='__blank'>" . $post_meta["order_id"] . "</a>";
        }
    }
}
