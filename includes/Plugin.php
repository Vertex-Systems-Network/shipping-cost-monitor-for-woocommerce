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
		add_filter(
			'wpe_scm_carrier_adapters',
			static function ( $adapters ) {
				$adapters = is_array( $adapters ) ? $adapters : array();
				$adapters[] = new Integrations\ShipStationAdapter();
				return $adapters;
			}
		);

		$carrier_sync = new Admin\CarrierSyncController();
		$carrier_sync->register();

		$page = new Admin\Page();
		$page->register();

		$order_cost_fields = new Admin\OrderCostFields();
		$order_cost_fields->register();

		$csv_export = new Admin\CsvExport();
		$csv_export->register();

		$reference_matcher = new Admin\ReferenceMatcher();
		$reference_matcher->register();

		$invoice_importer = new Admin\InvoiceImporter();
		$invoice_importer->register();
	}
}
