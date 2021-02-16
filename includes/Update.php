<?php

namespace SpringDevs\WcEssentialAddons;

/**
 * Class Update
 */
class Update
{
    public function __construct()
    {
        $plugin_version = get_option('sdevs_wea_version');
        if ($plugin_version == SDEVS_WEA_ASSETS_VERSION) return;
        if ($plugin_version == '1.0.1') $this->migrate_v102();
    }

    public function migrate_v102()
    {
        $active_modules = get_option("sdevs_wea_activated_modules", []);
        $all_modules = get_option('sdevs_wea_modules', []);
        $new_modules = [];
        foreach ($all_modules as $key => $value) {
            if (array_key_exists($key, $active_modules)) {
                $new_modules[$key] = $value;
            }
        }
        update_option('sdevs_wea_activated_modules', $new_modules);
        update_option('sdevs_wea_version', SDEVS_WEA_ASSETS_VERSION);
    }
}
