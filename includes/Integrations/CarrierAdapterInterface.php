<?php
/**
 * Contract for optional carrier evidence adapters.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Integrations;

defined( 'ABSPATH' ) || exit;

/**
 * Provider adapters return shipment evidence only; they never mutate checkout rates or orders.
 */
interface CarrierAdapterInterface {
	/**
	 * Stable machine-readable provider key.
	 */
	public function get_id(): string;

	/**
	 * Human-readable provider name.
	 */
	public function get_label(): string;

	/**
	 * Regions supported by this adapter, using documented short labels.
	 *
	 * @return string[]
	 */
	public function get_regions(): array;

	/**
	 * Evidence capabilities supported by this adapter.
	 *
	 * Supported values include shipment_ids, tracking_ids, booked_costs, invoice_costs and webhooks.
	 *
	 * @return string[]
	 */
	public function get_capabilities(): array;

	/**
	 * Current non-secret connection status.
	 *
	 * @return string One of disconnected, connected, reauth_required or unavailable.
	 */
	public function get_connection_state(): string;

	/**
	 * Fetch a bounded page of normalized shipment evidence.
	 *
	 * Query keys: from, to and cursor. Return keys: items and next_cursor.
	 * Each item may contain provider_shipment_id, tracking_id, order_reference,
	 * amount, currency, evidence and occurred_at. Do not include recipient PII.
	 * Return WP_Error on provider or validation failure.
	 *
	 * @param array<string,mixed> $query Bounded date window and optional cursor.
	 * @return array|\WP_Error
	 */
	public function fetch_shipments( array $query );
}
