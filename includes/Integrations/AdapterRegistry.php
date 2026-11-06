<?php
/**
 * Discovery and safe status projection for optional carrier adapters.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Integrations;

defined( 'ABSPATH' ) || exit;

/**
 * Exposes only validated adapter metadata to the admin UI.
 */
final class AdapterRegistry {
	/**
	 * Get available adapters registered by this plugin or an extension.
	 *
	 * @return CarrierAdapterInterface[]
	 */
	public function get_adapters(): array {
		$registered = apply_filters( 'wpe_scm_carrier_adapters', array() );
		if ( ! is_array( $registered ) ) {
			return array();
		}

		$adapters = array();
		foreach ( $registered as $adapter ) {
			if ( ! $adapter instanceof CarrierAdapterInterface ) {
				continue;
			}

			try {
				$id = sanitize_key( $adapter->get_id() );
				$label = sanitize_text_field( $adapter->get_label() );
				if ( '' === $id || '' === $label || isset( $adapters[ $id ] ) ) {
					continue;
				}
				$adapters[ $id ] = $adapter;
			} catch ( \Throwable $error ) {
				continue;
			}
		}

		return $adapters;
	}

	/**
	 * Project adapter details for the admin UI without exposing credentials.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function get_public_status(): array {
		$allowed_capabilities = array(
			'shipment_ids',
			'tracking_ids',
			'booked_costs',
			'invoice_costs',
			'webhooks',
		);
		$allowed_states = array( 'disconnected', 'connected', 'reauth_required', 'unavailable' );
		$status = array();

		foreach ( $this->get_adapters() as $id => $adapter ) {
			try {
				$regions = array_map( 'sanitize_text_field', array_filter( $adapter->get_regions(), 'is_string' ) );
				$capabilities = array_values(
					array_intersect(
						array_map( 'sanitize_key', array_filter( $adapter->get_capabilities(), 'is_string' ) ),
						$allowed_capabilities
					)
				);
				$state = $adapter->get_connection_state();
				$status[] = array(
					'id'           => $id,
					'label'        => sanitize_text_field( $adapter->get_label() ),
					'regions'      => array_values( array_unique( $regions ) ),
					'capabilities' => array_values( array_unique( $capabilities ) ),
					'state'        => in_array( $state, $allowed_states, true ) ? $state : 'unavailable',
				);
			} catch ( \Throwable $error ) {
				$status[] = array(
					'id'           => $id,
					'label'        => $id,
					'regions'      => array(),
					'capabilities' => array(),
					'state'        => 'unavailable',
				);
			}
		}

		return $status;
	}
}
