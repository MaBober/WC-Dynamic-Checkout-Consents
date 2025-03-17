<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Dynamic_Consents_Admin {
    public function __construct() {
        $this->init_hooks();
        // add_action('admin_init', array($this, 'admin_page_init'));
        
    }

    public function init_hooks() {

        add_filter('woocommerce_get_settings_account', [$this, 'add_dynamic_consent_settings_to_checkout']);
        add_action('woocommerce_admin_field_consents_table', [$this, 'render_dynamic_consents_table']);
        add_filter('woocommerce_admin_settings_sanitize_option', [$this, 'save_dynamic_consents_settings'], 10, 3);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_order_consents']);
    }

    public function enqueue_admin_assets($hook) {

        if ($hook !== 'woocommerce_page_wc-settings') {
            return;
        }
    
        wp_enqueue_style(
            'wc-dynamic-consents-admin-css',
            plugin_dir_url(__FILE__) . '../../assets/css/wc-dynamic-consents-admin.css',
            [],
            '1.0.0'
        );
    
        wp_enqueue_script(
            'wc-dynamic-consents-admin-js',
            plugin_dir_url(__FILE__) . '../../assets/js/wc-dynamic-consents-admin.js',
            ['jquery'],
            '1.0.0',
            true
        );
        // Pobranie listy kategorii i produktów
        $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
        $products = wc_get_products(['limit' => -1]);

        // Przygotowanie danych dla JavaScript
        $localized_data = [
            'categories' => [],
            'products'   => [],
        ];

        foreach ($categories as $category) {
            $localized_data['categories'][] = [
                'id'   => 'cat-' . $category->term_id,
                'name' => $category->name,
            ];
        }

        foreach ($products as $product) {
            $localized_data['products'][] = [
                'id'   => 'prod-' . $product->get_id(),
                'name' => $product->get_name(),
            ];
        }

        // Przekazanie danych do JS
        wp_localize_script('wc-dynamic-consents-admin-js', 'wcDynamicConsentsData', $localized_data);

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
                'title' => __('Additional consents', 'woocommerce'),
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
    
        return array_merge($settings, $custom_settings);
    }

    public function render_dynamic_consents_table($value) {

        $saved_consents = get_option($value['id'], []);
        if (!is_array($saved_consents)) {
            $saved_consents = [];
        }
    
        // Pobieramy listę kategorii i produktów WooCommerce
        $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
        $products = wc_get_products(['limit' => -1]);
    
        ?>
        <table class="widefat wc_input_table" id="dynamic-consents-table">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Consent Text', 'wc-dynamic-consents'); ?></th>
                    <th><?php echo esc_html__('Include in', 'wc-dynamic-consents'); ?></th>
                    <th><?php echo esc_html__('Required', 'wc-dynamic-consents'); ?></th>
                    <th><?php echo esc_html__('Default checked', 'wc-dynamic-consents'); ?></th>
                    <th><?php echo esc_html__('Actions', 'wc-dynamic-consents'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saved_consents as $index => $consent): ?>
                    <?php $this->render_consent_row($index, $consent, $categories, $products); ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    
        <button type="button" class="button add-consent"><?php esc_html_e('Add New Consent', 'wc-dynamic-consents'); ?></button>
    
        <!-- 🔹 Ukryty szablon wiersza tabeli -->
        <template id="consent-row-template">
            <?php $this->render_consent_row('__INDEX__', [], $categories, $products); ?>
        </template>
        <template id="condition-row-template">
            <?php $this->render_condition_row('__CONSENT_INDEX__', '__CONDITION_INDEX__', [], $categories, $products); ?>
        </template>
        <?php
    }
    
    /**
     * Renderowanie pojedynczego wiersza tabeli
     */
    private function render_consent_row($index, $consent, $categories, $products) {
        ?>
        <tr>
            <td>
                <textarea name="dynamic_consents[<?php echo esc_attr($index); ?>][text]" 
                          rows="2" class="input-text wide-input full-width-textarea"><?php echo esc_textarea($consent['text'] ?? ''); ?></textarea>
            </td>
            <td>
                <div class="consent-conditions">
                    <?php if (!empty($consent['conditions'])): ?>
                        <?php foreach ($consent['conditions'] as $condition_index => $condition): ?>
                            <?php $this->render_condition_row($index, $condition_index, $condition, $categories, $products); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="button add-condition"><?php esc_html_e('Add Condition', 'wc-dynamic-consents'); ?></button>
            </td>
            <td>
                <input type="checkbox" name="dynamic_consents[<?php echo esc_attr($index); ?>][required]" 
                       value="1" <?php checked($consent['required'] ?? '', '1'); ?>>
            </td>
            <td>
                <input type="checkbox" name="dynamic_consents[<?php echo esc_attr($index); ?>][default_checked]" 
                       value="1" <?php checked($consent['default_checked'] ?? '', '1'); ?>>
            </td>
            <td>
                <button type="button" class="button remove-consent"><?php esc_html_e('Remove', 'wc-dynamic-consents'); ?></button>
            </td>
        </tr>
        <?php
    }
    
    /**
     * Renderuje pojedynczy warunek w sekcji "Include In"
     */
    private function render_condition_row($consent_index, $condition_index, $condition, $categories, $products) {
        ?>
        <div class="consent-condition">
            <select name="dynamic_consents[<?php echo esc_attr($consent_index); ?>][conditions][<?php echo esc_attr($condition_index); ?>][type]" class="consent-type">
                <option value="category" <?php selected($condition['type'] ?? '', 'category'); ?>><?php esc_html_e('Category', 'wc-dynamic-consents'); ?></option>
                <option value="product" <?php selected($condition['type'] ?? '', 'product'); ?>><?php esc_html_e('Product', 'wc-dynamic-consents'); ?></option>
            </select>
            <select name="dynamic_consents[<?php echo esc_attr($consent_index); ?>][conditions][<?php echo esc_attr($condition_index); ?>][id]" class="consent-target" data-selected="<?php echo esc_attr($condition['id'] ?? 'all'); ?>">
                <option value="all"><?php esc_html_e('All', 'wc-dynamic-consents'); ?></option>
            </select>
            <button type="button" class="button remove-condition"><?php esc_html_e('Remove', 'wc-dynamic-consents'); ?></button>
        </div>
        <?php
    }

    public function save_dynamic_consents_settings($value, $option, $raw_value) {
        if ($option['id'] === 'dynamic_consents') {
            foreach ($raw_value as &$consent) {
                if (isset($consent['text'])) {
                    $consent['text'] = wp_kses_post($consent['text']);
                }
            }
            update_option($option['id'], array_filter($raw_value));
            return $raw_value;
        }
        return $value;
    }

    public function display_order_consents($order) {
        $consents = get_post_meta($order->get_id(), '_dynamic_consents', true);
    
        if (!empty($consents)) {
            echo '<p><strong>' . esc_html__('Consents Given:', 'wc-dynamic-consents') . '</strong></p>';
            echo '<ul>';
            foreach ($consents as $consent) {
                // Usuwamy znaczniki HTML, aby nie wpływały na wyświetlanie w panelu
                $consent_text = wp_strip_all_tags($consent['text']);
                $consent_status = !empty($consent['accepted']) 
                    ? _x('Accepted ✅', 'Consent status', 'wc-dynamic-consents') 
                    : _x('Not Accepted ❌', 'Consent status', 'wc-dynamic-consents');
    
                echo '<li><strong>' . esc_html($consent_text) . ':<br></strong> ' . esc_html($consent_status) . '</li>';
            }
            echo '</ul>';
        }
    }
}


// 
