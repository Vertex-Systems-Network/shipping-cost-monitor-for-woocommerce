<?php
/**
 * Minimal PSR-4 fallback used before Composer's generated autoloader is packaged.
 *
 * @package WPEssentialShippingCostMonitor
 */

defined( 'ABSPATH' ) || exit;

spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'WPEssential\\ShippingCostMonitor\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$file           = WPE_SCM_DIR . 'includes/' . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
);
