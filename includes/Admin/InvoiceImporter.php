<?php
/**
 * Explicit, bounded invoice row commit into existing order cost records.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Commits only reviewed, exact-matched invoice rows; raw files are never stored.
 */
final class InvoiceImporter {
	/**
	 * Register the privileged AJAX commit action.
	 */
	public function register(): void {
		add_action( 'wp_ajax_wpe_scm_commit_invoice_rows', array( $this, 'commit_rows' ) );
	}

	/**
	 * Validate and commit up to 50 explicitly selected invoice rows.
	 */
	public function commit_rows(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to import shipping costs.', 'wpessential-shipping-cost-monitor' ) ),
				403
			);
		}

		$nonce = isset( $_POST['nonce'] ) && is_string( $_POST['nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
			: '';

		if ( ! wp_verify_nonce( $nonce, 'wpe_scm_commit_invoice_rows' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'The import request expired. Reload the page and review the rows again.', 'wpessential-shipping-cost-monitor' ) ),
				403
			);
		}

		$rows = isset( $_POST['rows'] ) && is_array( $_POST['rows'] )
			? wp_unslash( $_POST['rows'] )
			: array();

		if ( empty( $rows ) || count( $rows ) > 50 ) {
			wp_send_json_error(
				array( 'message' => __( 'Commit between 1 and 50 matched rows at a time.', 'wpessential-shipping-cost-monitor' ) ),
				400
			);
		}

		$results = array();
		foreach ( $rows as $row ) {
			$results[] = $this->commit_row( $row );
		}

		wp_send_json_success(
			array(
				'results'   => $results,
				'persisted' => true,
				'committed' => true,
			)
		);
	}

	/**
	 * Validate one row against the exact WooCommerce order before saving.
	 *
	 * @param mixed $row Submitted row.
	 * @return array<string,mixed>
	 */
	private function commit_row( $row ): array {
		if (
			! is_array( $row )
			|| ! isset( $row['row'], $row['order_id'], $row['reference'], $row['amount'], $row['currency'], $row['batch'] )
		) {
			return array( 'status' => 'invalid' );
		}

		$row_number = absint( $row['row'] );
		$order_id   = absint( $row['order_id'] );
		$reference  = is_scalar( $row['reference'] ) ? preg_replace( '/^#\s*/', '', trim( sanitize_text_field( (string) $row['reference'] ) ) ) : '';
		$raw_amount = is_scalar( $row['amount'] ) ? trim( (string) $row['amount'] ) : '';
		$currency   = is_scalar( $row['currency'] ) ? strtoupper( sanitize_text_field( (string) $row['currency'] ) ) : '';
		$batch_hash = is_scalar( $row['batch'] ) ? strtolower( sanitize_text_field( (string) $row['batch'] ) ) : '';
		$evidence   = isset( $row['evidence'] ) && is_scalar( $row['evidence'] ) ? sanitize_key( (string) $row['evidence'] ) : '';
		$tax_basis  = isset( $row['tax_basis'] ) && is_scalar( $row['tax_basis'] ) ? sanitize_key( (string) $row['tax_basis'] ) : '';

		if (
			! $row_number
			|| ! $order_id
			|| '' === $reference
			|| strlen( $reference ) > 100
			|| ! preg_match( '/^[a-f0-9]{64}$/', $batch_hash )
			|| ! is_numeric( $raw_amount )
			|| (float) $raw_amount < 0
			|| ! preg_match( '/^[A-Z]{3}$/', $currency )
			|| ! array_key_exists( $currency, get_woocommerce_currencies() )
			|| ! in_array( $evidence, array( 'invoice_final', 'merchant_confirmed', 'booked_label' ), true )
			|| ! in_array( $tax_basis, array( 'unknown', 'exclusive', 'inclusive' ), true )
		) {
			return array(
				'row'    => $row_number,
				'status' => 'invalid',
			);
		}

		$amount = wc_format_decimal( $raw_amount, wc_get_price_decimals(), false );
		if ( ! is_numeric( $amount ) ) {
			return array(
				'row'    => $row_number,
				'status' => 'invalid',
			);
		}

		$order = wc_get_order( $order_id );
		if (
			! $order instanceof \WC_Order
			|| 'shop_order' !== $order->get_type()
			|| empty( $order->get_items( 'shipping' ) )
			|| ! current_user_can( 'edit_shop_order', $order_id )
			|| (
				(string) $order->get_id() !== $reference
				&& (string) $order->get_order_number() !== $reference
			)
		) {
			return array(
				'row'    => $row_number,
				'status' => 'order_mismatch',
			);
		}

		if ( strtoupper( $order->get_currency() ) !== $currency ) {
			return array(
				'row'    => $row_number,
				'status' => 'currency_mismatch',
				'order'  => $order->get_order_number(),
				'currency' => $order->get_currency(),
			);
		}

		$fingerprint = hash( 'sha256', $batch_hash . ':' . $row_number . ':' . $order_id );
		$saved_fingerprint = (string) $order->get_meta( '_wpe_scm_cost_import_fingerprint', true );

		if ( $saved_fingerprint === $fingerprint ) {
			return array(
				'row'    => $row_number,
				'status' => 'already_imported',
				'order'  => $order->get_order_number(),
			);
		}

		if ( '' !== (string) $order->get_meta( '_wpe_scm_cost_amount', true ) ) {
			return array(
				'row'    => $row_number,
				'status' => 'existing_cost',
				'order'  => $order->get_order_number(),
			);
		}

		$source = sprintf(
			/* translators: %d: invoice source row number. */
			__( 'Invoice CSV source row %d', 'wpessential-shipping-cost-monitor' ),
			$row_number
		);

		$order->update_meta_data( '_wpe_scm_cost_amount', $amount );
		$order->update_meta_data( '_wpe_scm_cost_currency', $currency );
		$order->update_meta_data( '_wpe_scm_cost_evidence', $evidence );
		$order->update_meta_data( '_wpe_scm_cost_tax_basis', $tax_basis );
		$order->update_meta_data( '_wpe_scm_cost_note', $source );
		$order->update_meta_data( '_wpe_scm_cost_updated_at', gmdate( 'Y-m-d H:i:s' ) );
		$order->update_meta_data( '_wpe_scm_cost_updated_by', get_current_user_id() );
		$order->update_meta_data( '_wpe_scm_cost_import_fingerprint', $fingerprint );
		$order->update_meta_data( '_wpe_scm_cost_import_batch', $batch_hash );
		$order->update_meta_data( '_wpe_scm_cost_import_row', $row_number );
		$order->save();

		$order->add_order_note(
			sprintf(
				/* translators: 1: amount, 2: currency, 3: source row, 4: evidence, 5: tax treatment. */
				__( 'Shipping cost imported from invoice CSV: %1$s %2$s, source row %3$d (%4$s; tax %5$s).', 'wpessential-shipping-cost-monitor' ),
				$amount,
				$currency,
				$row_number,
				$evidence,
				$tax_basis
			),
			0,
			true
		);

		return array(
			'row'    => $row_number,
			'status' => 'committed',
			'order'  => $order->get_order_number(),
		);
	}
}
