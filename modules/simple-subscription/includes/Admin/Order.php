<?php

namespace SpringDevs\WcSubscription\Admin;

/**
 * Order class
 * @package SpringDevs\WcSubscription\Admin
 */
class Order
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
    }

    public function add_meta_boxes()
    {
        add_meta_box(
            'subscrpt_order_related',
            __('Related Subscriptions', 'sdevs_wea'),
            [$this, 'subscrpt_order_related'],
            'shop_order',
            'normal',
            'default'
        );
    }

    public function subscrpt_order_related()
    {
        $order_id = get_the_ID();
        $order_meta = get_post_meta($order_id, "_order_subscrpt_data", true);
        if (empty($order_meta) && is_array($order_meta) && !$order_meta['status']) return;
?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th></th>
                    <th><?php _e('Started on', 'sdevs_wea'); ?></th>
                    <th><?php _e('Recurring', 'sdevs_wea'); ?></th>
                    <th><?php _e('Expiry date', 'sdevs_wea'); ?></th>
                    <th><?php _e('Status', 'sdevs_wea'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($order_meta['posts'] as $post) :
                    if (get_the_title($post) != "") :
                        $post_meta = get_post_meta($post, "_subscrpt_order_general", true);
                ?>
                        <tr>
                            <td>
                                <a href="<?php echo get_edit_post_link($post); ?>" target="_blank">#<?php echo $post; ?> - <?php echo get_the_title($post_meta['product_id']); ?></a>
                            </td>
                            <td>
                                <?php echo $post_meta['trial'] == null ? date('F d, Y', $post_meta['start_date']) : "+" . $post_meta['trial'] . " " . __('free trial', 'sdevs_wea'); ?>
                            </td>
                            <td><?php echo $post_meta['total_price_html']; ?></td>
                            <td><?php echo $post_meta['trial'] == null ? date('F d, Y', $post_meta['next_date']) : date('F d, Y', $post_meta['start_date']); ?></td>
                            <td><?php echo get_post_status($post); ?></td>
                        </tr>
                <?php endif;
                endforeach; ?>
            </tbody>
        </table>
<?php
    }
}
