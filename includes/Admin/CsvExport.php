<?php
/**
 * Permission-protected, privacy-minimized report CSV export.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Streams order-level shipping records without customer personal data.
 */
final class CsvExport {
	/**
	 * Register the export action.
	 */
	public function register(): void {
		add_action( 'admin_post_wpe_scm_export_report', array( $this, 'export' ) );
	}

	/**
	 * Stream the selected date range as CSV.
	 */
	public function export(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die(
				esc_html__( 'You do not have permission to export shipping reports.', 'shipping-cost-monitor-for-woocommerce' ),
				esc_html__( 'Access denied', 'shipping-cost-monitor-for-woocommerce' ),
				array( 'response' => 403 )
			);
		}

		check_admin_referer( 'wpe_scm_export_report' );

		$requested_start_raw = isset( $_GET['wpe_scm_start'] ) && is_string( $_GET['wpe_scm_start'] )
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- Nonce checked by check_admin_referer() above; length-capped and sanitized below.
			? wp_unslash( $_GET['wpe_scm_start'] )
			: null;
		$requested_end_raw = isset( $_GET['wpe_scm_end'] ) && is_string( $_GET['wpe_scm_end'] )
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput -- Nonce checked by check_admin_referer() above; length-capped and sanitized below.
			? wp_unslash( $_GET['wpe_scm_end'] )
			: null;
		$requested_start = is_string( $requested_start_raw ) && strlen( $requested_start_raw ) <= 10
			? sanitize_text_field( $requested_start_raw )
			: null;
		$requested_end = is_string( $requested_end_raw ) && strlen( $requested_end_raw ) <= 10
			? sanitize_text_field( $requested_end_raw )
			: null;
		$period = Summary::normalize_period( $requested_start, $requested_end );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header(
			'Content-Disposition: attachment; filename="shipping-cost-report-' .
			$period['start'] . '-to-' . $period['end'] . '.csv"'
		);

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Streams the CSV to the HTTP response; no file is written.
		$output = fopen( 'php://output', 'w' );
		if ( false === $output ) {
			wp_die( esc_html__( 'Unable to open the report stream.', 'shipping-cost-monitor-for-woocommerce' ) );
		}

		fputcsv(
			$output,
			array(
				__( 'Order ID', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Order number', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Order date', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Status', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Currency', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Shipping refunded (ex tax)', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Shipping charged net (ex tax)', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Recorded cost', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Cost currency', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Evidence', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Cost tax treatment', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Comparable variance', 'shipping-cost-monitor-for-woocommerce' ),
				__( 'Comparison status', 'shipping-cost-monitor-for-woocommerce' ),
			)
		);

		$page = 1;
		$max_pages = 1;

		do {
			$result = wc_get_orders(
				array(
					'type'         => 'shop_order',
					'status'       => array( 'processing', 'completed' ),
					'date_created' => $period['start'] . '...' . $period['end'],
					'limit'        => 100,
					'paged'        => $page,
					'paginate'     => true,
					'return'       => 'objects',
					'orderby'      => 'date',
					'order'        => 'DESC',
				)
			);

			if ( ! is_object( $result ) || empty( $result->orders ) ) {
				break;
			}

			$max_pages = max( 1, (int) $result->max_num_pages );

			foreach ( $result->orders as $order ) {
				if ( ! $order instanceof \WC_Order || empty( $order->get_items( 'shipping' ) ) ) {
					continue;
				}

				$currency     = (string) $order->get_currency();
				$cost_amount  = $order->get_meta( '_wpe_scm_cost_amount', true );
				$cost_currency = (string) $order->get_meta( '_wpe_scm_cost_currency', true );
				$evidence     = (string) $order->get_meta( '_wpe_scm_cost_evidence', true );
				$tax_basis    = (string) $order->get_meta( '_wpe_scm_cost_tax_basis', true );
				$cost_currency = '' !== $cost_currency ? $cost_currency : $currency;
				$shipping_refunded = min( (float) $order->get_shipping_total(), (float) $order->get_total_shipping_refunded() );
				$shipping_net = (float) $order->get_shipping_total() - $shipping_refunded;
				$variance     = '';
				$comparison   = __( 'No confirmed cost', 'shipping-cost-monitor-for-woocommerce' );

				if (
					in_array( $evidence, array( 'invoice_final', 'merchant_confirmed' ), true )
					&& '' !== (string) $cost_amount
					&& is_numeric( $cost_amount )
					&& (float) $cost_amount >= 0
				) {
					if ( $currency !== $cost_currency ) {
						$comparison = __( 'Currency mismatch', 'shipping-cost-monitor-for-woocommerce' );
					} elseif ( 'exclusive' !== $tax_basis ) {
						$comparison = __( 'Tax basis not comparable', 'shipping-cost-monitor-for-woocommerce' );
					} else {
						$variance = wc_format_decimal(
							$shipping_net - (float) $cost_amount,
							wc_get_price_decimals()
						);
						$comparison = __( 'Comparable', 'shipping-cost-monitor-for-woocommerce' );
					}
				} elseif ( '' !== $evidence && ! in_array( $evidence, array( 'invoice_final', 'merchant_confirmed' ), true ) ) {
					$comparison = __( 'Evidence not confirmed', 'shipping-cost-monitor-for-woocommerce' );
				}

				$created = $order->get_date_created();
				$row = array(
					$order->get_id(),
					self::safe_text( $order->get_order_number() ),
					$created ? wp_date( 'Y-m-d H:i:s', $created->getTimestamp() ) : '',
					self::safe_text( $order->get_status() ),
					self::safe_text( $currency ),
					wc_format_decimal( $shipping_refunded, wc_get_price_decimals() ),
					wc_format_decimal( $shipping_net, wc_get_price_decimals() ),
					is_numeric( $cost_amount ) && (float) $cost_amount >= 0 ? wc_format_decimal( $cost_amount, wc_get_price_decimals() ) : '',
					self::safe_text( $cost_currency ),
					self::safe_text( $evidence ),
					self::safe_text( $tax_basis ),
					$variance,
					self::safe_text( $comparison ),
				);

				fputcsv( $output, $row );
			}

			++$page;
		} while ( $page <= $max_pages );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Closes the php://output response stream opened above.
		fclose( $output );
		exit;
	}

	/**
	 * Neutralize spreadsheet formula prefixes on text cells.
	 *
	 * @param string $value Untrusted text.
	 */
	private static function safe_text( string $value ): string {
		if ( preg_match( '/^[\t\r\n ]*[=+\-@]/', $value ) ) {
			return "'" . $value;
		}

		return $value;
	}
}
