<?php

/**
 * WooCommerce Dynamic Checkout Consents - Loader
 *
 * Handles loading required files and initializing the main plugin class.
 *
 * @package WCDynamicCheckoutConsents
 */

if (!defined('ABSPATH')) {
    exit; // Zabezpieczenie przed bezpośrednim dostępem
}

/**
 * Class WC_Dynamic_Consents_Loader
 *
 * Ensures the plugin loads required dependencies and initializes correctly.
 */
class WC_Dynamic_Consents_Loader {

    /**
     * Singleton instance
     *
     * @var WC_Dynamic_Consents_Loader|null
     */
    private static $instance = null;

    /**
     * Returns the singleton instance of the loader.
     *
     * Prevents unnecessary initialization in cases like favicon requests or cron jobs.
     *
     * @return WC_Dynamic_Consents_Loader|null
     */
    public static function get_instance() {
        
        $request_uri = $_SERVER['REQUEST_URI'];
        if (
            strpos($request_uri, 'favicon.ico') !== false ||
            (defined('DOING_CRON') && DOING_CRON)
        ) {
            return null;
        }

        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * WC_Dynamic_Consents_Loader constructor.
     *
     * Initializes the plugin by including necessary files and setting up hooks.
     */
    private function __construct() {
        $this->includes();

        $this->init_plugin();
    }

    /**
     * Includes required files for the plugin.
     */
    private function includes() {
        require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents.php';
        require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents-helper.php';
        require_once plugin_dir_path(__FILE__) . '../admin/class-wc-dynamic-consents-order.php';
        require_once plugin_dir_path(__FILE__) . '../admin/class-wc-dynamic-consents-settings.php';
        require_once plugin_dir_path(__FILE__) . '../frontend/class-wc-dynamic-consents-frontend.php';
    }


    /**
     * Initializes the main plugin class.
     */
    public function init_plugin() {
        WC_Dynamic_Consents::get_instance();
    }
}

