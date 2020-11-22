<?php

namespace springdevs\custompft\Frontend;

/**
 * Woocommerce Tab
 */
class Tabs
{
    public function __construct()
    {
        add_filter('woocommerce_product_tabs', [$this, "custompft_add_tabs"], 98);
    }

    public function custompft_add_tabs($tabs)
    {
        $faqs = apply_filters("custompft_faq", get_the_ID());
        if (is_array($faqs)) :
            $tabs['custompft_faqs'] = array(
                'title'     => __('Faq\'s', 'sdevs_wea'),
                'priority'     => 50,
                'callback'     => [$this, 'custompft_tab_content']
            );
        endif;
        return $tabs;
    }

    public function custompft_tab_content()
    {
        $faqs = apply_filters("custompft_faq", get_the_ID());
        if (is_array($faqs)) :
?>
            <ul class="custompft_faq_views">
                <?php foreach ($faqs as $faq) : ?>
                    <li><strong><?php echo $faq["question"]; ?></strong></li>
                    <p><?php echo $faq["answer"]; ?></p>
                <?php endforeach; ?>
            </ul>
<?php
        endif;
    }
}
