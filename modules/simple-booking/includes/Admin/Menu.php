<?php

namespace SpringDevs\WcBooking\Admin;

/**
 * Class Menu || admin menu here
 * @package SpringDevs\WcBooking\Admin
 */
class Menu
{
    public function __construct()
    {
        add_action("admin_menu", [$this, "create_admin_menu"]);
    }

    public function create_admin_menu()
    {
        $parent_slug = "edit.php?post_type=bookable_order";
        add_menu_page("Bookings", "Bookings", "manage_options", $parent_slug, false, "dashicons-calendar-alt", 50);
    }
}
