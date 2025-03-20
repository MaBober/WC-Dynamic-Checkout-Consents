<?php
/**
 * WooCommerce Dynamic Checkout Consents - Loader
 *
 * Loads required files and initializes the main plugin class.
 *
 * @package WooCommerceDynamicCheckoutConsents
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Dynamic_Consents_Loader {

    /**
     * Singleton instance
     *
     * @var WC_Dynamic_Consents_Loader
     */
    private static $instance = null;

    /**
     * Get the singleton instance.
     *
     * @return WC_Dynamic_Consents_Loader
     */
    public static function get_instance() {
        
        $request_uri = $_SERVER['REQUEST_URI'];
        if (
            strpos($request_uri, 'favicon.ico') !== false ||
            (defined('DOING_CRON') && DOING_CRON)
        ) {
            return;
        }

        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * WC_Dynamic_Consents_Loader constructor.
     */
    private function __construct() {
        $this->includes();
        $this->init_hooks();
        $this->init_plugin();
    }

    /**
     * Load required files.
     */
    private function includes() {
        require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents.php';
        // require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents-activator.php';
        require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents-helper.php';
        require_once plugin_dir_path(__FILE__) . '../admin/class-wc-dynamic-consents-admin-definer.php';
        require_once plugin_dir_path(__FILE__) . '../admin/class-wc-dynamic-consents-admin-display.php';
    }

    /**
     * Register actions and filters.
     */
    private function init_hooks() {
        register_activation_hook(WC_DYNAMIC_CONSENTS_PLUGIN_FILE, array('WC_Dynamic_Consents_Activator', 'activate'));
        register_deactivation_hook(WC_DYNAMIC_CONSENTS_PLUGIN_FILE, array('WC_Dynamic_Consents_Activator', 'deactivate'));
        register_uninstall_hook(WC_DYNAMIC_CONSENTS_PLUGIN_FILE, array('WC_Dynamic_Consents_Activator', 'uninstall'));
    }

    /**
     * Initialize the main plugin class.
     */
    public function init_plugin() {
        WC_Dynamic_Consents::get_instance();
    }
}