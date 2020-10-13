<?php


namespace SpringDevs\WcBooking\Frontend;


/**
 * Class MyAccount
 * @package SpringDevs\WcBooking\Frontend
 */
class MyAccount
{
    public function __construct()
    {
        add_action("init", [$this, "flush_rewrite_rules"]);
        add_filter( 'woocommerce_account_menu_items', [ $this, 'custom_my_account_menu_items' ] );
        add_filter( 'the_title', [ $this, 'change_endpoint_title'] );
        add_action( 'woocommerce_account_bookable-endpoint_endpoint', [ $this, 'bookable_endpoint_content' ] );
    }

    /**
     * Re-write flush
     */
    public function flush_rewrite_rules()
    {
        add_rewrite_endpoint( 'bookable-endpoint', EP_ROOT | EP_PAGES );
        flush_rewrite_rules();
    }

    /**
     * @param $title
     * @return string|void
     */
    public function change_endpoint_title($title)
    {
        global $wp_query;
        $is_endpoint = isset( $wp_query->query_vars['bookable-endpoint'] );
        if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
            $title = __( 'My Booking\'s', 'woocommerce' );
            remove_filter( 'the_title', [ $this, 'change_endpoint_title'] );
        }
        return $title;
    }

    /**
     * @param $items
     * @return mixed
     */
    public function custom_my_account_menu_items($items)
    {
        $logout = $items['customer-logout'];
        unset( $items['customer-logout']);
        $items['bookable-endpoint'] = __( 'Bookings', 'sdevs_wea' );
        $items['customer-logout'] = $logout;
        return $items;
    }

    /**
     * Bookable EndPoint Content
     */
    public function bookable_endpoint_content()
    {
        wc_get_template('myaccount/bookings.php', [], 'simple-booking', WCBOOKING_TEMPLATES);
    }
}
