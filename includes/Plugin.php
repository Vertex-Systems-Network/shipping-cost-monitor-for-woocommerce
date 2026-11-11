<?php
/**
 * Main plugin coordinator.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor;

defined( 'ABSPATH' ) || exit;

/**
 * Boots the plugin's admin-facing modules.
 */
final class Plugin {
	/**
	 * Plugin instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Get the plugin instance.
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register plugin hooks.
	 */
	public function init(): void {
		$page = new Admin\Page();
		$page->register();

		$order_cost_fields = new Admin\OrderCostFields();
		$order_cost_fields->register();

		$csv_export = new Admin\CsvExport();
		$csv_export->register();

		/**
		 * Fires after the core modules are registered. Add-ons hook here to register their own.
		 *
		 * @param Plugin $plugin Plugin instance.
		 */
		do_action( 'wpe_scm_init', $this );
	}
}
