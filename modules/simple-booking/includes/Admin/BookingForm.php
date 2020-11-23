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
        add_filter('woocommerce_product_data_tabs', array($this, 'custom_data_tabs'));
        add_filter('woocommerce_product_data_panels', array($this, 'custom_tab_data'));
        add_action('save_post_product', array($this, 'save_bookable_settings'));
    }

    public function custom_data_tabs($tabs)
    {
        $class = apply_filters('sdevs_booking_product_data_class', 'show_if_simple');
        $tabs['sdevs_booking'] = array(
            'label'     => __('Booking', 'sdevs_wea'),
            'class' => $class,
            'target'     => 'sdevs_booking_data'
        );
        return $tabs;
    }

    public function custom_tab_data()
    {
        if (sdevs_is_pro_module_activate('booking-pro')) {
            do_action('sdevs_booking_pro_edit_fields_html');
            return;
        } else {
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
                    $enable_booking = $post_meta["enable_booking"] ? "yes" : false;
                    $display_next_days = $post_meta["display_next_days"];
                    $display_start_time = $post_meta["display_start_time"];
                    $display_end_time = $post_meta["display_end_time"];
                    $bookable_require_conf = $post_meta["bookable_require_conf"] ? "yes" : false;
                endif;
            else :
                $enable_booking = false;
                $display_next_days = "";
                $display_start_time = "";
                $display_end_time = "";
                $bookable_require_conf = false;
            endif;
            $class = apply_filters('sdevs_booking_product_data_class', 'show_if_simple'); ?>
            <div id="sdevs_booking_data" class="panel sdevs_panel woocommerce_options_panel sdevs-form <?php echo $class; ?>">
                <strong>Booking Settings</strong>
                <?php

                woocommerce_wp_checkbox([
                    "id" => "enable_booking",
                    "label" => __("Enable Booking", "sdevs_wea"),
                    "value" => "yes",
                    "cbvalue" => $enable_booking,
                    "description"  => __("check this box to enable booking for this product", "sdevs_wea"),
                    "desc_tip"  =>  true
                ]);

                woocommerce_wp_checkbox([
                    "id" => "bookable_require_conf",
                    "label" => __("Require Confirmations", "sdevs_wea"),
                    "value" => "yes",
                    "cbvalue" => $bookable_require_conf,
                    "description"  => __("check this box if admin approval / confirmation is required for booking", "sdevs_wea"),
                    "desc_tip"  =>  true
                ]);

                echo "<hr style='margin: 20px 0;' /><strong>Calendar Display Options</strong>";

                woocommerce_wp_text_input([
                    "id" => "display_next_days",
                    "label" => __('Display Next Days', 'sdevs_wea'),
                    "type" => "number",
                    "value" => $display_next_days
                ]);

                woocommerce_wp_text_input([
                    "id" => "display_start_time",
                    "label" => __('Display Start Time', 'sdevs_wea'),
                    "type" => "time",
                    "value" => $display_start_time
                ]);

                woocommerce_wp_text_input([
                    "id" => "display_end_time",
                    "label" => __('Display End Time', 'sdevs_wea'),
                    "type" => "time",
                    "value" => $display_end_time
                ]);

                ?>
            </div>
<?php
        }
    }

    public function save_bookable_settings($post_id)
    {
        if (!isset($_POST["product-type"]) || !isset($_POST['display_next_days'])) return;
        if (sdevs_is_pro_module_activate('booking-pro')) return;
        $display_next_days = isset($_POST['display_next_days']) ? sanitize_text_field($_POST['display_next_days']) : false;
        $display_start_time = isset($_POST['display_start_time']) ? sanitize_text_field($_POST['display_start_time']) : false;
        $display_end_time = isset($_POST['display_end_time']) ? sanitize_text_field($_POST['display_end_time']) : false;
        $bookable_require_conf = isset($_POST['bookable_require_conf']);
        $enable_booking = isset($_POST['enable_booking']);
        update_post_meta($post_id, 'bookable_product_meta', [
            "enable_booking" => $enable_booking,
            "display_next_days" => $display_next_days,
            "display_start_time" => $display_start_time,
            "display_end_time" => $display_end_time,
            "bookable_require_conf" => $bookable_require_conf
        ]);
    }
}
