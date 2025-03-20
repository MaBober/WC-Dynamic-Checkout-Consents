<!-- <?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WC_Dynamic_Consents_Activator
 *
 * Handles plugin activation and deactivation.
 */
class WC_Dynamic_Consents_Activator {

    /**
     * Activate the plugin.
     */
    public static function activate() {
        error_log('Activating the plugin');
        // Create default options in the database
        if (get_option('dynamic_consents') === false) {
            update_option('dynamic_consents', []);
        }
    }

    /**
     * Deactivate the plugin.
     */
    public static function deactivate() {
        error_log('Deactivating the plugin');
        // No specific actions needed on deactivation
    }

    /**
     * Uninstall the plugin.
     */
    public static function uninstall() {
        error_log('Uninstalling the plugin');
        // Remove options from the database
        delete_option('dynamic_consents');
    }
} -->