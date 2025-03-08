<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Dynamic_Consents {
    public function __construct() {
        if (is_admin()) {
            $this->include_admin_files();
            new WC_Dynamic_Consents_Admin();
        } else {
            $this->include_frontend_files();
            new WC_Dynamic_Consents_Frontend();

        }
    }

    private function include_admin_files() {
        require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents-admin.php';
    }

    private function include_frontend_files() {
        require_once plugin_dir_path(__FILE__) . 'class-wc-dynamic-consents-frontend.php';
    }

    private function frontend_hooks() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        new WC_Dynamic_Consents_Frontend();
    }

}