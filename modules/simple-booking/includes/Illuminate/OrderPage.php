<?php

namespace SpringDevs\WcBooking\Illuminate;

/**
 * Class OrderPage || Customize Order
 * @package SpringDevs\WcBooking\Illuminate
 */
class OrderPage
{
    public function __construct()
    {
        add_action('init', [$this, 'register_custom_order_status']);
        add_filter('wc_order_statuses', [$this, 'custom_order_status']);
    }

    public function register_custom_order_status()
    {
        register_post_status('wc-reconf', array(
            'label'                     => 'requires confirmation',
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop('Require Confirmation <span class="count">(%s)</span>', 'Requests<span class="count">(%s)</span>')
        ));
    }

    public function custom_order_status($order_statuses)
    {
        $order_statuses['wc-reconf'] = _x('Require Confirmation', 'Order status', 'sdevs_wea');
        return $order_statuses;
    }
}
