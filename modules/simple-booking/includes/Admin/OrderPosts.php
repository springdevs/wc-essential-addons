<?php


namespace SpringDevs\WcBooking\Admin;


/**
 * Class OrderStatus
 * @package SpringDevs\WcBooking\Admin
 */
class OrderPosts
{
    public function __construct()
    {
        add_action('admin_menu', [$this, "remove_default_meta_box"]);
        add_action('add_meta_boxes', [$this, "create_meta_boxes"]);
        add_action('admin_head-post.php', [$this, "some_styles"]);
        add_action('admin_head-post-new.php', [$this, "some_styles"]);
        add_action('admin_footer-post.php', [$this, "some_scripts"]);
        add_action('admin_footer-post-new.php', [$this, "some_scripts"]);
        add_action("save_post", [$this, "save_bookable_order_post"], 25);
    }

    public function remove_default_meta_box()
    {
        remove_meta_box('submitdiv', 'bookable_order', 'side');
    }

    public function create_meta_boxes()
    {
        // Save Data
        add_meta_box(
            'bookable_order_save_post',
            'Save',
            [$this, 'bookable_order_save_post'],
            'bookable_order',
            'side',
            'default'
        );

        // User Data
        add_meta_box(
            'bookable_order_customer_data',
            'Customer Details',
            [$this, 'bookable_order_customer_data'],
            'bookable_order',
            'side',
            'default'
        );

        // Form Fields
        add_meta_box(
            'bookable_order_meta_fields',
            'Data',
            [$this, 'bookable_order_meta_fields'],
            'bookable_order',
            'normal',
            'default'
        );
    }

    public function bookable_order_save_post()
    {
?>
        <div class="submitbox">
            <div id="delete-action">
                <a class="submitdelete deletion" href="<?php echo get_delete_post_link(); ?>">Move to trash</a>
            </div>
            <input type="submit" class="button save_order button-primary tips" name="save" value="Save Booking">
        </div>
    <?php
    }

    public function bookable_order_customer_data()
    {
        $post_meta = get_post_meta(get_the_ID(), "_booking_order_meta", true);
        $order = wc_get_order($post_meta["order_id"]);
    ?>
        <table class="booking-customer-details" style="width: 100%;">
            <tbody>
                <tr>
                    <th>Name:</th>
                    <td><?php echo $order->get_formatted_billing_full_name(); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><a href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><?php echo $order->get_formatted_billing_address(); ?></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><?php echo $order->get_billing_phone(); ?></td>
                </tr>
                <tr class="view">
                    <th>&nbsp;</th>
                    <td><a class="button button-small" target="_blank" href="<?php echo get_edit_post_link($post_meta['order_id']); ?>">View Order</a></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    public function some_styles()
    {
        global $post;
        if ($post->post_type == "bookable_order") :
        ?>
            <style>
                .submitbox {
                    display: flex;
                    justify-content: space-around;
                }

                .booking-customer-details th {
                    padding: 0 6px 6px 0;
                }

                .booking-customer-details th {
                    vertical-align: top;
                    text-align: left;
                }
            </style>
        <?php
        endif;
    }

    public function some_scripts()
    {
        global $post;
        if ($post->post_type == "bookable_order") :
        ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery(window).off("beforeunload", null);
                });
            </script>
        <?php
        endif;
    }

    public function bookable_order_meta_fields()
    {
        global $post;
        $statuses = [
            "paid" => "Paid",
            "unpaid" => "Unpaid",
            "pending_conf" => "Pending Confirmation",
            "confirmed" => "Confirmed",
            "complete" => "Complete",
            "cancelled" => "Cancelled",
        ];
        $post_meta = get_post_meta($post->ID, "_booking_order_meta", true);
        $product = wc_get_product($post_meta["product_id"]);
        $attributes = [];

        if (empty($post_meta)) {
            $date = null;
            $time = null;
        } else {
            $date = $post_meta["date"];
            $time = strtotime($post_meta["time"]);
            $time = date("H:i", $time);
            $order = wc_get_order($post_meta["order_id"]);
            foreach ($order->get_items() as $key => $item) {
                foreach ($item->get_meta_data() as $data) {
                    if ($data->key != "Date" && $data->key != "Time") $attributes[$data->key] = $data->value;
                }
            }
        }
        ?>
        <table class="form-table sdevs-form">
            <tbody>
                <tr>
                    <th class="sdevs_th" scope="row"><label for="bookable_order_date">Product</label></th>
                    <td>
                        <p class="description" id="tagline-description">
                            <a href="<?php the_permalink($post_meta["product_id"]); ?>" target="_blank">
                                <?php echo $product->get_title(); ?>
                            </a>
                            <br />
                            <?php foreach ($attributes as $key => $value) : ?>
                                <strong><?php echo $key; ?> : </strong> <?php echo $value; ?><br />
                            <?php endforeach; ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th class="sdevs_th" scope="row"><label for="bookable_order_date">Date</label></th>
                    <td><input name="bookable_order_date" type="text" id="bookable_order_date" class="regular-text pac-target-input" value="<?php echo $date; ?>" required />
                        <p class="description" id="tagline-description">Ex : Sep 14, 2020</p>
                    </td>
                </tr>
                <tr>
                    <th class="sdevs_th" scope="row"><label for="bookable_order_time">Time</label></th>
                    <td><input name="bookable_order_time" type="time" id="bookable_order_time" value="<?php echo $time; ?>" class="regular-text pac-target-input" required /></td>
                </tr>
                <tr>
                    <th class="sdevs_th" scope="row"><label for="bookable_order_status">Status</label></th>
                    <td>
                        <select name="bookable_order_status" id="bookable_order_status">
                            <?php foreach ($statuses as $value => $label) : ?>
                                <option value="<?php echo $value; ?>" <?php if ($post->post_status == $value) echo "selected"; ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
<?php
    }

    public function save_bookable_order_post($post_id)
    {
        if (wp_is_post_revision($post_id)) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST["bookable_order_date"])) return;

        remove_action("save_post", [$this, "save_bookable_order_post"], 25);

        $date = sanitize_text_field($_POST["bookable_order_date"]);
        $time = sanitize_text_field($_POST["bookable_order_time"]);
        $time = date("h:i a", strtotime($time));
        $status = sanitize_text_field($_POST["bookable_order_status"]);
        $post_meta = get_post_meta($post_id, "_booking_order_meta", true);
        $post_meta["date"] = $date;
        $post_meta["time"] = $time;
        update_post_meta($post_id, "_booking_order_meta", $post_meta);
        $post = array('ID' => $post_id, 'post_status' => $status);
        wp_update_post($post);

        add_action("save_post", [$this, "save_bookable_order_post"], 25);
        return;
    }
}
