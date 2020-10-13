<?php

// Create Option
if (!get_option("gmap_api_key")) {
    add_option("gmap_api_key", null);
}
