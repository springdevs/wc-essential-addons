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
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('sdmaw_custom');
        });
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
        $hook = add_menu_page(__('Missing Addons', 'sdevs_wea'), __('Missing Addons', 'sdevs_wea'), $capability, $parent_slug, [$this, 'plugin_page'], 'dashicons-image-filter', 40);
        add_submenu_page($parent_slug, "", __("All Modules", "sdevs_wea"), $capability, $parent_slug, [$this, 'plugin_page']);
        if (!function_exists('sdevs_freemius_setup') || !sdevs_freemius_setup()->can_use_premium_code()) {
            $pricing = add_submenu_page($parent_slug, __("Active Pro", "sdevs_wea"), __("Active Pro", "sdevs_wea"), $capability, "springdevs-freemius", [$this, 'pricing_page']);
            add_action('load-' . $pricing, [$this, 'init_hooks']);
        }
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
        wp_enqueue_script('freemius_checkout');
        wp_enqueue_script('freemius_custom');
    }

    /**
     * Handles the addons page
     *
     * @return void
     */
    public function plugin_page()
    {
        include 'views/modules.php';
    }

    /**
     * Handles the pricing page
     *
     * @return void
     */
    public function pricing_page()
    {
        include 'views/pricing.php';
    }

    public function active_modules($req_addon)
    {
        $modules = get_option("sdevs_wea_modules");
        $active_modules = get_option("sdevs_wea_activated_modules");
        foreach ($modules as $key => $value) {
            if ($key == $req_addon) {
                $module_path = SDEVS_WEA_ASSETS_PATH . "/modules/" . $key;
                $filter_module_path = apply_filters('sdevs_wma_module_path', $module_path, $key, $value);
                if (file_exists($filter_module_path)) {
                    require_once $filter_module_path . "/requirements.php";
                    $active_modules[$key] = $value;
                }
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
                $module_path = SDEVS_WEA_ASSETS_PATH . "/modules/" . $key;
                $filter_module_path = apply_filters('sdevs_wma_module_path', $module_path, $key, $value);
                $uninstall_file = $filter_module_path . "/uninstall.php";
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
