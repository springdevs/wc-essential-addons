<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * Class Email
 * @package SpringDevs\WcSubscription\Illuminate
 */
class Email
{
    public function __construct()
    {
        add_action('woocommerce_email_after_order_table', function ($order) {
            $post_meta = get_post_meta(
                $order->get_id(),
                "_order_subscrpt_full_data",
                true
            );
            if (!empty($post_meta) && is_array($post_meta) && count($post_meta) > 0) :
?>
                <div style="margin-bottom: 40px;">
                    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
                        <tbody>
                            <tr>
                                <h2><?php _e('Related Subscriptions', 'sdevs_wea'); ?></h2>
                            </tr>
                            <?php
                            foreach ($post_meta as $subscrpt_meta) :
                                if (!empty($subscrpt_meta) && is_array($subscrpt_meta)) :
                                    $post = $subscrpt_meta['post_id'];
                                    $trial_status = $subscrpt_meta['trial'] == null ? false : true;
                                    $product_name = apply_filters('subscrpt_filter_product_name', get_the_title($subscrpt_meta['product_id']), $subscrpt_meta);
                                    $product_link = apply_filters('subscrpt_filter_product_permalink', get_the_permalink($subscrpt_meta['product_id']), $subscrpt_meta);
                            ?>
                                    <tr>
                                        <th class="td" scope="row" colspan="3" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: center;"><?php echo get_the_title($post); ?></th>
                                    </tr>
                                    <tr>
                                        <th class="td" scope="row" colspan="3" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><a href="<?php echo $product_link; ?>"><?php echo $product_name; ?></a>
                                            <strong class="product-quantity">×&nbsp;<?php echo $subscrpt_meta['qty']; ?></strong></th>
                                    </tr>
                                    <tr>
                                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;">Status: </th>
                                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php echo get_post_status($post); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;">Recurring amount: </th>
                                        <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php echo $subscrpt_meta['subtotal_price_html']; ?></td>
                                    </tr>
                                    <?php if ($trial_status == null) { ?>
                                        <tr>
                                            <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php _e('Next billing on', 'sdevs_wea'); ?>: </th>
                                            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php echo date('F d, Y', $subscrpt_meta['next_date']); ?></td>
                                        </tr>
                                    <?php } else { ?>
                                        <tr>
                                            <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php _e('Trial', 'sdevs_wea'); ?>: </th>
                                            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php echo $subscrpt_meta['trial']; ?></td>
                                        </tr>
                                        <tr>
                                            <th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php _e('First billing on', 'sdevs_wea'); ?>: </th>
                                            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;"><?php echo date('F d, Y', $subscrpt_meta['start_date']); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <th class="td" scope="row" colspan="3" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left; padding-bottom: 30px;"></th>
                                    </tr>
                            <?php endif;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
<?php
            endif;
        });
    }
}
