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

function sdevs_get_module_name_by_key($key)
{
    $modules = get_option("sdevs_wea_modules");
    foreach ($modules as $mkey => $value) {
        if ($mkey === $key) {
            return $value['name'];
        }
    }
}
