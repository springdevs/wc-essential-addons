<?php

namespace SpringDevs\WcEssentialAddons;

use SpringDevs\WcEssentialAddons\Admin\Menu;

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
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        add_action("admin_enqueue_scripts", function () {
            wp_enqueue_style('sdwac_app_css');
        });
    }
}
