<?php

/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/simple-booking/myaccount/bookings.php
 *
 */

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = [
    'author' => get_current_user_id(),
    'posts_per_page' => 10,
    'paged' => $paged,
    'post_type' => 'subscrpt_order',
    'post_status' => ["pending", "active", "on_hold", "cancelled", "expired", "pe_cancelled"]
];

$postslist = new WP_Query($args);
?>

<table class="shop_table my_account_subscrpt">
    <thead>
        <tr>
            <th scope="col" class="subscrpt-id"><?php esc_html_e('Subscription', 'sdevs_wea'); ?></th>
            <th scope="col" class="order-status"><?php esc_html_e('Status', 'sdevs_wea'); ?></th>
            <th scope="col" class="order-product"><?php esc_html_e('Product', 'sdevs_wea'); ?></th>
            <th scope="col" class="subscrpt-next-date"><?php esc_html_e('Next Payment', 'sdevs_wea'); ?></th>
            <th scope="col" class="subscrpt-total"><?php esc_html_e('Total', 'sdevs_wea'); ?></th>
            <th scope="col" class="subscrpt-action"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($postslist->have_posts()) :
            while ($postslist->have_posts()) : $postslist->the_post();
                $post_meta = get_post_meta(get_the_ID(), "_subscrpt_order_general", true);
                $product = wc_get_product($post_meta["product_id"]);
        ?>
                <tr>
                    <td><?php the_ID(); ?></td>
                    <td><?php echo get_post_status(); ?></td>
                    <td><a href="<?php the_permalink($post_meta['product_id']); ?>" target="_blank"><?php echo get_the_title($post_meta['product_id']); ?></a></td>
                    <?php if ($post_meta['trial'] == null) : ?>
                        <td><?php echo date('F d, Y', $post_meta['next_date']); ?></td>
                    <?php else : ?>
                        <td><small>First Billing : </small><?php echo date('F d, Y', $post_meta['start_date']); ?></td>
                    <?php endif; ?>
                    <td><?php echo $post_meta['subtotal_price_html']; ?></td>
                    <td>
                        <a href="<?php echo get_permalink(wc_get_page_id('myaccount')) . "view-subscrpt/" . get_the_ID(); ?>" class="woocommerce-button button view">View</a>
                    </td>
                </tr>
        <?php
            endwhile;
            next_posts_link('Older Entries', $postslist->max_num_pages);
            previous_posts_link('Next Entries &raquo;');
            wp_reset_postdata();
        endif;
        ?>
    </tbody>
</table>
<?php