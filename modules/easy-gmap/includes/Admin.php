<?php

namespace springdevs\EasyGmap;

use springdevs\EasyGmap\Admin\Locations;
use springdevs\EasyGmap\Admin\Maps;
use springdevs\EasyGmap\Admin\Settings;

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
        add_action('admin_menu', [$this, 'menu']);
        new Locations;
        new Maps;
        new Settings;
    }

    /**
     * Admin menu
     *
     **/
    public function menu()
    {
        add_menu_page("Gmaps", "Gmaps", "manage_options", "gmaps", function () {
            return "Hello world";
        }, '', 40);
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
