<?php
/**
 * Plugin Name: WPEssential Shipping Cost Monitor
 * Description: Shipping cost intelligence and margin reporting for WooCommerce.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.1
 * WC requires at least: 11.2
 * Requires Plugins: woocommerce
 * Author: WPEssential
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpessential-shipping-cost-monitor
 * Domain Path: /languages
 *
 * @package WPEssentialShippingCostMonitor
 */

defined( 'ABSPATH' ) || exit;

define( 'WPE_SCM_VERSION', '0.1.0' );
define( 'WPE_SCM_FILE', __FILE__ );
define( 'WPE_SCM_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPE_SCM_URL', plugin_dir_url( __FILE__ ) );

require_once WPE_SCM_DIR . 'vendor/autoload.php';

/**
 * Start the plugin after WooCommerce has loaded.
 */
function wpe_scm_initialize(): void {
	load_plugin_textdomain(
		'wpessential-shipping-cost-monitor',
		false,
		dirname( plugin_basename( WPE_SCM_FILE ) ) . '/languages'
	);

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wpe_scm_missing_woocommerce_notice' );
		return;
	}

	\WPEssential\ShippingCostMonitor\Plugin::instance()->init();
}
add_action( 'plugins_loaded', 'wpe_scm_initialize', 20 );

/**
 * Tell administrators that WooCommerce must be active.
 */
function wpe_scm_missing_woocommerce_notice(): void {
	if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-error"><p>%s</p></div>',
		esc_html__(
			'WPEssential Shipping Cost Monitor requires WooCommerce to be installed and active.',
			'wpessential-shipping-cost-monitor'
		)
	);
}
