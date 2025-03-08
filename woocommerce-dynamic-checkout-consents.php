<?php
/**
 * Plugin Name: WooCommerce Dynamic Checkout Consents
 * Plugin URI: https://www.marcin.bober.pl
 * Description: Add dynamic consents to WooCommerce checkout.
 * Version: 1.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Marcin Bober
 * Author URI: https://www.marcin.bober.pl
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-dynamic-consents
 * Domain Path: /languages
 * Requires Plugins: WooCommerce
 *  
 * @package WooCommerceDynamicCheckoutConsents
 * @category WooCommerce
 * @author Marcin Bober
 * @license GPL-2.0+
 * @link https://www.marcin.bober.pl
 * @since 1.0.0
 *
 * This plugin allows you to add dynamic consents to the WooCommerce checkout page.
 * You can define multiple consents that customers need to accept before placing an order.
 * The consents are saved as order meta data and displayed in the admin order details.
 * Each consent can be displayed for specific products or product categories.
 */

 if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__FILE__) . 'includes/class-wc-dynamic-consents.php';

new WC_Dynamic_Consents();


