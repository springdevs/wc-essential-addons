<?php

namespace SpringDevs\WcEssentialAddons\Admin;

/**
 * Admin Pages Handler
 *
 * Class Menu
 */
class Menu
{
    /**
     * Menu constructor.
     */
    public function __construct()
    {
        add_action('admin_init', [$this, "handle_requests"]);
        add_action('admin_menu', [$this, 'admin_menu']);
    }

    public function handle_requests()
    {
        if (isset($_GET["page"])) {
            if ($_GET["page"] == "springdevs-modules") {
                if (isset($_GET["modules_activate"])) {
                    $this->active_modules($_GET["modules_activate"]);
                } elseif (isset($_GET["modules_deactivate"])) {
                    $this->deactive_modules($_GET["modules_deactivate"]);
                }
            }
        }
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu()
    {
        $parent_slug = 'springdevs-modules';
        $capability = 'manage_options';
        $hook = add_menu_page(__('Essential Addons', 'sdevs_wea'), __('Essential Addons', 'sdevs_wea'), $capability, $parent_slug, [$this, 'plugin_page'], 'dashicons-image-filter', 40);
        add_submenu_page($parent_slug, "", __("All Modules", "sdevs_wea"), $capability, $parent_slug, [$this, 'plugin_page']);
        add_action('load-' . $hook, [$this, 'init_hooks']);
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts()
    {
        // wp_enqueue_style( 'admin' );
        // wp_enqueue_script( 'admin' );
    }

    /**
     * Handles the main page
     *
     * @return void
     */
    public function plugin_page()
    {
        $modules = get_option("sdevs_wea_modules");
        $active_modules = get_option("sdevs_wea_activated_modules");
?>
        <div class="wrap">
            <div class="card" style="max-width: 100%;">
                <h2 class="title"><?php esc_attr_e('All Modules', 'sdevs_wea'); ?></h2>
                <p><?php _e("these extensions can power up your marketing efforts.", "sdevs_wea"); ?></p>
                <div class="sdwac_addon_lists">
                    <?php
                    foreach ($modules as $key => $value) :
                    ?>
                        <div class="card sdwac_addon_item">
                            <img src="<?php echo SDEVS_WEA_ASSETS_URL . "/modules/" . $key . "/icon.png"; ?>" alt="">
                            <h3><?php echo $value[1]; ?></h3>
                            <p><?php _e($value[2], "sdevs_wea") ?></p>
                            <?php if (array_key_exists($key, $active_modules)) : ?>
                                <a href="admin.php?page=springdevs-modules&modules_deactivate=<?php echo $key; ?>" class="button-secondary">Deactivate</a>
                            <?php else : ?>
                                <a href="admin.php?page=springdevs-modules&modules_activate=<?php echo $key; ?>" class="button-primary">Activate</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php
    }

    public function active_modules($req_addon)
    {
        $modules = get_option("sdevs_wea_modules");
        $active_modules = get_option("sdevs_wea_activated_modules");
        foreach ($modules as $key => $value) {
            if ($key == $req_addon) {
                require_once SDEVS_WEA_ASSETS_PATH . "/modules/" . $key . "/requirements.php";
                $active_modules[$key] = $value;
            }
        }
        update_option("sdevs_wea_activated_modules", $active_modules);
        $this->reload();
    }

    public function deactive_modules($req_addon)
    {
        $modules = get_option("sdevs_wea_modules");
        $active_modules = get_option("sdevs_wea_activated_modules");
        foreach ($modules as $key => $value) {
            if ($key == $req_addon) {
                $uninstall_file = SDEVS_WEA_ASSETS_PATH . "/modules/" . $key . "/uninstall.php";
                if (file_exists($uninstall_file)) require_once $uninstall_file;
                unset($active_modules[$key]);
            }
        }
        update_option("sdevs_wea_activated_modules", $active_modules);
        $this->reload();
    }

    public function reload()
    {
    ?>
        <script>
            window.location.href = "admin.php?page=springdevs-modules";
        </script>
<?php
    }
}
