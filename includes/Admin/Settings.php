<?php

namespace SpringDevs\WcEssentialAddons\Admin;

/**
 * Settings Handler
 *
 * Class Settings
 */
class Settings
{
    public function __construct()
    {
        add_filter('woocommerce_get_sections_wcma', [$this, 'add_section'], 10);
    }

    public function add_section($sections)
    {
        $sections[''] = __('General', 'sdevs_wea');
        return $sections;
    }
}
