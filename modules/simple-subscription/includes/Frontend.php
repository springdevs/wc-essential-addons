<?php

namespace SpringDevs\WcSubscription;

use SpringDevs\WcSubscription\Frontend\ActionController;
use SpringDevs\WcSubscription\Frontend\Downloadable;
use SpringDevs\WcSubscription\Frontend\MyAccount;
use SpringDevs\WcSubscription\Frontend\Product;
use SpringDevs\WcSubscription\Frontend\Thankyou;
use SpringDevs\WcSubscription\Illuminate\AutoRenewal;
use SpringDevs\WcSubscription\Illuminate\Cron;
use SpringDevs\WcSubscription\Illuminate\Email;
use SpringDevs\WcSubscription\Illuminate\Order;
use SpringDevs\WcSubscription\Illuminate\RegisterPostStatus;
use SpringDevs\WcSubscription\Illuminate\Subscriptions;

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
        new Subscriptions;
        new Cron;
        new RegisterPostStatus;
        new Product;
        new Thankyou;
        new ActionController;
        new MyAccount;
        new Downloadable;
        new Order;
        new Email;
        new AutoRenewal;
    }
}
