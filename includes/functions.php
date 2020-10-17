<?php

function sdevs_has_pro_version()
{
    if (in_array('wc-missing-addons-pro/wc-missing-addons-pro.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        return true;
    }
    return false;
}


function sdevs_is_pro_module_activate($module)
{
    $active_modules = get_option("sdevs_wea_activated_modules", []);
    return array_key_exists($module, $active_modules);
}
