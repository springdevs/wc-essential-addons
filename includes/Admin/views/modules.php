<?php
$modules = get_option("sdevs_wea_modules", []);
$active_modules = get_option("sdevs_wea_activated_modules", []);
?>
<div class="wrap">
    <div class="card" style="max-width: 100%;">
        <h2 class="title"><?php esc_attr_e('All Modules', 'sdevs_wea'); ?></h2>
        <p><?php _e("these extensions can power up your marketing efforts.", "sdevs_wea"); ?></p>
        <div class="sdwac_addon_lists">
            <?php
            foreach ($modules as $key => $value) :
                $module_path = SDEVS_WEA_ASSETS_URL . "/modules/" . $key;
                $filter_module_path = apply_filters('sdevs_wma_module_asset_path', $module_path, $key, $value);
            ?>
                <div class="card sdwac_addon_item">
                    <h3><?php echo $value['name']; ?></h3>
                    <p><?php echo $value['desc']; ?></p>
                    <?php if (array_key_exists($key, $active_modules)) : ?>
                        <a href="admin.php?page=springdevs-modules&modules_deactivate=<?php echo $key; ?>" class="button-secondary"><?php _e('Deactivate', 'sdevs_wea'); ?></a>
                    <?php else : ?>
                        <a href="admin.php?page=springdevs-modules&modules_activate=<?php echo $key; ?>" class="button-primary"><?php _e('Activate', 'sdevs_wea'); ?></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>