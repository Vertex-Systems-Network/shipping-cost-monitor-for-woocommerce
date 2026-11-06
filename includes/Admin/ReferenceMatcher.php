<?php
/**
 * Exact, non-persistent invoice reference matching preview.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Matches a bounded set of invoice references without saving source rows.
 */
final class ReferenceMatcher {
	/**
	 * Register the private admin AJAX action.
	 */
	public function register(): void {
		add_action( 'wp_ajax_wpe_scm_match_preview', array( $this, 'match_preview' ) );
	}

	/**
	 * Return exact WooCommerce order-number or order-ID matches.
	 */
	public function match_preview(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to match invoice references.', 'wpessential-shipping-cost-monitor' ) ),
				403
			);
		}

		$nonce = isset( $_POST['nonce'] ) && is_string( $_POST['nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
			: '';

		if ( ! wp_verify_nonce( $nonce, 'wpe_scm_match_preview' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The matching request expired. Reload the page and try again.', 'wpessential-shipping-cost-monitor' ) ),
				403
			);
		}

		$references = isset( $_POST['references'] ) && is_array( $_POST['references'] )
			? wp_unslash( $_POST['references'] )
			: array();

		if ( empty( $references ) || count( $references ) > 50 ) {
			wp_send_json_error(
				array( 'message' => __( 'Match between 1 and 50 invoice rows at a time.', 'wpessential-shipping-cost-monitor' ) ),
				400
			);
		}

		$normalized = array();
		foreach ( $references as $row ) {
			if ( ! is_array( $row ) || ! isset( $row['row'] ) || ! isset( $row['reference'] ) || ! is_scalar( $row['reference'] ) ) {
				wp_send_json_error(
					array( 'message' => __( 'The reference list has an invalid row.', 'wpessential-shipping-cost-monitor' ) ),
					400
				);
			}

			$reference = trim( sanitize_text_field( (string) $row['reference'] ) );
			$reference = preg_replace( '/^#\\s*/', '', $reference );
			if ( '' === $reference || strlen( $reference ) > 100 ) {
				wp_send_json_error(
					array( 'message' => __( 'Each reference must contain 1 to 100 characters.', 'wpessential-shipping-cost-monitor' ) ),
					400
				);
			}

			$normalized[] = array(
				'row'       => absint( $row['row'] ),
				'reference' => $reference,
			);
		}

		$counts = array_count_values( array_column( $normalized, 'reference' ) );
		$results = array();

		foreach ( $normalized as $entry ) {
			$reference = $entry['reference'];
			if ( $counts[ $reference ] > 1 ) {
				$results[] = array(
					'row'    => $entry['row'],
					'status' => 'duplicate',
				);
				continue;
			}

			$candidate_ids = array();
			if ( function_exists( 'wc_order_search' ) ) {
				$search_results = wc_order_search( $reference );
				if ( is_array( $search_results ) ) {
					$candidate_ids = array_merge( $candidate_ids, $search_results );
				}
			}

			if ( ctype_digit( $reference ) ) {
				$candidate_ids[] = absint( $reference );
			}

			$candidate_ids = array_unique( array_filter( array_map( 'absint', $candidate_ids ) ) );
			$matches = array();

			foreach ( $candidate_ids as $candidate_id ) {
				$order = wc_get_order( $candidate_id );
				if (
					$order instanceof \WC_Order
					&& 'shop_order' === $order->get_type()
					&& ! empty( $order->get_items( 'shipping' ) )
					&& (
						(string) $order->get_order_number() === $reference
						|| (string) $order->get_id() === $reference
					)
				) {
					$matches[ $order->get_id() ] = $order;
				}
			}

			if ( 1 === count( $matches ) ) {
				$order = reset( $matches );
				$has_existing_cost = '' !== (string) $order->get_meta( '_wpe_scm_cost_amount', true );
				$results[] = array(
					'row'               => $entry['row'],
					'status'            => $has_existing_cost ? 'existing_cost' : 'matched',
					'order_id'          => $order->get_id(),
					'order_number'      => $order->get_order_number(),
					'order_status'      => $order->get_status(),
					'currency'          => $order->get_currency(),
					'has_existing_cost' => $has_existing_cost,
				);
			} elseif ( count( $matches ) > 1 ) {
				$results[] = array(
					'row'    => $entry['row'],
					'status' => 'ambiguous',
				);
			} else {
				$results[] = array(
					'row'    => $entry['row'],
					'status' => 'unmatched',
				);
			}
		}

		wp_send_json_success(
			array(
				'results'     => $results,
				'persisted'   => false,
				'committed'   => false,
			)
		);
	}
}
