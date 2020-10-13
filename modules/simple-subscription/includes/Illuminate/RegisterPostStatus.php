<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * Class RegisterPostStatus || Register Custom PostStatus
 * @package SpringDevs\WcSubscription\Illuminate
 */
class RegisterPostStatus
{
    public function __construct()
    {
        add_action('init', [$this, 'register_post_status']);
    }

    public function register_post_status()
    {
        register_post_status('pending', array(
            'label'                     => _x('Pending', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['subscrpt_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('active', array(
            'label'                     => _x('Active', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['subscrpt_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('on_hold', array(
            'label'                     => _x('On Hold', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['subscrpt_order'],
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
            'post_type'                 => ['subscrpt_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('expired', array(
            'label'                     => _x('Expired', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['subscrpt_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));

        register_post_status('pe_cancelled', array(
            'label'                     => _x('Pending Cancellation', 'post status label', 'sdevs_wea'),
            'public'                    => true,
            'label_count'               => _n_noop('Pending Cancellation <span class="count">(%s)</span>', 'Pending Cancellation <span class="count">(%s)</span>', 'sdevs_wea'),
            'post_type'                 => ['subscrpt_order'],
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'show_in_metabox_dropdown'  => true,
            'show_in_inline_dropdown'   => true,
            'dashicon'                  => '',
        ));
    }
}
