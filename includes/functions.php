<?php

function sdevs_has_pro_version()
{
    if (class_exists('Sdevs_wma_Main_Pro')) return true;
    return false;
}

function sdevs_is_pro_module_activate($module)
{
    $active_modules = get_option("sdevs_wea_activated_modules", []);
    return array_key_exists($module, $active_modules);
}
