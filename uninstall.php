<?php
/**
 * Uninstall cleanup for the free plugin.
 *
 * Order-level shipping cost records are merchant accounting data stored on WooCommerce
 * orders and are kept. Builds before 1.0.0 stored an encrypted ShipStation key in this
 * plugin; it is removed here unless the Pro add-on, which now owns it, is installed.
 *
 * @package WPEssentialShippingCostMonitor
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! file_exists( WP_PLUGIN_DIR . '/shipping-cost-monitor-pro-for-woocommerce/shipping-cost-monitor-pro-for-woocommerce.php' ) ) {
	delete_option( 'wpe_scm_shipstation_credentials' );
}
