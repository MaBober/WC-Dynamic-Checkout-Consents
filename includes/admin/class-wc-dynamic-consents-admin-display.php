<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WC_Dynamic_Consents_Display
 *
 * Handles the display of dynamic consents in the WooCommerce admin order details.
 */
class WC_Dynamic_Consents_Display {

    /**
     * WC_Dynamic_Consents_Display constructor.
     */
    public function __construct() {
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_order_consents']);
    }

    /**
     * Display the consents given by the customer in the order details.
     *
     * @param WC_Order $order The order object.
     */
    public function display_order_consents($order) {
        $consents = get_post_meta($order->get_id(), '_dynamic_consents', true);
    
        if (!empty($consents)) {
            echo '<p><strong>' . esc_html__('Consents Given:', 'wc-dynamic-consents') . '</strong></p>';
            echo '<ul>';
            foreach ($consents as $consent) {
                $consent_text = wp_strip_all_tags($consent['text']);
                $consent_status = !empty($consent['accepted']) 
                    ? _x('Accepted ✅', 'Consent status', 'wc-dynamic-consents') 
                    : _x('Not Accepted ❌', 'Consent status', 'wc-dynamic-consents');
    
                echo '<li><strong>' . esc_html($consent_text) . ':<br></strong> ' . esc_html($consent_status) . '</li>';
            }
            echo '</ul>';
        }
    }
}