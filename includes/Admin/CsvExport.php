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
				esc_html__( 'You do not have permission to export shipping reports.', 'wpessential-shipping-cost-monitor' ),
				esc_html__( 'Access denied', 'wpessential-shipping-cost-monitor' ),
				array( 'response' => 403 )
			);
		}

		check_admin_referer( 'wpe_scm_export_report' );

		$requested_start = isset( $_GET['wpe_scm_start'] ) && is_string( $_GET['wpe_scm_start'] )
			? sanitize_text_field( wp_unslash( $_GET['wpe_scm_start'] ) )
			: null;
		$requested_end = isset( $_GET['wpe_scm_end'] ) && is_string( $_GET['wpe_scm_end'] )
			? sanitize_text_field( wp_unslash( $_GET['wpe_scm_end'] ) )
			: null;
		$period = Summary::normalize_period( $requested_start, $requested_end );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header(
			'Content-Disposition: attachment; filename="shipping-cost-report-' .
			$period['start'] . '-to-' . $period['end'] . '.csv"'
		);

		$output = fopen( 'php://output', 'w' );
		if ( false === $output ) {
			wp_die( esc_html__( 'Unable to open the report stream.', 'wpessential-shipping-cost-monitor' ) );
		}

		fputcsv(
			$output,
			array(
				__( 'Order ID', 'wpessential-shipping-cost-monitor' ),
				__( 'Order number', 'wpessential-shipping-cost-monitor' ),
				__( 'Order date', 'wpessential-shipping-cost-monitor' ),
				__( 'Status', 'wpessential-shipping-cost-monitor' ),
				__( 'Currency', 'wpessential-shipping-cost-monitor' ),
				__( 'Shipping charged (ex tax)', 'wpessential-shipping-cost-monitor' ),
				__( 'Recorded cost', 'wpessential-shipping-cost-monitor' ),
				__( 'Cost currency', 'wpessential-shipping-cost-monitor' ),
				__( 'Evidence', 'wpessential-shipping-cost-monitor' ),
				__( 'Cost tax treatment', 'wpessential-shipping-cost-monitor' ),
				__( 'Comparable variance', 'wpessential-shipping-cost-monitor' ),
				__( 'Comparison status', 'wpessential-shipping-cost-monitor' ),
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
				$variance     = '';
				$comparison   = __( 'No confirmed cost', 'wpessential-shipping-cost-monitor' );

				if (
					in_array( $evidence, array( 'invoice_final', 'merchant_confirmed' ), true )
					&& '' !== (string) $cost_amount
					&& is_numeric( $cost_amount )
				) {
					if ( $currency !== $cost_currency ) {
						$comparison = __( 'Currency mismatch', 'wpessential-shipping-cost-monitor' );
					} elseif ( 'exclusive' !== $tax_basis ) {
						$comparison = __( 'Tax basis not comparable', 'wpessential-shipping-cost-monitor' );
					} else {
						$variance = wc_format_decimal(
							(float) $order->get_shipping_total() - (float) $cost_amount,
							wc_get_price_decimals()
						);
						$comparison = __( 'Comparable', 'wpessential-shipping-cost-monitor' );
					}
				} elseif ( '' !== $evidence && ! in_array( $evidence, array( 'invoice_final', 'merchant_confirmed' ), true ) ) {
					$comparison = __( 'Evidence not confirmed', 'wpessential-shipping-cost-monitor' );
				}

				$created = $order->get_date_created();
				$row = array(
					$order->get_id(),
					self::safe_text( $order->get_order_number() ),
					$created ? wp_date( 'Y-m-d H:i:s', $created->getTimestamp() ) : '',
					self::safe_text( $order->get_status() ),
					self::safe_text( $currency ),
					wc_format_decimal( $order->get_shipping_total(), wc_get_price_decimals() ),
					is_numeric( $cost_amount ) ? wc_format_decimal( $cost_amount, wc_get_price_decimals() ) : '',
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
