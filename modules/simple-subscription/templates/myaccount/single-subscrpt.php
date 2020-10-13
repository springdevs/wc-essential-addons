<?php
if (!isset($id)) return;
if (!get_the_title($id)) return;
do_action("before_single_subscrpt_content");
$post_meta = get_post_meta($id, "_subscrpt_order_general", true);
$order = wc_get_order($post_meta['order_id']);
$product = wc_get_product($post_meta['product_id']);
$status = get_post_status($id);
?>

<table class="shop_table subscription_details">
    <tbody>
        <tr>
            <td><?php _e('Order', 'sdevs_wea'); ?></td>
            <td><a href="<?php echo get_permalink(wc_get_page_id('myaccount')) . "view-order/" . $post_meta['order_id']; ?>" target="_blank"># <?php echo $post_meta['order_id']; ?></a></td>
        </tr>
        <tr>
            <td><?php _e('Status', 'sdevs_wea'); ?></td>
            <td><?php echo $status; ?></td>
        </tr>
        <tr>
            <td><?php _e('Start date', 'sdevs_wea'); ?></td>
            <td><?php echo date('F d, Y', $post_meta['start_date']); ?></td>
        </tr>
        <?php if ($post_meta['trial'] == null) : ?>
            <tr>
                <td><?php _e('Next payment date', 'sdevs_wea'); ?></td>
                <td><?php echo date('F d, Y', $post_meta['next_date']); ?></td>
            </tr>
        <?php else : ?>
            <tr>
                <td><?php _e('Trial', 'sdevs_wea'); ?></td>
                <td><?php echo $post_meta['trial']; ?></td>
            </tr>
            <tr>
                <td><?php _e('Trial End & First Billing', 'sdevs_wea'); ?></td>
                <td><?php echo date('F d, Y', $post_meta['start_date']); ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <td><?php _e('Payment', 'sdevs_wea'); ?></td>
            <td>
                <span data-is_manual="yes" class="subscription-payment-method"><?php echo $order->get_payment_method_title(); ?></span>
            </td>
        </tr>
        <?php
        $subscrpt_nonce = wp_create_nonce('subscrpt_nonce');
        $product_meta = $product->get_meta('subscrpt_general', true);
        ?>
        <?php if ($status != "cancelled") : ?>
            <tr>
                <td><?php _e('Actions', 'sdevs_wea'); ?></td>
                <td>
                    <?php if (($status == "pending" || $status == "active" || $status == "on_hold") && $product_meta['user_cancell'] == 'yes') : ?>
                        <a href="<?php echo get_permalink(wc_get_page_id('myaccount')) . "view-subscrpt/" . $id . "?subscrpt_id=" . $id . "&action=cancelled&wpnonce=" . $subscrpt_nonce; ?>" class="button cancel">Cancel</a>
                    <?php elseif (trim($status) == trim("expired")) : ?>
                        <a href="<?php echo get_permalink(wc_get_page_id('myaccount')) . "view-subscrpt/" . $id . "?subscrpt_id=" . $id . "&action=renew&wpnonce=" . $subscrpt_nonce; ?>" class="button subscription_renewal_early">Renew now</a>
                    <?php elseif (trim($status) == trim("pe_cancelled")) : ?>
                        <a href="" class="button subscription_renewal_early">Reactive</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2><?php _e('Subscription totals', 'sdevs_wea'); ?></h2>

<table class="shop_table order_details">
    <thead>
        <tr>
            <th class="product-name"><?php _e('Product', 'sdevs_wea'); ?></th>
            <th class="product-total"><?php _e('Total', 'sdevs_wea'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr class="order_item">
            <td class="product-name">
                <a href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_name(); ?></a>
                <strong class="product-quantity">× <?php echo $post_meta['qty']; ?></strong> </td>
            <td class="product-total">
                <span class="woocommerce-Price-amount amount"><?php echo $post_meta['subtotal_price_html']; ?></span>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row"><?php _e('Subtotal', 'sdevs_wea'); ?>:</th>
            <td>
                <span class="woocommerce-Price-amount amount"><?php echo $post_meta['subtotal_price_html']; ?></span>
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Total', 'sdevs_wea'); ?>:</th>
            <td>
                <span class="woocommerce-Price-amount amount">
                    <?php echo $post_meta['total_price_html']; ?>
                </span>
            </td>
        </tr>
    </tfoot>
</table>

<section class="woocommerce-customer-details">
    <h2 class="woocommerce-column__title"><?php _e('Billing address', 'sdevs_wea'); ?></h2>
    <address>
        <?php echo $order->get_formatted_billing_address(); ?>
        <p class="woocommerce-customer-details--phone"><?php echo $order->get_billing_phone(); ?></p>
        <p class="woocommerce-customer-details--email"><?php echo $order->get_billing_email(); ?></p>
    </address>
</section>
<div class="clear"></div>