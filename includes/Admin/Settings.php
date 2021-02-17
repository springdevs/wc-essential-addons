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
        add_filter('woocommerce_get_settings_wcma', [$this, 'settings_content']);
    }

    public function add_section($sections)
    {
        $sections[''] = __('General', 'sdevs_wea');
        return $sections;
    }

    public function settings_content($settings)
    {
        global $current_section;
        if ($current_section == '') {
            $sdevs_settings   = [];
            $sdevs_settings[] = [
                'name' => __('General Settings', 'sdevs_wea'),
                'type' => 'title',
                'desc' => __('The following options are used to configure our plugin', 'sdevs_wea'),
                'id'   => 'sdevs_main',
            ];
            $sdevs_settings[] = array('type' => 'sectionend', 'id' => 'sdevs_main');
            return $sdevs_settings;
        }
        return $settings;
    }
}
