<?php

namespace springdevs\WooAdvanceCoupon;

use springdevs\WooAdvanceCoupon\Frontend\Auto;
use springdevs\WooAdvanceCoupon\Frontend\Coupon;
use springdevs\WooAdvanceCoupon\Frontend\Url;
use springdevs\WooAdvanceCoupon\Frontend\Validation;
use springdevs\WooAdvanceCoupon\Illuminate\Coupon as IlluminateCoupon;

/**
 * Frontend handler class
 */
class Frontend
{
    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        $exists = $this->is_rest();
        if ($exists) return;
        new IlluminateCoupon;
        new Validation;
        new Coupon;
        new Auto;
        new Url;
    }

    public function is_rest()
    {
        $prefix = rest_get_url_prefix();
        if (
            defined('REST_REQUEST') && REST_REQUEST // (#1)
            || isset($_GET['rest_route']) // (#2)
            && strpos(trim($_GET['rest_route'], '\\/'), $prefix, 0) === 0
        )
            return true;
        // (#3)
        global $wp_rewrite;
        if ($wp_rewrite === null) $wp_rewrite = new \WP_Rewrite();

        // (#4)
        $rest_url = wp_parse_url(trailingslashit(rest_url()));
        $current_url = wp_parse_url(add_query_arg(array()));
        return strpos($current_url['path'], $rest_url['path'], 0) === 0;
    }
}
