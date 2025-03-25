<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WC_Dynamic_Consents_Admin_Order
 *
 * Displays consent data in the WooCommerce order details within the admin panel.
 *
 * @package WCDynamicCheckoutConsents
 */
class WC_Dynamic_Consents_Admin_Order {

    /**
     * WC_Dynamic_Consents_Admin_Order constructor.
     *
     * Initializes hooks related to displaying consent data in order details.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Registers WooCommerce admin hooks.
     */
    public function init_hooks() {
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_order_consents']);
    }
    
    /**
    * Displays consents in the WooCommerce admin order details.
    *
    * Retrieves stored consents from order meta and displays them in a structured format.
    *
    * @param WC_Order $order The WooCommerce order object.
    */
    public function display_order_consents($order) {
        $consents = get_post_meta($order->get_id(), '_dynamic_consents', true);
    
        if (!empty($consents)) {
            echo '<p><strong>' . esc_html__('Consents Given:', 'wc-dynamic-consents') . '</strong></p>';
            echo '<ul>';
            foreach ($consents as $consent) {
                // Usuwamy znaczniki HTML, aby nie wpływały na wyświetlanie w panelu
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


// 
