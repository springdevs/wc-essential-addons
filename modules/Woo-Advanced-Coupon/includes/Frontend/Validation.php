<?php

namespace springdevs\WooAdvanceCoupon\Frontend;

/**
 * Class Validation
 * Validate Coupon
 */
class Validation
{
    public function __construct()
    {
        add_filter("woocommerce_coupon_is_valid", [$this, "woocommerce_coupon_is_valid"], 10, 2);
    }

    public function woocommerce_coupon_is_valid($valid, $coupon)
    {
        $product_ids = $coupon->get_product_ids();
        $coupon_id = $coupon->get_id();
        $coupon_meta = get_post_meta($coupon_id, "_sdwac_coupon_meta", true);
        if (empty($coupon_meta) || !is_array($coupon_meta)) return $valid;
        if (isset($coupon_meta['relation']) && isset($coupon_meta['rules']) && $coupon->get_discount_type() != 'sdwac_product_percent' && $coupon->get_discount_type() != 'sdwac_product_fixed') {
            $check_rules = $this->check_rules($coupon_meta['relation'], $coupon_meta['rules'], $coupon);
            if (!$check_rules) return false;
        }
        if (!isset($coupon_meta['type'])) return $valid;
        $check_multi = $this->check_multi();
        if (!$check_multi) return false;
        if ($coupon_meta['type'] == 'sdwac_product_percent' || $coupon_meta['type'] == 'sdwac_product_fixed') {
            if ($coupon_meta['list'] == 'inList') {
                foreach (WC()->cart->get_cart() as $value) if (!in_array($value["product_id"], $product_ids)) return false;
            }
        }
        return $valid;
    }

    public function check_multi()
    {
        $sdwac_multi = get_option('sdwac_multi', 'yes');
        $applied_coupons = WC()->cart->applied_coupons;
        if ($sdwac_multi == 'no' && count($applied_coupons) > 1) return false;
        return true;
    }

    public function check_rules($relation, $rules, $coupon)
    {
        if (empty($rules) && !is_array($rules)) return true;
        $result = true;
        foreach ($rules as $rule) {
            $operator = $rule["operator"];
            $value = $rule["item_count"];
            $calculate = $rule["calculate"];
            if ($rule["type"] == "cart_subtotal") {
                switch ($operator) {
                    case 'less_than':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart") {
                            if (!($subtotal < $value && $subtotal != 0)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $amount = $this->get_coupon_filter_cart_subtotal($coupon->get_product_ids());
                            if (!($amount < (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'less_than_or_equal':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart" && $subtotal != 0) {
                            if (!($subtotal <= $value)) {
                                $result = false;
                            }
                        } else if ($calculate == "from_filter") {
                            $amount = $this->get_coupon_filter_cart_subtotal($coupon->get_product_ids());
                            if (!($amount <= (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                    case 'greater_than':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart" && $subtotal != 0) {
                            if (!($subtotal > $value)) $result = false;
                        } else if ($calculate == "from_filter") {
                            $amount = $this->get_coupon_filter_cart_subtotal($coupon->get_product_ids());
                            if (!($amount > (float) $value)) $result = false;
                        }
                        break;
                    case 'greater_than_or_equal':
                        $subtotal = WC()->cart->cart_contents_total;
                        if ($calculate == "from_cart" && $subtotal != 0) {
                            if (!($subtotal >= $value)) {
                                $result = false;
                            }
                        } else if ($calculate >= "from_filter") {
                            $amount = $this->get_coupon_filter_cart_subtotal($coupon->get_product_ids());
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
                            $count = $this->get_coupon_filter_cart_item_count($coupon->get_product_ids());
                            if (!($count < (float) $value)) {
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
                            $count = $this->get_coupon_filter_cart_item_count($coupon->get_product_ids());
                            if (!($count <= (float) $value)) {
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
                            $count = $this->get_coupon_filter_cart_item_count($coupon->get_product_ids());
                            if (!($count > (float) $value)) {
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
                            $count = $this->get_coupon_filter_cart_item_count($coupon->get_product_ids());
                            if (!($count >= (float) $value)) {
                                $result = false;
                            }
                        }
                        break;
                }
            }
        }
        return $result;
    }

    public function get_coupon_filter_cart_subtotal($product_ids)
    {
        $amount = 0;
        foreach (WC()->cart->get_cart() as $value) {
            if (in_array($value["product_id"], $product_ids)) $amount = $amount + $value["line_subtotal"];
        }
        return $amount;
    }

    public function get_coupon_filter_cart_item_count($product_ids)
    {
        $count = 0;
        foreach (WC()->cart->get_cart() as $value) {
            if (in_array($value["product_id"], $product_ids)) $count += 1;
        }
        return $count;
    }

    static public function check($code)
    {
        $coupon = new \WC_Coupon($code);
        $class = new Validation;
        return $class->woocommerce_coupon_is_valid(true, $coupon);
    }
}
