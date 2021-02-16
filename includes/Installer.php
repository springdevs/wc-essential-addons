<?php

namespace SpringDevs\WcEssentialAddons;

/**
 * Class Installer
 */
class Installer
{
    /**
     * Run the installer
     *
     * @return void
     */
    public function run()
    {
        $this->add_version();
    }

    /**
     * Add time and version on DB
     */
    public function add_version()
    {
        $installed = get_option('sdevs_wea_installed');

        if (!$installed) {
            update_option('sdevs_wea_installed', time());
        }

        update_option('sdevs_wea_version', SDEVS_WEA_ASSETS_VERSION);
    }
}
