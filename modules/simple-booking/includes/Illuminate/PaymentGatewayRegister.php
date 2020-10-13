<?php

/**
 * Class PaymentGatewayRegister || Add custom Payment Gateway
 */

class PaymentGatewayRegister extends WC_Payment_Gateway
{
    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
        $this->id                = 'wc-booking-gateway';
        $this->icon              = '';
        $this->has_fields        = false;
        $this->method_title      = __('Check booking availability', 'sdevs_wea');
        $this->title             = $this->method_title;
        $this->order_button_text = __('Request Confirmation', 'sdevs_wea');

        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
    }

    /**
     * Admin page.
     */
    public function admin_options()
    {
        $title = (!empty($this->method_title)) ? $this->method_title : __('Settings', 'sdevs_wea');

        echo '<h3>' . esc_html($title) . '</h3>';

        echo '<p>' . esc_html__('This is fictitious payment method used for bookings that requires confirmation.', 'sdevs_wea') . '</p>';
        echo '<p>' . esc_html__('This gateway requires no configuration.', 'sdevs_wea') . '</p>';

        // Hides the save button
        echo '<style>p.submit input[type="submit"] { display: none }</style>';
    }

    /**
     * Process the payment and return the result
     *
     * @param  int $order_id
     *
     * @return array
     */
    public function process_payment($order_id)
    {
        $order = new WC_Order($order_id);

        // Add custom order note.
        $order->add_order_note(__('This order is awaiting confirmation from the shop manager', 'sdevs_wea'));

        // Remove cart
        WC()->cart->empty_cart();

        // Return thankyou redirect.
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page($order_id)
    {
        $order = new WC_Order($order_id);

        if ('completed' == $order->get_status()) {
            echo '<p>' . esc_html__('Your booking has been confirmed. Thank you.', 'sdevs_wea') . '</p>';
        } else {
            echo '<p>' . esc_html__('Your booking is awaiting confirmation. You will be notified by email as soon as we\'ve confirmed availability.', 'sdevs_wea') . '</p>';
        }
    }
}
