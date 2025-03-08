<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Dynamic_Consents_Admin {
    public function __construct() {
        add_action('admin_init', array($this, 'admin_page_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function admin_page_init() {
        add_filter('woocommerce_get_settings_checkout', array($this, 'add_dynamic_consent_settings_to_checkout'));
    }

    public function enqueue_admin_assets() {
        wp_enqueue_style('wc-dynamic-consents-admin', plugin_dir_url(__FILE__) . '../css/wc-dynamic-consents-admin.css', array(), '1.0', 'all');
        wp_enqueue_script('wc-dynamic-consents-admin', plugin_dir_url(__FILE__) . '../js/wc-dynamic-consents-admin.js', array('jquery'), '1.0', true);
        wp_localize_script(
            'wc-dynamic-consents-admin',
            'wcDynamicConsentsData',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('dynamic-consents-nonce'),
            )
        );
    }
    

    /**
     * Adds dynamic consent settings to the WooCommerce checkout settings.
     *
     * This function appends custom settings for dynamic consents to the existing
     * WooCommerce checkout settings. The custom settings include a title, a consents
     * table, and a section end.
     *
     * @param array $settings The existing WooCommerce checkout settings.
     * @return array The modified settings with the added dynamic consent options.
     */
    public function add_dynamic_consent_settings_to_checkout($settings) {
        $custom_settings = array(
            array(
                'title' => __('Dodatkowe zgody', 'woocommerce'),
                'type' => 'title',
                'id' => 'dynamic_consents_options',
            ),
            array(
                'type' => 'consents_table',
                'id' => 'dynamic_consents',
                'desc' => __('Define the consents you want customers to accept. Use the "Add Consent" button to add more.', 'woocommerce'),
            ),
            array(
                'type' => 'sectionend',
                'id' => 'dynamic_consents_options',
            ),
        );
    
        // Merge custom settings with existing WooCommerce checkout settings
        return array_merge($settings, $custom_settings);
    }
}


// 
