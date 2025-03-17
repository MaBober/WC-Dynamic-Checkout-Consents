<?php
/**
 * Plugin Name: WooCommerce Dynamic Checkout Consents
 * Plugin URI: https://www.marcin.bober.pl
 * Description: Add dynamic consents to WooCommerce checkout.
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * Author: Marcin Bober
 * Author URI: https://www.marcin.bober.pl
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-dynamic-consents
 * Domain Path: /languages
 * Requires Plugins: WooCommerce
 *  
 * @package WooCommerceDynamicCheckoutConsents
 * @category WooCommerce
 * Author: Marcin Bober
 * License: GPL-2.0+
 * Link: https://www.marcin.bober.pl
 * Since: 1.0.0
 *
 * This plugin allows you to add dynamic consents to the WooCommerce checkout page.
 * You can define multiple consents that customers need to accept before placing an order.
 * The consents are saved as order meta data and displayed in the admin order details.
 * Each consent can be displayed for specific products or product categories and be required or optional.
 * Administrators can define the consents in the WooCommerce settings.
 * Basic HTML is allowed in the consent text.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('WC_DYNAMIC_CONSENTS_PLUGIN_FILE', __FILE__);

require_once plugin_dir_path(__FILE__) . 'includes/core/class-wc-dynamic-consents-loader.php';

function wc_dynamic_checkout_consents_init() {

    error_log("MAIN - wc_dynamic_checkout_consents_init");
    WC_Dynamic_Consents_Loader::get_instance();

}
add_action('plugins_loaded', 'wc_dynamic_checkout_consents_init');

