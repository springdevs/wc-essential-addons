<?php

namespace springdevs\WooAdvanceCoupon;

use springdevs\WooAdvanceCoupon\Admin\sdwac_Coupon;
use springdevs\WooAdvanceCoupon\Admin\sdwac_Panels;
use springdevs\WooAdvanceCoupon\Admin\sdwac_Setting;

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
        new sdwac_Coupon;
        new sdwac_Panels;
        new sdwac_Setting;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
    }
}
