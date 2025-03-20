<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Dynamic_Consents {

    private static $instance = null;

    public static function get_instance() {

        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {

        $this->init_hooks();

        if (is_admin()) {

            new WC_Dynamic_Consents_Admin();
        }
    }

    private function init_hooks() {
        add_action('woocommerce_checkout_fields', [$this, 'add_checkout_consents']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_consents_in_order_meta']);
        add_filter('woocommerce_checkout_required_field_notice', [$this, 'customize_required_field_notice'], 10, 2);
    }

    public function add_checkout_consents($fields) {
        $saved_consents = get_option('dynamic_consents', []);
        
        if (!empty($saved_consents)) {

            foreach ($saved_consents as $index => $consent) {

                if (!$this->should_display_consent($consent)) {
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

    private function get_cart_product_and_category_ids() {

        $cart_items = WC()->cart->get_cart();
        $cart_product_ids = [];
        $cart_category_ids = [];
    

        foreach ($cart_items as $cart_item) {
            $cart_product_ids[] = $cart_item['product_id'];
            $categories = get_the_terms($cart_item['product_id'], 'product_cat');
            if ($categories && !is_wp_error($categories)) {
                foreach ($categories as $category) {
                    $cart_category_ids[] = $category->term_id;
                }
            }
        }
    
        return [
            'product_ids'  => $cart_product_ids,
            'category_ids' => $cart_category_ids
        ];
    }

    private function should_display_consent($consent) {
        if (empty($consent['conditions'])) {
            return true; 
        }

        $cart_data = $this->get_cart_product_and_category_ids();
        $cart_product_ids = $cart_data['product_ids'];
        $cart_category_ids = $cart_data['category_ids'];
    
        foreach ($consent['conditions'] as $condition) {
            if (!isset($condition['type'], $condition['id'])) {
                continue;
            }
    
            if ($condition['id'] === 'all') {
                return true;
            }
    
            if ($condition['type'] === 'category') {
                $category_id = (int) str_replace('cat-', '', $condition['id']);
                if (in_array($category_id, $cart_category_ids)) {
                    return true; 
                }
            }
    
            if ($condition['type'] === 'product') {
                $product_id = (int) str_replace('prod-', '', $condition['id']);
                if (in_array($product_id, $cart_product_ids)) {
                    return true; 
                }
            }
        }
    
        return false; 
    }


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

    public function save_consents_in_order_meta($order_id) {

        $saved_consents = get_option('dynamic_consents', []);

        if (!empty($saved_consents)) {
            $consents = [];
    
            foreach ($saved_consents as $index => $consent) {

                if (!$this->should_display_consent($consent)) {
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

