<?php
/**
 * Manual shipping cost fields on WooCommerce order edit screens.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Adds an order-level cost record using WooCommerce CRUD APIs.
 */
final class OrderCostFields {
	/**
	 * Register order edit hooks.
	 */
	public function register(): void {
		add_action(
			'woocommerce_admin_order_data_after_shipping_address',
			array( $this, 'render_fields' ),
			20,
			1
		);

		add_action(
			'woocommerce_process_shop_order_meta',
			array( $this, 'save_fields' ),
			20,
			1
		);
	}

	/**
	 * Render the fields in the WooCommerce order editor.
	 *
	 * @param \WC_Order $order Order object.
	 */
	public function render_fields( $order ): void {
		if ( ! $order instanceof \WC_Order || ! current_user_can( 'edit_shop_order', $order->get_id() ) ) {
			return;
		}

		$order_id  = $order->get_id();
		$amount    = (string) $order->get_meta( '_wpe_scm_cost_amount', true );
		$currency  = (string) $order->get_meta( '_wpe_scm_cost_currency', true );
		$evidence  = (string) $order->get_meta( '_wpe_scm_cost_evidence', true );
		$note      = (string) $order->get_meta( '_wpe_scm_cost_note', true );
		$currency  = '' !== $currency ? $currency : $order->get_currency();
		$precision = wc_get_price_decimals();
		$step      = $precision > 0 ? '0.' . str_repeat( '0', $precision - 1 ) . '1' : '1';
		$nonce_key = 'wpe_scm_order_cost_nonce_' . $order_id;

		echo '<div class="wpe-scm-order-cost">';
		echo '<h3>' . esc_html__( 'Shipping Cost Monitor', 'wpessential-shipping-cost-monitor' ) . '</h3>';
		echo '<p class="description">' . esc_html__( 'Record the merchant-paid shipping cost and its evidence. Quotes remain estimates and are not treated as confirmed costs.', 'wpessential-shipping-cost-monitor' ) . '</p>';

		wp_nonce_field( 'wpe_scm_save_order_cost_' . $order_id, $nonce_key );

		if ( '' !== $amount ) {
			printf(
				'<p><strong>%1$s</strong> %2$s %3$s <span class="description">(%4$s)</span></p>',
				esc_html__( 'Current recorded cost:', 'wpessential-shipping-cost-monitor' ),
				esc_html( $currency ),
				esc_html( $amount ),
				esc_html( $this->evidence_label( $evidence ) )
			);
		} else {
			echo '<p class="description">' . esc_html__( 'No cost has been recorded for this order.', 'wpessential-shipping-cost-monitor' ) . '</p>';
		}

		echo '<p class="form-field form-field-wide">';
		printf(
			'<label for="wpe_scm_cost_amount">%s</label>',
			esc_html__( 'Merchant-paid shipping cost', 'wpessential-shipping-cost-monitor' )
		);
		printf(
			'<input type="number" class="short wc_input_price" id="wpe_scm_cost_amount" name="wpe_scm_cost_amount" min="0" step="%1$s" value="%2$s" inputmode="decimal" />',
			esc_attr( $step ),
			esc_attr( $amount )
		);
		echo '</p>';

		echo '<p class="form-field form-field-wide">';
		printf(
			'<label for="wpe_scm_cost_currency">%s</label>',
			esc_html__( 'Cost currency (ISO 4217)', 'wpessential-shipping-cost-monitor' )
		);
		printf(
			'<input type="text" class="short" id="wpe_scm_cost_currency" name="wpe_scm_cost_currency" maxlength="3" pattern="[A-Za-z]{3}" value="%1$s" aria-describedby="wpe-scm-cost-currency-help" />',
			esc_attr( $currency )
		);
		echo '<span id="wpe-scm-cost-currency-help" class="description">' . esc_html__( 'Defaults to the order currency. A different currency will not be compared automatically.', 'wpessential-shipping-cost-monitor' ) . '</span>';
		echo '</p>';

		echo '<p class="form-field form-field-wide">';
		printf(
			'<label for="wpe_scm_cost_evidence">%s</label>',
			esc_html__( 'Cost evidence', 'wpessential-shipping-cost-monitor' )
		);
		echo '<select class="short" id="wpe_scm_cost_evidence" name="wpe_scm_cost_evidence">';
		foreach ( $this->evidence_options() as $value => $label ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $value ),
				selected( $evidence, $value, false ),
				esc_html( $label )
			);
		}
		echo '</select>';
		echo '</p>';

		echo '<p class="form-field form-field-wide">';
		printf(
			'<label for="wpe_scm_cost_note">%s</label>',
			esc_html__( 'Source or note', 'wpessential-shipping-cost-monitor' )
		);
		printf(
			'<textarea class="short" id="wpe_scm_cost_note" name="wpe_scm_cost_note" rows="2">%s</textarea>',
			esc_textarea( $note )
		);
		echo '</p>';

		echo '<p class="description">' . esc_html__( 'Leave the amount empty and save the order to remove the current cost record.', 'wpessential-shipping-cost-monitor' ) . '</p>';
		echo '</div>';
	}

	/**
	 * Validate and save submitted values via the WooCommerce order object.
	 *
	 * @param int $order_id Order ID.
	 */
	public function save_fields( $order_id ): void {
		$order_id = absint( $order_id );

		if ( ! $order_id || ! isset( $_POST['wpe_scm_order_cost_nonce_' . $order_id] ) ) {
			return;
		}

		$nonce_key = 'wpe_scm_order_cost_nonce_' . $order_id;
		$nonce     = sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) );

		if (
			! wp_verify_nonce( $nonce, 'wpe_scm_save_order_cost_' . $order_id )
			|| ! current_user_can( 'edit_shop_order', $order_id )
			|| ! isset( $_POST['wpe_scm_cost_amount'] )
		) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$raw_amount = trim( sanitize_text_field( wp_unslash( $_POST['wpe_scm_cost_amount'] ) ) );

		if ( '' === $raw_amount ) {
			$previous_amount   = (string) $order->get_meta( '_wpe_scm_cost_amount', true );
			$previous_currency = (string) $order->get_meta( '_wpe_scm_cost_currency', true );
			$previous_evidence = (string) $order->get_meta( '_wpe_scm_cost_evidence', true );

			if ( '' === $previous_amount ) {
				return;
			}

			$this->delete_cost_meta( $order );
			$order->save();
			$order->add_order_note(
				sprintf(
					/* translators: 1: currency code, 2: amount, 3: evidence label. */
					__( 'Shipping cost record removed: %1$s %2$s (%3$s).', 'wpessential-shipping-cost-monitor' ),
					$previous_currency,
					$previous_amount,
					$this->evidence_label( $previous_evidence )
				),
				0,
				true
			);
			return;
		}

		if ( ! is_numeric( $raw_amount ) || (float) $raw_amount < 0 ) {
			return;
		}

		$amount   = wc_format_decimal( $raw_amount, wc_get_price_decimals(), false );
		$currency = isset( $_POST['wpe_scm_cost_currency'] )
			? strtoupper( sanitize_text_field( wp_unslash( $_POST['wpe_scm_cost_currency'] ) ) )
			: '';
		$evidence = isset( $_POST['wpe_scm_cost_evidence'] )
			? sanitize_key( wp_unslash( $_POST['wpe_scm_cost_evidence'] ) )
			: '';
		$note     = isset( $_POST['wpe_scm_cost_note'] )
			? sanitize_textarea_field( wp_unslash( $_POST['wpe_scm_cost_note'] ) )
			: '';

		if (
			! is_numeric( $amount )
			|| ! preg_match( '/^[A-Z]{3}$/', $currency )
			|| ! array_key_exists( $currency, get_woocommerce_currencies() )
			|| ! array_key_exists( $evidence, $this->evidence_options() )
		) {
			return;
		}

		$previous_amount   = (string) $order->get_meta( '_wpe_scm_cost_amount', true );
		$previous_currency = (string) $order->get_meta( '_wpe_scm_cost_currency', true );
		$previous_evidence = (string) $order->get_meta( '_wpe_scm_cost_evidence', true );

		$order->update_meta_data( '_wpe_scm_cost_amount', $amount );
		$order->update_meta_data( '_wpe_scm_cost_currency', $currency );
		$order->update_meta_data( '_wpe_scm_cost_evidence', $evidence );
		$order->update_meta_data( '_wpe_scm_cost_note', $note );
		$order->update_meta_data( '_wpe_scm_cost_updated_at', gmdate( 'Y-m-d H:i:s' ) );
		$order->update_meta_data( '_wpe_scm_cost_updated_by', get_current_user_id() );
		$order->save();

		if (
			$previous_amount !== $amount
			|| $previous_currency !== $currency
			|| $previous_evidence !== $evidence
		) {
			$action = '' === $previous_amount ? 'added' : 'updated';
			$order->add_order_note(
				sprintf(
					/* translators: 1: action, 2: old currency, 3: old amount, 4: old evidence, 5: new currency, 6: new amount, 7: new evidence. */
					__( 'Shipping cost record %1$s: %2$s %3$s (%4$s) → %5$s %6$s (%7$s).', 'wpessential-shipping-cost-monitor' ),
					$action,
					$previous_currency,
					$previous_amount,
					$this->evidence_label( $previous_evidence ),
					$currency,
					$amount,
					$this->evidence_label( $evidence )
				),
				0,
				true
			);
		}
	}

	/**
	 * Remove the selected cost record from an order.
	 *
	 * @param \WC_Order $order Order object.
	 */
	private function delete_cost_meta( \WC_Order $order ): void {
		foreach (
			array(
				'_wpe_scm_cost_amount',
				'_wpe_scm_cost_currency',
				'_wpe_scm_cost_evidence',
				'_wpe_scm_cost_note',
				'_wpe_scm_cost_updated_at',
				'_wpe_scm_cost_updated_by',
			) as $meta_key
		) {
			$order->delete_meta_data( $meta_key );
		}
	}

	/**
	 * Evidence types supported in the manual ledger.
	 *
	 * @return array<string,string>
	 */
	private function evidence_options(): array {
		return array(
			'merchant_confirmed' => __( 'Merchant-confirmed', 'wpessential-shipping-cost-monitor' ),
			'invoice_final'      => __( 'Invoice-final', 'wpessential-shipping-cost-monitor' ),
			'booked_label'       => __( 'Booked label / manifest', 'wpessential-shipping-cost-monitor' ),
			'estimate'           => __( 'Estimate only', 'wpessential-shipping-cost-monitor' ),
		);
	}

	/**
	 * Get a readable evidence label.
	 *
	 * @param string $evidence Evidence key.
	 */
	private function evidence_label( string $evidence ): string {
		$options = $this->evidence_options();
		return isset( $options[ $evidence ] ) ? $options[ $evidence ] : __( 'Unclassified', 'wpessential-shipping-cost-monitor' );
	}
}
