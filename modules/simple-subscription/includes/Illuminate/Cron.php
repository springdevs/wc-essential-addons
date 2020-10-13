<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * Class Cron
 * @package SpringDevs\WcSubscription\Illuminate
 */
class Cron
{
    public function __construct()
    {
        add_action('subscrpt_daily_cron', [$this, 'daily_cron_task']);
    }

    public function daily_cron_task()
    {
        $active_items = get_user_meta(get_current_user_id(), '_subscrpt_active_items', true);
        foreach ($active_items as $key => $active_item) {
            $post_meta = get_post_meta($active_item['post'], '_subscrpt_order_general', true);
            if (time() >= $post_meta['next_date'] || ($post_meta['trial'] == null && time() >= $post_meta['start_date'])) {
                wp_update_post([
                    "ID" => $post_meta['post'],
                    "post_type" => "subscrpt_order",
                    "post_status" => "expired"
                ]);
                Action::status('expired', get_current_user_id(), $active_item);
            }
        }
    }
}
