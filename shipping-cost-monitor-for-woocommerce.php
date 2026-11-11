<?php
/**
 * Plugin Name: Shipping Cost Monitor for WooCommerce
 * Plugin URI: https://wpessential.org
 * Description: Shipping cost intelligence and margin reporting for WooCommerce.
 * Version: 0.1.0
 * Requires at least: 7.0
 * Requires PHP: 8.1
 * WC requires at least: 11.2
 * WC tested up to: 11.2
 * Requires Plugins: woocommerce
 * Author: WPEssential
 * Author URI: https://wpessential.org
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: shipping-cost-monitor-for-woocommerce
 *
 * @package WPEssentialShippingCostMonitor
 */

defined( 'ABSPATH' ) || exit;

/*
 * Test builds before decision F-5 were installed as wpessential-shipping-cost-monitor/.
 * Both copies declare the same functions, constants and classes, so if the old copy is
 * active (loaded before or after this file) this copy stays dormant and asks the admin
 * to deactivate the old one. It starts on the next request after that.
 */
$wpe_scm_legacy_basename = 'wpessential-shipping-cost-monitor/wpessential-shipping-cost-monitor.php';
$wpe_scm_network_plugins = is_multisite() ? (array) get_site_option( 'active_sitewide_plugins', array() ) : array();
if (
	defined( 'WPE_SCM_FILE' )
	|| in_array( $wpe_scm_legacy_basename, (array) get_option( 'active_plugins', array() ), true )
	|| isset( $wpe_scm_network_plugins[ $wpe_scm_legacy_basename ] )
) {
	add_action(
		'admin_notices',
		static function (): void {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			printf(
				'<div class="notice notice-warning"><p>%s</p></div>',
				esc_html__(
					'Shipping Cost Monitor for WooCommerce is paused because an older build named "WPEssential Shipping Cost Monitor" is active. Deactivate the older build; your shipping cost records are kept.',
					'shipping-cost-monitor-for-woocommerce'
				)
			);
		}
	);
	return;
}

if ( ! function_exists( 'wpe_scm_initialize' ) ) {
	/**
	 * Start the plugin after WooCommerce has loaded.
	 */
	function wpe_scm_initialize(): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', 'wpe_scm_missing_woocommerce_notice' );
			return;
		}

		\WPEssential\ShippingCostMonitor\Plugin::instance()->init();
	}
}

if ( ! function_exists( 'wpe_scm_missing_woocommerce_notice' ) ) {
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
				'Shipping Cost Monitor for WooCommerce requires WooCommerce to be installed and active.',
				'shipping-cost-monitor-for-woocommerce'
			)
		);
	}
}

if ( ! function_exists( 'wpe_scm_missing_autoloader_notice' ) ) {
	/**
	 * Explain an incomplete package instead of failing with a fatal error.
	 */
	function wpe_scm_missing_autoloader_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			esc_html__(
				'Shipping Cost Monitor for WooCommerce is missing its generated files. Install the packaged plugin ZIP instead of the raw source.',
				'shipping-cost-monitor-for-woocommerce'
			)
		);
	}
}

define( 'WPE_SCM_VERSION', '0.1.0' );
define( 'WPE_SCM_FILE', __FILE__ );
define( 'WPE_SCM_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPE_SCM_URL', plugin_dir_url( __FILE__ ) );

$wpe_scm_build_id = 'source';
$wpe_scm_build_file = WPE_SCM_DIR . 'BUILD-ID.txt';
if ( is_readable( $wpe_scm_build_file ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads the plugin's own local BUILD-ID.txt (41 bytes max), not a remote URL.
	$wpe_scm_build_candidate = trim( (string) file_get_contents( $wpe_scm_build_file, false, null, 0, 41 ) );
	if ( preg_match( '/^[a-f0-9]{40}$/D', $wpe_scm_build_candidate ) ) {
		$wpe_scm_build_id = substr( $wpe_scm_build_candidate, 0, 12 );
	}
}
define( 'WPE_SCM_BUILD_ID', $wpe_scm_build_id );

if ( ! is_readable( WPE_SCM_DIR . 'vendor/autoload.php' ) ) {
	add_action( 'admin_notices', 'wpe_scm_missing_autoloader_notice' );
	return;
}

require_once WPE_SCM_DIR . 'vendor/autoload.php';

/**
 * Declare order-storage compatibility; all order access uses WooCommerce CRUD.
 */
add_action(
	'before_woocommerce_init',
	static function (): void {
		if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WPE_SCM_FILE, true );
		}
	}
);

add_action( 'plugins_loaded', 'wpe_scm_initialize', 20 );
