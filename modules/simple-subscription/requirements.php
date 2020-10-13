<?php

if (!wp_next_scheduled('subscrpt_daily_cron')) {
    wp_schedule_event(time(), 'daily', 'subscrpt_daily_cron');
}
