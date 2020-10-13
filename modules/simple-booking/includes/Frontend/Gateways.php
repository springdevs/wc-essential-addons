<?php

namespace SpringDevs\WcBooking\Frontend;

/**
 * Class Gateways
 * @package SpringDevs\WcBooking\Frontend
 */

class Gateways
{
    public function __construct()
    {
        add_filter('woocommerce_available_payment_gateways', [$this, "filter_available_gateways"], 10, 1);
    }

    /**
     * @param $available_gateways
     * @return array
     */
    public function filter_available_gateways($available_gateways)
    {
        if (is_admin())
            return $available_gateways;

        $bookable = false;

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $book_meta = get_post_meta($cart_item['product_id'], "bookable_product_meta", true);
            if (!empty($book_meta) && $book_meta["enable_booking"]):
                $book_meta["bookable_require_conf"] ? $bookable = true : false;
            endif;
        }

        if (!$bookable) :
            unset($available_gateways['wc-booking-gateway']);
        else :
            $bookable_gateway = $available_gateways['wc-booking-gateway'];
            $available_gateways = [];
            $available_gateways['wc-booking-gateway'] = $bookable_gateway;
        endif;

        return $available_gateways;
    }
}
