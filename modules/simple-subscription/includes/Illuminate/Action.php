<?php

namespace SpringDevs\WcSubscription\Illuminate;

/**
 * Action [ helper class ]
 * @package SpringDevs\WcSubscription\Illuminate
 */
class Action
{
    static private $expired_items;
    static private $active_items;
    static private $pending_items;
    static private $cancelled_items;

    static public function status($action, $author, $data = [])
    {
        self::get($author);
        self::edit($action, $data);
        self::user($author, $action);
        self::update($author);
    }

    static private function get($author)
    {
        $expired_items = get_user_meta($author, '_subscrpt_expired_items', true);
        $active_items = get_user_meta($author, '_subscrpt_active_items', true);
        $pending_items = get_user_meta($author, '_subscrpt_pending_items', true);
        $cancelled_items = get_user_meta($author, '_subscrpt_cancelled_items', true);

        self::$expired_items = is_array($expired_items) ? $expired_items : [];
        self::$active_items = is_array($active_items) ? $active_items : [];
        self::$pending_items = is_array($pending_items) ? $pending_items : [];
        self::$cancelled_items = is_array($cancelled_items) ? $cancelled_items : [];
    }

    static private function edit($action, $data)
    {
        switch ($action) {
            case 'expired':
                self::expired($data);
                break;
            case 'active':
                self::active($data);
                break;
            case 'renew':
                self::renew($data);
                break;
            case 'pending':
                self::pending($data);
                break;
            case 'cancelled':
                self::cancelled($data);
                break;
        }
    }

    static private function user($author, $action)
    {
        $user = new \WP_User($author);
        if (!empty($user->roles) && is_array($user->roles) && in_array('administrator', $user->roles)) return;
        if ($action == 'active') $user->set_role(get_option('subscrpt_active_role', 'subscriber'));
        if (($action == 'cancelled' || $action == 'expired') && (count(self::$active_items) == 0 && count(self::$pending_items) == 0)) {
            $user->set_role(get_option('subscrpt_unactive_role', 'customer'));
        }
    }

    static private function expired($data)
    {
        if (!in_array($data, self::$expired_items)) array_push(self::$expired_items, $data);

        if (in_array($data, self::$active_items)) {
            $key = array_search($data, self::$active_items);
            unset(self::$active_items[$key]);
        }

        if (in_array($data, self::$pending_items)) {
            $key = array_search($data, self::$pending_items);
            unset(self::$pending_items[$key]);
        }

        if (in_array($data, self::$cancelled_items)) {
            $key = array_search($data, self::$cancelled_items);
            unset(self::$cancelled_items[$key]);
        }

        $post_meta = get_post_meta($data['post'], '_subscrpt_order_general', true);
        if ($post_meta['trial'] != null && time() >= $post_meta['start_date']) {
            $post_meta['trial'] = null;
            update_post_meta($data['post'], '_subscrpt_order_general', $post_meta);
        }

        $comment_id = wp_insert_comment([
            "comment_agent" => "simple-subscriptions",
            "comment_author" => "simple-subscriptions",
            "comment_content" => __('Subscription is Expired ', 'sdevs_wea'),
            "comment_post_ID" => $data['post'],
            "comment_type" => "order_note"
        ]);
        update_comment_meta($comment_id, 'subscrpt_activity', __('Subscription Expired', 'sdevs_wea'));
        do_action('subscrpt_when_product_expired', $data['post'], $data['product'], $data, false);
    }

    static private function active($data)
    {
        if (!in_array($data, self::$active_items)) array_push(self::$active_items, $data);

        if (in_array($data, self::$expired_items)) {
            $key = array_search($data, self::$expired_items);
            unset(self::$expired_items[$key]);
        }

        if (in_array($data, self::$pending_items)) {
            $key = array_search($data, self::$pending_items);
            unset(self::$pending_items[$key]);
        }

        if (in_array($data, self::$cancelled_items)) {
            $key = array_search($data, self::$cancelled_items);
            unset(self::$cancelled_items[$key]);
        }

        $comment_id = wp_insert_comment([
            "comment_agent" => "simple-subscriptions",
            "comment_author" => "simple-subscriptions",
            "comment_content" => __('Subscription activated.Next payment due date set. ', 'sdevs_wea'),
            "comment_post_ID" => $data['post'],
            "comment_type" => "order_note"
        ]);
        update_comment_meta($comment_id, 'subscrpt_activity', __('Subscription Activated', 'sdevs_wea'));
    }

    static private function renew($data)
    {
        $post_meta = get_post_meta($data['post'], '_subscrpt_order_general', true);
        if ($post_meta['trial'] != null) {
            $post_meta['trial'] = null;
            update_post_meta($data['post'], '_subscrpt_order_general', $post_meta);
        }
        do_action('subscrpt_when_product_expired', $data['post'], $data['product'], $data, true);
    }

    static private function pending($data)
    {
        if (!in_array($data, self::$pending_items)) array_push(self::$pending_items, $data);

        if (in_array($data, self::$expired_items)) {
            $key = array_search($data, self::$expired_items);
            unset(self::$expired_items[$key]);
        }

        if (in_array($data, self::$active_items)) {
            $key = array_search($data, self::$active_items);
            unset(self::$active_items[$key]);
        }

        if (in_array($data, self::$cancelled_items)) {
            $key = array_search($data, self::$cancelled_items);
            unset(self::$cancelled_items[$key]);
        }

        $comment_id = wp_insert_comment([
            "comment_agent" => "simple-subscriptions",
            "comment_author" => "simple-subscriptions",
            "comment_content" => __('Subscription is pending.', 'sdevs_wea'),
            "comment_post_ID" => $data['post'],
            "comment_type" => "order_note"
        ]);
        update_comment_meta($comment_id, 'subscrpt_activity', __('Subscription Pending', 'sdevs_wea'));
    }

    static private function cancelled($data)
    {
        if (!in_array($data, self::$cancelled_items)) array_push(self::$cancelled_items, $data);

        if (in_array($data, self::$expired_items)) {
            $key = array_search($data, self::$expired_items);
            unset(self::$expired_items[$key]);
        }

        if (in_array($data, self::$active_items)) {
            $key = array_search($data, self::$active_items);
            unset(self::$active_items[$key]);
        }

        if (in_array($data, self::$pending_items)) {
            $key = array_search($data, self::$pending_items);
            unset(self::$pending_items[$key]);
        }

        $comment_id = wp_insert_comment([
            "comment_agent" => "simple-subscriptions",
            "comment_author" => "simple-subscriptions",
            "comment_content" => __('Subscription is Cancelled.', 'sdevs_wea'),
            "comment_post_ID" => $data['post'],
            "comment_type" => "order_note"
        ]);
        update_comment_meta($comment_id, 'subscrpt_activity', __('Subscription Cancelled', 'sdevs_wea'));
    }

    static private function update($author)
    {
        update_user_meta($author, '_subscrpt_expired_items', self::$expired_items);
        update_user_meta($author, '_subscrpt_active_items', self::$active_items);
        update_user_meta($author, '_subscrpt_pending_items', self::$pending_items);
        update_user_meta($author, '_subscrpt_cancelled_items', self::$cancelled_items);
    }
}
