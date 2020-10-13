<?php

namespace SpringDevs\WcBooking;

/**
 * Class Installer
 * @package SpringDevs\WcBooking
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
        $this->create_tables();
    }

    /**
     * Add time and version on DB
     */
    public function add_version()
    {
        $installed = get_option('simple booking_installed');

        if (!$installed) {
            update_option('simple booking_installed', time());
        }

        if (!get_term_by('slug', 'bookable', 'product_type')) {
            wp_insert_term('bookable', 'product_type');
        }

        update_option('simple booking_version', WCBOOKING_ASSETS_VERSION);
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_tables()
    {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
    }
}
