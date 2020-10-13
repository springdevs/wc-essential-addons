<?php

namespace SpringDevs\WcSubscription;

/**
 * Class Installer
 * @package SpringDevs\WcSubscription
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
        $installed = get_option('simple subscription_installed');

        if (!$installed) {
            update_option('simple subscription_installed', time());
        }

        update_option('simple subscription_version', WCSUBSCRIPTION_ASSETS_VERSION);

        add_filter('cron_schedules', function ($schedules) {
            $schedules['every_three_minutes'] = array(
                'interval'  => 60,
                'display'   => __('Every 3 Minutes', 'textdomain')
            );
            return $schedules;
        });

        if (!wp_next_scheduled('subscrpt_daily_cron')) {
            wp_schedule_event(time(), 'daily', 'subscrpt_daily_cron');
        }
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
