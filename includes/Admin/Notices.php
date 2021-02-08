<?php

namespace SpringDevs\WcEssentialAddons\Admin;

/**
 * Admin Notices Handler
 *
 * Class Notices
 */
class Notices
{
    public function __construct()
    {
        add_action('admin_notices', [$this, "handle_notices"]);
    }

    public function handle_notices()
    {
        $notices = get_option("sdevs_notices", false);
        if ($notices && is_array($notices)) {
            foreach ($notices as $notice) {
                $class = 'notice notice-' . $notice['type'];
                $message = __($notice['msg'], 'sdevs_wea');
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
            }
            delete_option("sdevs_notices");
        }
    }
}
