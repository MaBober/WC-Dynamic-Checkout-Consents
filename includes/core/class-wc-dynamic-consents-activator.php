<?php

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
        // Create default options in the database
        if (get_option('dynamic_consents') === false) {
            update_option('dynamic_consents', []);
        }
    }

    /**
     * Deactivate the plugin.
     */
    public static function deactivate() {
        // No specific actions needed on deactivation
    }
} 