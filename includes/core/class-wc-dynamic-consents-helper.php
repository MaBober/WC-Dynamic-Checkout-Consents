<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class WC_Dynamic_Consents_Helper
 *
 * Provides helper functions for the WooCommerce Dynamic Checkout Consents plugin.
 */
class WC_Dynamic_Consents_Helper {

    /**
     * Get product and category IDs from the cart.
     *
     * This function retrieves the product and category IDs of items in the cart.
     *
     * @return array An associative array with 'product_ids' and 'category_ids'.
     */
    public static function get_cart_product_and_category_ids() {
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

    /**
     * Determine if a consent should be displayed based on conditions.
     *
     * This function checks if a consent should be displayed based on the conditions
     * specified in the consent settings. Conditions can include specific products
     * or categories.
     *
     * @param array $consent The consent data.
     * @return bool True if the consent should be displayed, false otherwise.
     */
    public static function should_display_consent($consent) {
        if (empty($consent['conditions'])) {
            return true; 
        }

        $cart_data = self::get_cart_product_and_category_ids();
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
}