<?php

function subscrpt_get_typos($number, $typo)
{
    if ($number == 1 && $typo == "days") {
        return __("day", "sdevs_wea");
    } elseif ($number == 1 && $typo == "weeks") {
        return __("week", "sdevs_wea");
    } elseif ($number == 1 && $typo == "months") {
        return __("month", "sdevs_wea");
    } elseif ($number == 1 && $typo == "years") {
        return __("year", "sdevs_wea");
    } else {
        return $typo;
    }
}

function subscrpt_check_trial($product_id)
{
    $result = true;
    $author = get_current_user_id();
    $product = wc_get_product($product_id);
    $cancelled_items = get_user_meta($author, '_subscrpt_cancelled_items', true);
    $expired_items = get_user_meta($author, '_subscrpt_expired_items', true);
    $active_items = get_user_meta($author, '_subscrpt_active_items', true);
    $pending_items = get_user_meta($author, '_subscrpt_pending_items', true);

    if (!is_array($cancelled_items)) $cancelled_items = [];
    if (!is_array($expired_items)) $expired_items = [];
    if (!is_array($active_items)) $active_items = [];
    if (!is_array($pending_items)) $pending_items = [];

    foreach ($cancelled_items as $cancelled_item) {
        if ($cancelled_item['product'] == $product_id) $result = false;
    }

    foreach ($expired_items as $expired_item) {
        if ($expired_item['product'] == $product_id) $result = false;
    }

    foreach ($active_items as $active_item) {
        if ($active_item['product'] == $product_id) $result = false;
    }

    foreach ($pending_items as $pending_item) {
        if ($pending_item['product'] == $product_id) $result = false;
    }

    if ($product->is_type("simple")) return $result;
    return apply_filters('subscrpt_filter_product_trial', $result, $product_id, $active_items, $pending_items, $cancelled_items, $expired_items);
}

function subscrpt_next_date($time, $trial = null)
{
    if ($trial == null) {
        $start_date = time();
    } else {
        $start_date = strtotime($trial);
    }
    return date('F d, Y', strtotime($time, $start_date));
}

function subscrpt_check_unexpired($product_id)
{
    $author = get_current_user_id();
    $product = wc_get_product($product_id);
    $active_items = get_user_meta($author, '_subscrpt_active_items', true);
    $pending_items = get_user_meta($author, '_subscrpt_pending_items', true);

    if (!is_array($active_items)) $active_items = [];
    if (!is_array($pending_items)) $pending_items = [];

    foreach ($active_items as $active_item) {
        if ($active_item['product'] == $product_id) return true;
    }

    foreach ($pending_items as $pending_item) {
        if ($pending_item['product'] == $product_id) return true;
    }

    if ($product->is_type('simple')) return false;
    return apply_filters('subscrpt_filter_check_unexpired', false, $product_id, $active_items, $pending_items);
}
