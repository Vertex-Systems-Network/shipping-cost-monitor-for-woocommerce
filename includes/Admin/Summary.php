<?php
/**
 * Read-only, currency-separated dashboard summary.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Builds a recent-order summary through WooCommerce order queries.
 */
final class Summary {
	/**
	 * Build a rolling 30-day dashboard snapshot.
	 *
	 * @return array<string,mixed>
	 */
	public static function build(): array {
		$now       = current_datetime();
		$start     = $now->modify( '-29 days' )->format( 'Y-m-d' );
		$end       = current_datetime()->format( 'Y-m-d' );
		$page      = 1;
		$max_pages = 1;
		$groups    = array();
		$orders    = 0;
		$covered   = 0;
		$mismatched_currency = 0;

		do {
			$result = wc_get_orders(
				array(
					'type'       => 'shop_order',
					'status'     => array( 'processing', 'completed' ),
					'date_created' => $start . '...' . $end,
					'limit'      => 100,
					'paged'      => $page,
					'paginate'   => true,
					'return'     => 'objects',
					'orderby'    => 'date',
					'order'      => 'DESC',
				)
			);

			if ( ! is_object( $result ) || empty( $result->orders ) ) {
				break;
			}

			$max_pages = max( 1, (int) $result->max_num_pages );

			foreach ( $result->orders as $order ) {
				if ( ! $order instanceof \WC_Order || $order->is_virtual() || empty( $order->get_items( 'shipping' ) ) ) {
					continue;
				}

				$order_currency = (string) $order->get_currency();
				if ( '' === $order_currency ) {
					continue;
				}

				if ( ! isset( $groups[ $order_currency ] ) ) {
					$groups[ $order_currency ] = array(
						'currency'          => $order_currency,
						'orders'            => 0,
						'charged_minor'     => 0,
						'confirmed_minor'   => 0,
						'confirmed_orders'  => 0,
						'currency_mismatch' => 0,
					);
				}

				++$orders;
				++$groups[ $order_currency ]['orders'];
				$groups[ $order_currency ]['charged_minor'] += (int) wc_add_number_precision( (float) $order->get_shipping_total() );

				$evidence = (string) $order->get_meta( '_wpe_scm_cost_evidence', true );
				if ( ! in_array( $evidence, array( 'invoice_final', 'merchant_confirmed' ), true ) ) {
					continue;
				}

				$cost_currency = (string) $order->get_meta( '_wpe_scm_cost_currency', true );
				$cost_currency = '' !== $cost_currency ? $cost_currency : $order_currency;
				$cost_amount   = $order->get_meta( '_wpe_scm_cost_amount', true );

				if ( '' === (string) $cost_amount || ! is_numeric( $cost_amount ) || (float) $cost_amount < 0 ) {
					continue;
				}

				if ( $cost_currency !== $order_currency ) {
					++$mismatched_currency;
					++$groups[ $order_currency ]['currency_mismatch'];
					continue;
				}

				$groups[ $order_currency ]['confirmed_minor'] += (int) wc_add_number_precision( (float) $cost_amount );
				++$groups[ $order_currency ]['confirmed_orders'];
				++$covered;
			}

			++$page;
		} while ( $page <= $max_pages );

		$precision = wc_get_price_decimals();
		foreach ( $groups as &$group ) {
			$group['shipping_charged'] = wc_format_decimal( wc_remove_number_precision( $group['charged_minor'] ), $precision );
			$group['confirmed_cost']   = wc_format_decimal( wc_remove_number_precision( $group['confirmed_minor'] ), $precision );
			unset( $group['charged_minor'], $group['confirmed_minor'] );
		}
		unset( $group );

		ksort( $groups );

		return array(
			'period'                    => array(
				'start' => $start,
				'end'   => $end,
				'days'  => 30,
			),
			'eligible_orders'           => $orders,
			'confirmed_cost_orders'     => $covered,
			'coverage_percent'          => $orders > 0 ? round( ( $covered / $orders ) * 100, 1 ) : 0,
			'currency_mismatch_orders'  => $mismatched_currency,
			'groups'                    => array_values( $groups ),
			'variance_available'        => false,
			'variance_unavailable_reason' => __( 'Shipping tax treatment is not recorded yet.', 'wpessential-shipping-cost-monitor' ),
			'refunds_adjusted'          => false,
		);
	}
}
