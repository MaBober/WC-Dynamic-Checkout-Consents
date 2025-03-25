<?php

if (!defined('ABSPATH')) {
    exit; 
}

/**
 * Class WC_Dynamic_Consents_Frontend
 *
 * Handles displaying and processing dynamic consents on the WooCommerce checkout page.
 *
 * @package WCDynamicCheckoutConsents
 */
class WC_Dynamic_Consents_Frontend {
    /**
     * WC_Dynamic_Consents_Frontend constructor.
     *
     * Initializes hooks for handling consents in the checkout process.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Registers hooks for checkout-related functionality.
     */
    private function init_hooks() {
        add_action('woocommerce_checkout_fields', [$this, 'add_checkout_consents']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_consents_in_order_meta']);
        add_filter('woocommerce_checkout_required_field_notice', [$this, 'customize_required_field_notice'], 10, 2);
    }
    
    /**
     * Adds dynamic consents to the WooCommerce checkout fields.
     *
     * @param array $fields WooCommerce checkout fields.
     * @return array Modified checkout fields with dynamic consents.
     */
    public function add_checkout_consents($fields) {
        $saved_consents = get_option('dynamic_consents', []);
        
        if (!empty($saved_consents)) {

            foreach ($saved_consents as $index => $consent) {

                if (!WC_Dynamic_Consents_Helper::should_display_consent($consent)) {
                    continue;
                }

                $consent_text = isset($consent['text']) ? wp_kses_post($consent['text']) : '';
                $is_required = isset($consent['required']) && $consent['required'] == '1';
                $is_checked = isset($consent['default_checked']) && $consent['default_checked'] == '1';
    
                $fields['billing']['dynamic_consent_' . $index] = [
                    'type'     => 'checkbox',
                    'label'    => $consent_text,
                    'required' => $is_required,
                    'class'    => ['form-row-wide'],
                    'default'  => $is_checked ? 1 : 0
                ];
            }
        }
        return $fields;
    }

    /**
     * Customizes the error message for required consents on the checkout page.
     *
     * @param string $notice Default WooCommerce required field notice.
     * @param string $field_label The label of the missing required field.
     * @return string Custom error message for required consents.
     */
    public function customize_required_field_notice($notice, $field_label) {

        $saved_consents = get_option('dynamic_consents', []);
        $field_label = substr($field_label, 8); // Remove "billing_" prefix

        foreach ($saved_consents as $index => $consent) {
            if ($consent['text'] === $field_label) {
                return __('You must accept all required consents to place an order.', 'wc-dynamic-consents');
            }
        }

        return $notice;
    }

    /**
     * Saves customer consents in the order metadata.
     *
     * @param int $order_id The WooCommerce order ID.
     */
    public function save_consents_in_order_meta($order_id) {

        $saved_consents = get_option('dynamic_consents', []);

        if (!empty($saved_consents)) {
            $consents = [];
    
            foreach ($saved_consents as $index => $consent) {

                if (!WC_Dynamic_Consents_Helper::should_display_consent($consent)) {
                    continue;
                }

                $consent_text = isset($consent['text']) ? wp_kses_post($consent['text']) : '';
                $is_required = isset($consent['required']) && $consent['required'] == '1';
    
                $consents[] = [
                    'text'     => $consent_text,
                    'required' => $is_required,
                    'accepted' => !empty($_POST['dynamic_consent_' . $index]),
                ];
            }
            update_post_meta($order_id, '_dynamic_consents', $consents);
        }
    }
   
}

