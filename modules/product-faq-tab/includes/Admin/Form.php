<?php

namespace springdevs\custompft\Admin;

/**
 * Form class
 */
class Form
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'custompft_enqueue_scripts']);
        add_action('add_meta_boxes', [$this, "custompft_metaboxes"]);
        add_action('save_post', [$this, 'custompft_save_meta_post']);
    }

    public function custompft_enqueue_scripts()
    {
        wp_enqueue_script("custompft_js");
        wp_enqueue_style("custompft_css");
        wp_localize_script(
            'custompft_js',
            'custompft_helper_obj',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );
        wp_localize_script(
            'custompft_js',
            'custompft_post',
            array('id' => get_the_ID())
        );
    }

    public function custompft_metaboxes()
    {
        add_meta_box(
            'custompft_faq',
            __('FAQ', 'sdevs_wea'),
            [$this, 'custompft_admin_screen'],
            'product',
            'normal',
            'default'
        );
    }

    function custompft_admin_screen()
    {
        $custompft_nonce = wp_create_nonce('custompft_nonce');
?>
        <div id="custompftapp">
            <productadmin :nonce='<?php echo json_encode($custompft_nonce); ?>'></productadmin>
        </div>
<?php
    }

    public function custompft_save_meta_post($post_id)
    {
        if (!isset($_POST["custompftLength"])) {
            return;
        }

        if (!wp_verify_nonce($_POST["custompft_nonce"], "custompft_nonce")) {
            wp_die(__('Sorry !! You cannot permit to access.', 'sdevs_wea'));
        }

        $custompftLength = (int)$_POST["custompftLength"];
        if (is_int($custompftLength)) {
            $faqs = [];
            if ($custompftLength == 0) {
                $faqs = null;
            } else {
                for ($i = 0; $i < $custompftLength; $i++) {
                    array_push($faqs, [
                        "question" => sanitize_text_field($_POST["custompft_que_" . $i]),
                        "answer" => sanitize_text_field($_POST["custompft_ans_" . $i]),
                        "show" => true,
                    ]);
                }
            }
            update_post_meta($post_id, "custompft_faqs", $faqs);
        }
    }
}
