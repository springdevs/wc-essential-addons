<?php

namespace SpringDevs\WcBooking;

use SpringDevs\WcBooking\Admin\BookingForm;
use SpringDevs\WcBooking\Admin\BookingOrder;
use SpringDevs\WcBooking\Admin\Menu;
use SpringDevs\WcBooking\Admin\OrderPosts;
use SpringDevs\WcBooking\Illuminate\Gateways;
use SpringDevs\WcBooking\Illuminate\Order;
use SpringDevs\WcBooking\Illuminate\OrderPage;
use SpringDevs\WcBooking\Illuminate\OrderStatus;

/**
 * The admin class
 */
class Admin
{

    /**
     * Initialize the class
     */
    public function __construct()
    {
        $this->dispatch_actions();
        new Menu;
        new BookingForm;
        new OrderPage;
        new Gateways;
        new BookingOrder;
        new OrderStatus;
        new OrderPosts;
        new Order;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        require_once WCBOOKING_ASSETS_INCLUDES . "/Illuminate/PaymentGatewayRegister.php";
    }
}
