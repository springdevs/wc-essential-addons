<?php

namespace SpringDevs\WcEssentialAddons;

use SpringDevs\WcEssentialAddons\Admin\Menu;
use SpringDevs\WcEssentialAddons\Admin\Notices;
use SpringDevs\WcEssentialAddons\Admin\Required;
use SpringDevs\WcEssentialAddons\Admin\Settings;

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
        new Settings;
        new Notices;
        new Required;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    public function dispatch_actions()
    {
        add_filter('woocommerce_get_settings_pages', function ($settings) {
            $settings[] = require_once __DIR__ . '/Utils/Settings.php';
            return $settings;
        });
    }
}
