<?php
/**
 * Plugin Name: WooCommerce Add Attachment
 * Plugin URI: https://github.com/gronnmann/woo-add-attachment
 * Description: Allows administrators to configure attachments for WooCommerce order confirmation emails
 * Version: 1.0.0
 * Author: gronnmann
 * Author URI: https://github.com/gronnmann
 * WC requires at least: 3.0.0
 * WC tested up to: 8.0.0
 *
 * @package WooAddAttachment
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    return;
}

// Declare HPOS compatibility
add_action('before_woocommerce_init', function() {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
    }
});

define('WAA_VERSION', '1.0.0');
define('WAA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WAA_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once WAA_PLUGIN_DIR . 'includes/class-woo-add-attachment.php';

function waa_init() {
    // Create the main plugin instance
    $plugin = new WooAddAttachment();
    $plugin->init();
}
add_action('plugins_loaded', 'waa_init');