<?php

namespace springdevs\WooAdvanceCoupon\Admin;

/**
 * Admin Coupon Class
 */
class Coupon
{
    public function __construct()
    {
        add_action("edit_form_top", [$this, 'add_html_for_vue']);
        add_action("admin_enqueue_scripts", [$this, 'enqueue_assets']);
        add_action('save_post_shop_coupon', [$this, 'coupon_save_meta_post']);
    }

    public function add_html_for_vue()
    {
        $screen = get_current_screen();
        if ($screen->post_type == "shop_coupon") {
            echo "<span id='wac_post'>";
        }
    }

    public function enqueue_assets()
    {
        wp_enqueue_script('sdwac_admin_coupon');
        wp_enqueue_script("sdwac_coupon_app");
        wp_enqueue_style("sdwac_coupon_app_css");
        wp_localize_script(
            'sdwac_coupon_app',
            'sdwac_coupon_helper_obj',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
        wp_localize_script(
            'sdwac_coupon_app',
            'sdwac_coupon_post',
            array('id' => get_the_ID())
        );
    }

    /**
     * save post meta
     **/
    public function coupon_save_meta_post($post_id)
    {
        if (!isset($_POST['discount_type']) || !isset($_POST['sdwac_coupon_admin_nonce'])) return;
        if (!wp_verify_nonce($_POST["sdwac_coupon_admin_nonce"], "sdwac_coupon_admin_nonce")) wp_die(__('Sorry !! You cannot permit to access.', 'sdevs_wea'));

        $type = sanitize_text_field($_POST['discount_type']);

        if ($type == 'sdwac_product_fixed' || $type == 'sdwac_product_percent') {
            $product_list_type = sanitize_text_field($_POST['sdwac_product_lists']);
            update_post_meta($post_id, '_sdwac_coupon_meta', ["type" => $type, 'list' => $product_list_type]);
            return;
        } elseif ($type == 'sdwac_bulk') {
            $sdwac_coupon_discount = [];
            if (isset($_POST["discountLength"])) {
                $discountLength = sanitize_text_field($_POST["discountLength"]);
                for ($i = 0; $i < $discountLength; $i++) {
                    array_push($sdwac_coupon_discount, [
                        "min" => sanitize_text_field($_POST["sdwac_coupon_discount_min_" . $i]),
                        "max" => sanitize_text_field($_POST["sdwac_coupon_discount_max_" . $i]),
                        "type" => sanitize_text_field($_POST["sdwac_coupon_discount_type_" . $i]),
                        "value" => $_POST["sdwac_coupon_discount_value_" . $i] ? sanitize_text_field($_POST["sdwac_coupon_discount_value_" . $i]) : 0
                    ]);
                }
            }
            update_post_meta($post_id, '_sdwac_coupon_meta', [
                "type" => $type,
                'discounts' => $sdwac_coupon_discount,
            ]);
        }

        $post_meta = get_post_meta($post_id, '_sdwac_coupon_meta', true);
        if (!is_array($post_meta)) $post_meta = [];
        $rulesLength = sanitize_text_field($_POST["rulesLength"]);
        $relation = $_POST["sdwac_coupon_rule_relation"] ? sanitize_text_field($_POST["sdwac_coupon_rule_relation"]) : "match_all";
        $sdwac_coupon_rules = [];
        if ($rulesLength != 0) {
            for ($i = 0; $i < $rulesLength; $i++) {
                array_push($sdwac_coupon_rules, [
                    "type" => sanitize_text_field($_POST["sdwac_coupon_rule_type_" . $i]),
                    "operator" => sanitize_text_field($_POST["sdwac_coupon_rule_operator_" . $i]),
                    "item_count" => sanitize_text_field($_POST["sdwac_coupon_rule_item_" . $i]),
                    "calculate" => sanitize_text_field($_POST["sdwac_coupon_rule_calculate_" . $i])
                ]);
            }
        }
        $post_meta['relation'] = $relation;
        $post_meta['rules'] = $sdwac_coupon_rules;
        update_post_meta($post_id, '_sdwac_coupon_meta', $post_meta);
    }
}
