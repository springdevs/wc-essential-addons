<?php


namespace SpringDevs\WcBooking\Illuminate;


class OrderStatus
{
    public function __construct()
    {
        add_action('init', [$this, 'custom_post_status']);
    }

    /**
     * Register Custom Post Status for bookable_order posts
     */
    public function custom_post_status()
    {
        register_post_status('paid', array(
            'label'                     => _x('Paid', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['bookable_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('unpaid', array(
            'label'                     => _x('Unpaid', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Unpaid <span class="count">(%s)</span>', 'Unpaid <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['bookable_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('pending_conf', array(
            'label'                     => _x('Pending Confirmation', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Pending Confirmation <span class="count">(%s)</span>', 'Pending Confirmation <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['bookable_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('confirmed', array(
            'label'                     => _x('Confirmed', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['bookable_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('complete', array(
            'label'                     => _x('Complete', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Complete <span class="count">(%s)</span>', 'Complete <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['bookable_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('cancelled', array(
            'label'                     => _x('Cancelled', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['bookable_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));
    }
}
