<?php

namespace springdevs\WooAdvanceCoupon\Frontend\Helpers;

use WC_Coupon;
use WC_Discounts;

/**
 * Coupon Validator class
 */
class Validator
{

    static public function check($coupon = null, $post_id = null, $sdwac_coupon_id = null)
    {
        $result = self::sdwac_coupon_fiter_validate($coupon, $post_id, $sdwac_coupon_id) ? true : false;
        if ($result) {
            $result = self::sdwac_coupon_multi_validate($coupon, $post_id) ? true : false;
        }
        if ($result) {
            $result = self::sdwac_coupon_rules_validate($coupon, $post_id, $sdwac_coupon_id) ? true : false;
        }
        $result = apply_filters("sdwac_coupon_validator", $result);
        return $result;
    }

    static public function sdwac_coupon_multi_validate($coupon, $post_id)
    {
        $sdwac_coupon_woo_setting_multi = get_option("sdwac_woo_setting_multi");
        $applied_coupons = WC()->cart->get_applied_coupons();
        if (count($applied_coupons) > 1) {
            if ($sdwac_coupon_woo_setting_multi == "no" & $applied_coupons[0] != $coupon) {
                wc_clear_notices();
                return false;
            }
        }
        return true;
    }

    static public function sdwac_coupon_fiter_validate($coupon, $post_id, $sdwac_coupon_id)
    {
        $result = true;
        if ($sdwac_coupon_id == null) {
            $post_meta = get_post_meta($post_id, "sdwac_coupon_panel", true);
            if (empty($post_meta["list_id"])) {
                return $result;
            }
            $filters = get_post_meta($post_meta["list_id"], "sdwac_coupon_filters", true);
            $sdwac_coupon_main = get_post_meta($post_meta["list_id"], "sdwac_coupon_main", true);
        } else {
            $filters = get_post_meta($sdwac_coupon_id, "sdwac_coupon_filters", true);
            $sdwac_coupon_main = get_post_meta($sdwac_coupon_id, "sdwac_coupon_main", true);
        }

        if (!$filters || !$sdwac_coupon_main || $sdwac_coupon_main["type"] == "product") {
            return $result;
        }

        foreach ($filters as $filter) {
            if ($filter["type"] == "all_products") {
                $result = true;
                break;
            } else {
                $products = WC()->cart->get_cart();
                $sdwac_coupon_products = [];
                $productLists = [];
                foreach ($filter["items"] as $filterItem) {
                    array_push($sdwac_coupon_products, $filterItem["value"]);
                }
                foreach ($products as $values) {
                    array_push($productLists, $values["product_id"]);
                }
                $productLists = array_map(function ($piece) {
                    return (string) $piece;
                }, $productLists);
                if ($filter["lists"] == "inList") {
                    foreach ($sdwac_coupon_products as $sdwac_coupon_product) {
                        $match = in_array($sdwac_coupon_product, $productLists);
                        if ($match == false) {
                            $result = false;
                            break;
                        }
                    }
                } else {
                    foreach ($sdwac_coupon_products as $sdwac_coupon_product) {
                        $result = in_array($sdwac_coupon_product, $productLists);
                        if ($result == true) {
                            break;
                        }
                    }
                }
            }
        }
        return $result;
    }

    static function sdwac_coupon_rules_validate($coupon, $post_id, $sdwac_coupon_id)
    {
        $result = true;
        if ($sdwac_coupon_id == null) {
            $post_meta = get_post_meta($post_id, "sdwac_coupon_panel", true);
            if (empty($post_meta["list_id"])) {
                return $result;
            } else {
                $rules = get_post_meta($post_meta["list_id"], "sdwac_coupon_rules", true);
                if (!$rules || $rules["rules"] == null) {
                    return $result;
                }
            }
        } else {
            $rules = get_post_meta($sdwac_coupon_id, "sdwac_coupon_rules", true);
            if (!$rules || $rules["rules"] == null) {
                return $result;
            }
        }


        $relation = $rules["relation"];

        foreach ($rules["rules"] as $rule) {
            $operator = $rule["operator"];
            $value = $rule["item_count"];
            $calculate = $rule["calculate"];
            if ($rule["type"] == "cart_subtotal") {
                switch ($operator) {
                    case 'less_than':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart") {
                            if (!($subtotal < $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $amount = self::sdwac_coupon_cart_filter_subtotal($post_meta["list_id"]);
                            if (!($amount < (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'less_than_or_equal':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart") {
                            if (!($subtotal <= $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $amount = self::sdwac_coupon_cart_filter_subtotal($post_meta["list_id"]);
                            if (!($amount <= (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'greater_than':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart") {
                            if (!($subtotal > $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $amount = self::sdwac_coupon_cart_filter_subtotal($post_meta["list_id"]);
                            if (!($amount > (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'greater_than_or_equal':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart") {
                            if (!($subtotal >= $value)) {
                                $result = false;
                            }
                        } else if ($calculate >= "from_filter") {
                            $amount = self::sdwac_coupon_cart_filter_subtotal($post_meta["list_id"]);
                            if (!($amount < (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                }
                if ($result === true) {
                    if ($relation == "match_any") {
                        break;
                    }
                }
            } else if ($rule["type"] == "cart_line_items_count") {
                $line_total = count(WC()->cart->get_cart());
                switch ($operator) {
                    case 'less_than':
                        if ($calculate == "from_cart") {
                            if (!($line_total < $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $total = self::sdwac_coupon_cart_filter_line_total($post_meta["list_id"]);
                            if (!($total < (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'less_than_or_equal':
                        if ($calculate == "from_cart") {
                            if (!($line_total <= $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $total = self::sdwac_coupon_cart_filter_line_total($post_meta["list_id"]);
                            if (!($total <= (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'greater_than':
                        if ($calculate == "from_cart") {
                            if (!($line_total > $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $total = self::sdwac_coupon_cart_filter_line_total($post_meta["list_id"]);
                            if (!($total > (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'greater_than_or_equal':
                        if ($calculate == "from_cart") {
                            if (!($line_total >= $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $total = self::sdwac_coupon_cart_filter_line_total($post_meta["list_id"]);
                            if (!($total >= (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                }
            }
        }
        return $result;
    }

    static public function sdwac_coupon_cart_filter_subtotal($post_id)
    {
        $filters = get_post_meta($post_id, "sdwac_coupon_filters", true);
        $sdwac_coupon_products = [];
        $amount = 0;
        foreach ($filters as $filter) {
            foreach ($filter["items"] as $filterItem) {
                array_push($sdwac_coupon_products, $filterItem["value"]);
            }
        }
        foreach (WC()->cart->get_cart() as $value) {
            foreach ($sdwac_coupon_products as $sdwac_coupon_product) {
                if ((string) $value["product_id"] == $sdwac_coupon_product) {
                    $amount = $amount + $value["line_subtotal"];
                }
            }
        }
        return $amount;
    }

    static public function sdwac_coupon_cart_filter_line_total($post_id)
    {
        $filters = get_post_meta($post_id, "sdwac_coupon_filters", true);
        $sdwac_coupon_products = [];
        $total = 0;
        foreach ($filters as $filter) {
            foreach ($filter["items"] as $filterItem) {
                array_push($sdwac_coupon_products, $filterItem["value"]);
            }
        }
        foreach (WC()->cart->get_cart() as $value) {
            foreach ($sdwac_coupon_products as $sdwac_coupon_product) {
                if ((string) $value["product_id"] == $sdwac_coupon_product) {
                    $total += 1;
                }
            }
        }
        return $total;
    }

    /**
     * Default Basic Validator [ not used anywhere ]
     *
     * @return Boolean true / false
     **/
    static public function basic_validate($coupon)
    {
        $coupon = new WC_Coupon($coupon);
        $discounts = new WC_Discounts(WC()->cart);
        $valid_response = $discounts->is_coupon_valid($coupon);
        if (
            is_wp_error($valid_response)
        ) {
            return false;
        } else {
            return true;
        }
    }
}
