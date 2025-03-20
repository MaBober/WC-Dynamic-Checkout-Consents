<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Main class for WooCommerce Dynamic Checkout Consents.
 */
class WC_Dynamic_Consents {

    /**
     * Singleton instance
     *
     * @var WC_Dynamic_Consents
     */
    private static $instance = null;

    /**
     * Get the singleton instance.
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
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        add_action('admin_init', [$this, 'admin_page_init']);
        add_action('woocommerce_checkout_fields', [$this, 'add_checkout_consents']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_consents_in_order_meta']);
        add_filter('woocommerce_checkout_required_field_notice', [$this, 'customize_required_field_notice'], 10, 2);
    }

    /**
     * Initialize admin classes based on the current page.
     */
    public function admin_page_init() {

        new WC_Dynamic_Consents_Definer();
        new WC_Dynamic_Consents_Display();

        if (isset($_GET['page']) && $_GET['page'] === 'wc-orders') {
            
        }
    }

    /**
     * Add dynamic consents to the checkout fields.
     *
     * @param array $fields Checkout fields.
     * @return array Modified checkout fields.
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
     * Customize the required field notice for dynamic consents.
     *
     * @param string $notice The original notice.
     * @param string $field_label The field label.
     * @return string Modified notice.
     */
    public function customize_required_field_notice($notice, $field_label) {
        $saved_consents = get_option('dynamic_consents', []);
        $field_label = substr($field_label, 8);

        foreach ($saved_consents as $index => $consent) {
            if ($consent['text'] === $field_label) {
                return __('You must accept all required consents to place an order.', 'wc-dynamic-consents');
            }
        }

        return $notice;
    }

    /**
     * Save consents in the order meta.
     *
     * @param int $order_id The order ID.
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