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
