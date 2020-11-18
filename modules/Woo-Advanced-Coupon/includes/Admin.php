<?php

namespace springdevs\WooAdvanceCoupon;

use springdevs\WooAdvanceCoupon\Admin\Coupon;
use springdevs\WooAdvanceCoupon\Admin\MetaBoxes;
use springdevs\WooAdvanceCoupon\Admin\sdwac_Panels;
use springdevs\WooAdvanceCoupon\Admin\Setting;
use springdevs\WooAdvanceCoupon\Illuminate\Coupon as IlluminateCoupon;

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
        new IlluminateCoupon;
        new Coupon;
        new MetaBoxes;
        new sdwac_Panels;
        new Setting;
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
