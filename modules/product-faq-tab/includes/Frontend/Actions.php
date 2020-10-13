<?php

namespace springdevs\custompft\Frontend;

/**
 * Actions handler class
 */
class Actions
{
    public function __construct()
    {
        add_filter("custompft_faq", [$this, "custompft_faq"]);
    }

    public function custompft_faq($post_id)
    {
        $faqs = get_post_meta($post_id, "custompft_faqs", true);
        return $faqs;
    }
}
