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
    <?php
    }

    /**
     * Handles the pricing page
     *
     * @return void
     */
    public function pricing_page()
    {
    ?>
        <div class="wrap">
            <section>
                <center>
                    <h1 style="font-size: 2.5em;font-family: 'Open Sans', sans-serif;">Premium Plans</h1>
                    <p style="color: black;">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab doloribus iste sint dignissimos velit quaerat! Repellat omnis ut dolorem consequatur, quod molestias dolore hic deserunt dicta quibusdam optio ea sequi?</p>
                </center>
                <div class="sdwma-pricing sdwma-pricing-palden">
                    <div class="sdwma-pricing-item sdwma-features-item sdwma-ja-animate" data-animation="move-from-bottom" data-delay="item-0" style="min-height: 497px;">
                        <div class="sdwma-pricing-deco">
                            <svg class="sdwma-pricing-deco-img" enable-background="new 0 0 300 100" height="100px" id="Layer_1" preserveAspectRatio="none" version="1.1" viewBox="0 0 300 100" width="300px" x="0px" xml:space="preserve" y="0px">
                                <path class="deco-layer deco-layer--1" d="M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z" fill="#FFFFFF" opacity="0.6"></path>
                                <path class="deco-layer deco-layer--2" d="M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z" fill="#FFFFFF" opacity="0.6"></path>
                                <path class="deco-layer deco-layer--3" d="M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716H42.401L43.415,98.342z" fill="#FFFFFF" opacity="0.7"></path>
                                <path class="deco-layer deco-layer--4" d="M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z" fill="#FFFFFF"></path>
                            </svg>
                            <div class="sdwma-pricing-price"><span class="sdwma-pricing-currency">$</span>29
                                <span class="sdwma-pricing-period">/ year</span>
                            </div>
                            <h3 class="sdwma-pricing-title">Single</h3>
                        </div>
                        <ul class="sdwma-pricing-feature-list">
                            <li class="sdwma-pricing-feature">Single site license only</li>
                            <li class="sdwma-pricing-feature">Subscription Pro Module</li>
                            <li class="sdwma-pricing-feature">Booking Pro Module</li>
                        </ul>
                        <button id="plan-1" class="sdwma-pricing-action">Buy Now</button>
                    </div>
                    <div class="sdwma-pricing-item sdwma-features-item sdwma-ja-animate sdwma-pricing__item--featured" data-animation="move-from-bottom" data-delay="item-1" style="min-height: 497px;">
                        <div class="sdwma-pricing-deco" style="background: linear-gradient(135deg,#a93bfe,#584efd)">
                            <svg class="sdwma-pricing-deco-img" enable-background="new 0 0 300 100" height="100px" id="Layer_1" preserveAspectRatio="none" version="1.1" viewBox="0 0 300 100" width="300px" x="0px" xml:space="preserve" y="0px">
                                <path class="deco-layer deco-layer--1" d="M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z" fill="#FFFFFF" opacity="0.6"></path>
                                <path class="deco-layer deco-layer--2" d="M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z" fill="#FFFFFF" opacity="0.6"></path>
                                <path class="deco-layer deco-layer--3" d="M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716H42.401L43.415,98.342z" fill="#FFFFFF" opacity="0.7"></path>
                                <path class="deco-layer deco-layer--4" d="M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z" fill="#FFFFFF"></path>
                            </svg>
                            <div class="sdwma-pricing-price"><span class="sdwma-pricing-currency">$</span>59
                                <span class="sdwma-pricing-period">/ year</span>
                            </div>
                            <h3 class="sdwma-pricing-title">Business</h3>
                        </div>
                        <ul class="sdwma-pricing-feature-list">
                            <li class="sdwma-pricing-feature">3-sites license only</li>
                            <li class="sdwma-pricing-feature">Subscription Pro Module</li>
                            <li class="sdwma-pricing-feature">Booking Pro Module</li>
                        </ul>
                        <button id="plan-2" class="sdwma-pricing-action feature">Buy Now</button>
                    </div>
                    <div class="sdwma-pricing-item sdwma-features-item sdwma-ja-animate" data-animation="move-from-bottom" data-delay="item-2" style="min-height: 497px;">
                        <div class="sdwma-pricing-deco">
                            <svg class="sdwma-pricing-deco-img" enable-background="new 0 0 300 100" height="100px" id="Layer_1" preserveAspectRatio="none" version="1.1" viewBox="0 0 300 100" width="300px" x="0px" xml:space="preserve" y="0px">
                                <path class="deco-layer deco-layer--1" d="M30.913,43.944c0,0,42.911-34.464,87.51-14.191c77.31,35.14,113.304-1.952,146.638-4.729c48.654-4.056,69.94,16.218,69.94,16.218v54.396H30.913V43.944z" fill="#FFFFFF" opacity="0.6"></path>
                                <path class="deco-layer deco-layer--2" d="M-35.667,44.628c0,0,42.91-34.463,87.51-14.191c77.31,35.141,113.304-1.952,146.639-4.729c48.653-4.055,69.939,16.218,69.939,16.218v54.396H-35.667V44.628z" fill="#FFFFFF" opacity="0.6"></path>
                                <path class="deco-layer deco-layer--3" d="M43.415,98.342c0,0,48.283-68.927,109.133-68.927c65.886,0,97.983,67.914,97.983,67.914v3.716H42.401L43.415,98.342z" fill="#FFFFFF" opacity="0.7"></path>
                                <path class="deco-layer deco-layer--4" d="M-34.667,62.998c0,0,56-45.667,120.316-27.839C167.484,57.842,197,41.332,232.286,30.428c53.07-16.399,104.047,36.903,104.047,36.903l1.333,36.667l-372-2.954L-34.667,62.998z" fill="#FFFFFF"></path>
                            </svg>
                            <div class="sdwma-pricing-price"><span class="sdwma-pricing-currency">$</span>99
                                <span class="sdwma-pricing-period">/ year</span>
                            </div>
                            <h3 class="sdwma-pricing-title">Enterprise</h3>
                        </div>
                        <ul class="sdwma-pricing-feature-list">
                            <li class="sdwma-pricing-feature">Unlimited license</li>
                            <li class="sdwma-pricing-feature">Subscription Pro Module</li>
                            <li class="sdwma-pricing-feature">Booking Pro Module</li>
                        </ul>
                        <button id="plan-3" class="sdwma-pricing-action">Buy Now</button>
                    </div>
                </div>
            </section>
        </div>
    <?php
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
