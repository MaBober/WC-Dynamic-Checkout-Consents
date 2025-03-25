<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; // Zabezpieczenie przed nieautoryzowanym dostępem
}

/**
 * Plugin Uninstall Script
 *
 * This script is executed when the plugin is deleted from the WordPress.
 * It removes all stored options and cleans up the database.
 */

// Usuwanie opcji z bazy danych
delete_option('dynamic_consents');