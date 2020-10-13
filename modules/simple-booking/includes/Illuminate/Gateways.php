<?php

namespace SpringDevs\WcBooking\Illuminate;

/**
 * Class Gateways
 * @package SpringDevs\WcBooking\Global
 */

class Gateways
{
    public function __construct()
    {
        add_filter('woocommerce_payment_gateways', [$this, "include_gateway"]);
    }

    public function include_gateway($gateways)
    {
        $gateways[] = 'PaymentGatewayRegister'; // gateway class
        return $gateways;
    }
}
