<?php

namespace SpringDevs\WcBooking\Admin;

/**
 * Class BookingForm || Booking Form
 * @package SpringDevs\WcBooking\Admin
 */
class BookingForm
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, "add_meta_boxes"));
        add_action('admin_footer', array($this, 'enable_js_on_wc_product'));
        add_action('save_post_product', array($this, 'save_bookable_settings'));
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'product_bookable_meta',
            'Booking Options',
            [$this, 'product_bookable_meta'],
            'product',
            'normal',
            'default'
        );
    }

    public function enable_js_on_wc_product()
    {
        global $post;
        if ($post == null || "product" != $post->post_type) return;
        $pro_version = get_option("sdevs_booking_pro");
        if (!$pro_version) :
?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    let selector = jQuery("#product-type");
                    selector.change(function() {
                        if (selector.val() !== "simple") {
                            jQuery("#product_bookable_meta").hide();
                        } else {
                            jQuery("#product_bookable_meta").show();
                        }
                    });
                    if (selector.val() !== "simple") {
                        jQuery("#product_bookable_meta").hide();
                    } else {
                        jQuery("#product_bookable_meta").show();
                    }
                });
            </script>
        <?php
        else :
        ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    jQuery("#product_bookable_meta").show();
                });
            </script>
        <?php
        endif;
    }

    public function product_bookable_meta()
    {
        $screen = get_current_screen();
        if ($screen->parent_base == "edit") :
            $post_meta = get_post_meta(get_the_ID(), "bookable_product_meta", true);
            if (empty($post_meta)) :
                $enable_booking = false;
                $display_next_days = "";
                $display_start_time = "";
                $display_end_time = "";
                $bookable_require_conf = false;
            else :
                $enable_booking = $post_meta["enable_booking"];
                $display_next_days = $post_meta["display_next_days"];
                $display_start_time = $post_meta["display_start_time"];
                $display_end_time = $post_meta["display_end_time"];
                $bookable_require_conf = $post_meta["bookable_require_conf"];
            endif;
        else :
            $enable_booking = false;
            $display_next_days = "";
            $display_start_time = "";
            $display_end_time = "";
            $bookable_require_conf = false;
        endif;
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="enable_booking">Enable Booking</label></th>
                    <td><input type="checkbox" name="enable_booking" id="enable_booking" value="yes" <?php if ($enable_booking) echo "checked"; ?> /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="display_next_days">Display Next Days</label></th>
                    <td><input type="number" name="display_next_days" id="display_next_days" placeholder="Enter Number" value="<?php echo $display_next_days; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="display_start_time">Display Start Time</label></th>
                    <td><input type="time" name="display_start_time" id="display_start_time" value="<?php echo $display_start_time; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="display_end_time">Display End Time</label></th>
                    <td><input type="time" name="display_end_time" id="display_end_time" value="<?php echo $display_end_time; ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bookable_require_conf">Require Confirmations</label></th>
                    <td><input type="checkbox" name="bookable_require_conf" value="yes" id="bookable_require_conf" <?php if ($bookable_require_conf) echo "checked"; ?> /></td>
                </tr>
            </tbody>
        </table>
<?php
    }

    public function save_bookable_settings($post_id)
    {
        if (!isset($_POST["product-type"])) return;
        $pro_version = get_option("sdevs_booking_pro");
        if (!$pro_version) :
            if ($_POST["product-type"] == "simple") :
                $display_next_days = isset($_POST['display_next_days']) ? sanitize_text_field($_POST['display_next_days']) : false;
                $display_start_time = isset($_POST['display_start_time']) ? sanitize_text_field($_POST['display_start_time']) : false;
                $display_end_time = isset($_POST['display_end_time']) ? sanitize_text_field($_POST['display_end_time']) : false;
                $bookable_require_conf = isset($_POST['bookable_require_conf']);
                $enable_booking = isset($_POST['enable_booking']);
            else :
                $display_next_days = false;
                $display_start_time = false;
                $display_end_time = false;
                $bookable_require_conf = false;
                $enable_booking = false;
            endif;
        else :
            $display_next_days = isset($_POST['display_next_days']) ? sanitize_text_field($_POST['display_next_days']) : false;
            $display_start_time = isset($_POST['display_start_time']) ? sanitize_text_field($_POST['display_start_time']) : false;
            $display_end_time = isset($_POST['display_end_time']) ? sanitize_text_field($_POST['display_end_time']) : false;
            $bookable_require_conf = isset($_POST['bookable_require_conf']);
            $enable_booking = isset($_POST['enable_booking']);
        endif;

        update_post_meta($post_id, 'bookable_product_meta', [
            "enable_booking" => $enable_booking,
            "display_next_days" => $display_next_days,
            "display_start_time" => $display_start_time,
            "display_end_time" => $display_end_time,
            "bookable_require_conf" => $bookable_require_conf
        ]);
    }
}
