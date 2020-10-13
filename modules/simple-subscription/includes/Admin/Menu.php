<?php

namespace SpringDevs\WcSubscription\Admin;

/**
 * Menu class
 * @package SpringDevs\WcSubscription\Admin
 */
class Menu
{
    public function __construct()
    {
        add_action("admin_menu", [$this, "create_admin_menu"]);
    }

    public function create_admin_menu()
    {
        $parent_slug = "edit.php?post_type=subscrpt_order";
        add_menu_page("Subscriptions", "Subscriptions", "manage_options", $parent_slug, false, "dashicons-image-rotate", 40);
    }
}
