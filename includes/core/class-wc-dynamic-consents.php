<?php

if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class WC_Dynamic_Consents
 *
 * Main class for handling WooCommerce Dynamic Checkout Consents functionality.
 *
 * @package WCDynamicCheckoutConsents
 */
class WC_Dynamic_Consents {

    /**
     * Singleton instance.
     *
     * @var WC_Dynamic_Consents|null
     */
    private static $instance = null;

    /**
     * Gets the singleton instance.
     *
     * Ensures that only one instance of the class exists.
     *
     * @return WC_Dynamic_Consents
     */
    public static function get_instance() {

        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * WC_Dynamic_Consents constructor.
     *
     * Initializes the plugin, setting up admin and frontend hooks.
     */
    private function __construct() {

        if (is_admin()) {
            $this->init_admin_hooks();
        }
        
        // Initialize hooks for frontend after WordPress is fully loaded
        add_action('wp', [$this, 'check_if_checkout']);
    }
    
    /**
     * Checks if the current page is the checkout page and initializes frontend hooks.
     */
    public function check_if_checkout() {
        if (is_checkout()) {
            new WC_Dynamic_Consents_Frontend();
        }
    }
    
    /**
     * Initializes admin hooks based on the current admin page.
     */
    private function init_admin_hooks() {
        if (isset($_GET['page'])) {
            if ($_GET['page'] === 'wc-settings') {
                new WC_Dynamic_Consents_Admin_Settings();
            } elseif ($_GET['page'] === 'wc-orders') {
                new WC_Dynamic_Consents_Admin_Order();
            }
        }
    }
}

