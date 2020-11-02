<?php

namespace SpringDevs\WcBooking;

use SpringDevs\WcBooking\Frontend\Gateways;
use SpringDevs\WcBooking\Frontend\MyAccount;
use SpringDevs\WcBooking\Frontend\Products;
use SpringDevs\WcBooking\Illuminate\Gateways as IlluminateGateways;
use SpringDevs\WcBooking\Illuminate\Order;
use SpringDevs\WcBooking\Illuminate\OrderPage;
use SpringDevs\WcBooking\Illuminate\OrderStatus;

/**
 * Frontend handler class
 */
class Frontend
{
    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        require_once WCBOOKING_ASSETS_INCLUDES . "/Illuminate/PaymentGatewayRegister.php";
        new Products;
        new Gateways;
        new IlluminateGateways;
        new OrderPage;
        new MyAccount;
        new OrderStatus;
        new Order;
    }
}
