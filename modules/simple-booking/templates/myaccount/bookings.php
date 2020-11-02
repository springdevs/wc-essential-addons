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
    'post_type' => 'bookable_order',
    'post_status' => ["paid", "unpaid", "pending_conf", "confirmed", "complete", "cancelled"]
];

$postslist = new WP_Query($args);
?>

<table class="shop_table my_account_bookings">
    <thead>
        <tr>
            <th scope="col" class="booking-id"><?php esc_html_e('ID', 'sdevs_wea'); ?></th>
            <th scope="col" class="order-number"><?php esc_html_e('Order', 'sdevs_wea'); ?></th>
            <th scope="col" class="booked-title"><?php esc_html_e('Booked', 'sdevs_wea'); ?></th>
            <th scope="col" class="booking-date-time"><?php esc_html_e('Date - Time', 'sdevs_wea'); ?></th>
            <th scope="col" class="booking-status"><?php esc_html_e('Status', 'sdevs_wea'); ?></th>
            <!--            <th scope="col" class="booking-cancel"></th>-->
        </tr>
    </thead>
    <tbody>
        <?php
        if ($postslist->have_posts()) :
            while ($postslist->have_posts()) : $postslist->the_post();
                $post_meta = get_post_meta(get_the_ID(), "_booking_order_meta", true);
                $product = wc_get_product($post_meta["product_id"]);
                $attributes = [];
                $order = wc_get_order($post_meta["order_id"]);
                $order_items = $order && is_array($order->get_items()) ? $order->get_items() : [];
                foreach ($order_items as $key => $item) {
                    foreach ($item->get_meta_data() as $data) {
                        if ($data->key != "Date" && $data->key != "Time") $attributes[$data->key] = $data->value;
                    }
                }
        ?>
                <tr>
                    <td><?php the_ID(); ?></td>
                    <td><a href="<?php echo "../view-order/" . $post_meta["order_id"]; ?>" target="_blank"><?php echo $post_meta["order_id"]; ?></a></td>
                    <td>
                        <?php if ($product) : ?>
                            <a href="<?php the_permalink($post_meta["product_id"]); ?>"><?php echo $product->get_title(); ?></a><br />
                            <?php foreach ($attributes as $key => $value) : ?>
                                <strong><?php echo $key; ?> : </strong> <?php echo $value; ?><br />
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $post_meta["date"] . " - " . $post_meta["time"]; ?></td>
                    <td><?php echo (get_post_status() == "pending_conf" ? "Pending Confirmation" : get_post_status()); ?></td>
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
