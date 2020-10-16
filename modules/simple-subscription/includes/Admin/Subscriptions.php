<?php

namespace SpringDevs\WcSubscription\Admin;

use SpringDevs\WcSubscription\Illuminate\Action;

/**
 * Subscriptions class
 * @package SpringDevs\WcSubscription\Admin
 */
class Subscriptions
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'custom_enqueue_scripts']);
        add_filter('post_row_actions', [$this, 'post_row_actions'], 10, 2);
        add_filter('bulk_actions-edit-subscrpt_order', [$this, 'edit_bulk_actions']);
        add_filter('manage_subscrpt_order_posts_columns', [$this, 'add_custom_columns']);
        add_action('manage_subscrpt_order_posts_custom_column', [$this, 'add_custom_columns_data'], 10, 2);
        add_action('add_meta_boxes', array($this, "create_meta_boxes"));
        add_action('admin_head-post.php', [$this, "some_styles"]);
        add_action('admin_head-post-new.php', [$this, "some_styles"]);
        add_action('admin_footer-post.php', [$this, "some_scripts"]);
        add_action('admin_footer-post-new.php', [$this, "some_scripts"]);
        add_action('save_post', [$this, 'save_subscrpt_order']);
        add_filter('woocommerce_order_item_get_formatted_meta_data', [$this, 'remove_order_meta'], 10, 1);
    }

    public function edit_bulk_actions($options)
    {
        unset($options['trash']);
        return $options;
    }

    public function remove_order_meta($formatted_meta)
    {
        $temp_metas = [];
        foreach ($formatted_meta as $key => $meta) {
            if (isset($meta->key) && !in_array($meta->key, ['_renew_subscrpt'])) {
                $temp_metas[$key] = $meta;
            }
        }
        return $temp_metas;
    }

    public function custom_enqueue_scripts()
    {
        wp_enqueue_style("subscrpt_admin_css");
    }

    public function post_row_actions($unset_actions, $post)
    {
        global $current_screen;
        if ($current_screen->post_type != 'subscrpt_order')
            return $unset_actions;
        unset($unset_actions['inline hide-if-no-js']);
        unset($unset_actions['view']);
        unset($unset_actions['trash']);
        unset($unset_actions['edit']);
        return $unset_actions;
    }

    public function add_custom_columns($columns)
    {
        $columns['subscrpt_start_date'] = __('Start Date', 'sdevs_wea');
        $columns['subscrpt_customer'] = __('Customer', 'sdevs_wea');
        $columns['subscrpt_next_date'] = __('Next Date', 'sdevs_wea');
        $columns['subscrpt_status'] = __('Status', 'sdevs_wea');
        unset($columns['date']);
        return $columns;
    }

    public function add_custom_columns_data($column, $post_id)
    {
        $post_meta = get_post_meta($post_id, "_subscrpt_order_general", true);
        $order = wc_get_order($post_meta['order_id']);
        if ($column == "subscrpt_start_date") {
            echo date('F d, Y', $post_meta['start_date']);
        } elseif ($column == "subscrpt_customer") {
?>
            <?php echo $order->get_formatted_billing_full_name(); ?>
            <br />
            <a href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a>
            <br />
            Phone : <a href="tel:<?php echo $order->get_billing_phone(); ?>"><?php echo $order->get_billing_phone(); ?></a>
        <?php
        } elseif ($column == "subscrpt_next_date") {
            echo date('F d, Y', $post_meta['next_date']);
        } elseif ($column == "subscrpt_status") {
            echo get_post_status($post_id);
        }
    }

    public function create_meta_boxes()
    {
        remove_meta_box('submitdiv', 'subscrpt_order', 'side');
        // Save Data
        add_meta_box(
            'subscrpt_order_save_post',
            __('Subscription Action', 'sdevs_wea'),
            [$this, 'subscrpt_order_save_post'],
            'subscrpt_order',
            'side',
            'default'
        );

        add_meta_box(
            'subscrpt_customer_info',
            __('Customer Info', 'sdevs_wea'),
            [$this, 'subscrpt_customer_info'],
            'subscrpt_order',
            'side',
            'default'
        );

        add_meta_box(
            'subscrpt_order_info',
            __('Subscription Info', 'sdevs_wea'),
            [$this, 'subscrpt_order_info'],
            'subscrpt_order',
            'normal',
            'default'
        );

        add_meta_box(
            'subscrpt_order_history',
            __('Subscription History', 'sdevs_wea'),
            [$this, 'subscrpt_order_history'],
            'subscrpt_order',
            'normal',
            'default'
        );

        add_meta_box(
            'subscrpt_order_activities',
            __('Subscription Activities', 'sdevs_wea'),
            [$this, 'subscrpt_order_activities'],
            'subscrpt_order',
            'normal',
            'default'
        );
    }

    public function subscrpt_order_history()
    {
        $order_histories = get_post_meta(get_the_ID(), '_subscrpt_order_history', true);
        rsort($order_histories);
        ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Order', 'sdevs_wea'); ?></th>
                    <th></th>
                    <th><?php _e('Date', 'sdevs_wea'); ?></th>
                    <th><?php _e('Status', 'sdevs_wea'); ?></th>
                    <th><?php _e('Amount', 'sdevs_wea'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_histories as $order_history) : ?>
                    <?php
                    $order = wc_get_order($order_history['order_id']);
                    ?>
                    <tr>
                        <td><a href="<?php echo get_edit_post_link($order_history['order_id']); ?>" target="_blank"><?php echo $order_history['order_id']; ?></a></td>
                        <td><?php echo $order_history['stats']; ?></td>
                        <td><?php echo date('F d, Y', strtotime($order->get_date_created())); ?></td>
                        <td><?php echo $order->get_status(); ?></td>
                        <td><?php echo $order_history['subtotal_price_html']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php
    }

    public function subscrpt_order_activities()
    {
    ?>
        <a href="https://springdevs.com" target="_blank">
            <img style="width: 100%;" src="<?php echo WCSUBSCRIPTION_ASSETS_ASSETS . '/images/subscrpt-ads.png'; ?>" />
        </a>
    <?php
    }

    public function subscrpt_order_save_post()
    {
        $actions = [
            ["label" => __('Activate Subscription', 'sdevs_wea'), "value" => 'active'],
            ["label" => __('Pending Subscription', 'sdevs_wea'), "value" => 'pending'],
            ["label" => __('Expire Subscription', 'sdevs_wea'), "value" => 'expired'],
            ["label" => __('Cancel Subscription', 'sdevs_wea'), "value" => 'cancelled'],
        ];
        $status = get_post_status(get_the_ID());
    ?>
        <p class="subscrpt_sub_box">
            <select id="subscrpt_order_type" name="subscrpt_order_action">
                <option value=""><?php _e('choose action', 'sdevs_wea'); ?></option>
                <?php foreach ($actions as $action) : ?>
                    <option value="<?php echo $action["value"]; ?>" <?php if ($action["value"] == $status) echo "selected"; ?>><?php echo $action["label"]; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <div class="submitbox">
            <input type="submit" class="button save_order button-primary tips" name="save" value="Process">
        </div>
    <?php
    }

    public function subscrpt_customer_info()
    {
        $post_meta = get_post_meta(get_the_ID(), "_subscrpt_order_general", true);
        $order = wc_get_order($post_meta["order_id"]);
    ?>
        <table class="booking-customer-details" style="width: 100%;">
            <tbody>
                <tr>
                    <th>Name:</th>
                    <td><?php echo $order->get_formatted_billing_full_name(); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><a href="mailto:<?php echo $order->get_billing_email(); ?>"><?php echo $order->get_billing_email(); ?></a></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><?php echo $order->get_formatted_billing_address(); ?></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><?php echo $order->get_billing_phone(); ?></td>
                </tr>
                <tr class="view">
                    <th>&nbsp;</th>
                    <td><a class="button button-small" target="_blank" href="<?php echo get_edit_post_link($post_meta['order_id']); ?>">View Order</a></td>
                </tr>
            </tbody>
        </table>
    <?php
    }

    public function subscrpt_order_info()
    {
        $post_meta = get_post_meta(get_the_ID(), "_subscrpt_order_general", true);
        $order_item = null;
        $order = wc_get_order($post_meta["order_id"]);
        foreach ($order->get_items() as $cart_item) {
            if ($cart_item["product_id"] == $post_meta["product_id"]) {
                $order_item = $cart_item;
                break;
            }
        }
    ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">Product : </th>
                    <td>
                        <a href="<?php the_permalink($post_meta['product_id']); ?>" target="_blank"><?php echo get_the_title($post_meta['product_id']); ?></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Cost : </th>
                    <td><?php echo wc_price($order->get_item_subtotal($order_item, false, true), array('currency' => $order->get_currency())); ?></td>
                </tr>
                <tr>
                    <th scope="row">Qty : </th>
                    <td>x<?php echo $post_meta['qty']; ?></td>
                </tr>
                <tr>
                    <th scope="row">Amount : </th>
                    <td><strong><?php echo $post_meta['subtotal_price_html']; ?></strong></td>
                </tr>
                <?php if (!empty($post_meta['trial'])) : ?>
                    <tr>
                        <th scope="row">Trial</th>
                        <td><?php echo $post_meta['trial']; ?></td>
                    </tr>
                    <tr>
                        <th scope="row">Trial Date</th>
                        <td><?php echo " [ " . date('F d, Y', strtotime($order->get_date_created())) . " - " . date('F d, Y', strtotime($post_meta['trial'], strtotime($order->get_date_created()))) . " ] "; ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row">Started date:</th>
                    <td><?php echo date('F d, Y', $post_meta['start_date']); ?></td>
                </tr>
                <tr>
                    <th scope="row">Payment due date:</th>
                    <td><?php echo date('F d, Y', $post_meta['next_date']); ?></td>
                </tr>
                <tr>
                    <th scope="row">Payment Method:</th>
                    <td><?php echo $order->get_payment_method_title(); ?></td>
                </tr>
                <tr>
                    <th scope="row">Billing:</th>
                    <td><?php echo $order->get_formatted_billing_address(); ?></td>
                </tr>
                <tr>
                    <th scope="row">Shipping:</th>
                    <td><?php echo $order->get_formatted_shipping_address() ? $order->get_formatted_shipping_address() : "No shipping address set."; ?></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    public function some_styles()
    {
        global $post;
        if ($post->post_type == "subscrpt_order") :
        ?>
            <style>
                .submitbox {
                    display: flex;
                    justify-content: space-around;
                }

                .subscrpt_sub_box {
                    display: grid;
                    line-height: 2;
                }
            </style>
        <?php
        endif;
    }

    public function some_scripts()
    {
        global $post;
        if ($post->post_type == "subscrpt_order") :
        ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery(window).off("beforeunload", null);
                });
            </script>
<?php
        endif;
    }

    public function save_subscrpt_order($post_id)
    {
        if (wp_is_post_revision($post_id)) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!isset($_POST['subscrpt_order_action'])) return;
        remove_all_actions('save_post');

        $action = sanitize_text_field($_POST['subscrpt_order_action']);
        wp_update_post([
            'ID' => $post_id,
            'post_status' => $action
        ]);

        $post_meta = get_post_meta($post_id, "_subscrpt_order_general", true);
        $data = ["post" => $post_id, "product" => $post_meta['product_id']];
        Action::status($action, $_POST['post_author'], $data);
    }
}
