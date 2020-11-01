<?php

namespace SpringDevs\WcSubscription;

use SpringDevs\WcSubscription\Admin\Menu;
use SpringDevs\WcSubscription\Admin\Order as AdminOrder;
use SpringDevs\WcSubscription\Admin\Product;
use SpringDevs\WcSubscription\Admin\Settings;
use SpringDevs\WcSubscription\Admin\Subscriptions;
use SpringDevs\WcSubscription\Illuminate\Comments;
use SpringDevs\WcSubscription\Illuminate\Cron;
use SpringDevs\WcSubscription\Illuminate\Email;
use SpringDevs\WcSubscription\Illuminate\Order;
use SpringDevs\WcSubscription\Illuminate\RegisterPostStatus;
use SpringDevs\WcSubscription\Illuminate\Subscriptions as IlluminateSubscriptions;

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
        new IlluminateSubscriptions;
        new Cron;
        new Menu;
        new Product;
        new Subscriptions;
        new RegisterPostStatus;
        new Order;
        new AdminOrder;
        new Comments;
        new Email;
        new Settings;
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
