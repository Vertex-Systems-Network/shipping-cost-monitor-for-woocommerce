<?php
/**
 * Read-only, currency-separated dashboard summary.
 *
 * @package WPEssentialShippingCostMonitor
 */

namespace WPEssential\ShippingCostMonitor\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Builds bounded, paginated reports through WooCommerce order queries.
 */
final class Summary {
	/**
	 * Build the report snapshot for a selected date range.
	 *
	 * @param string|null $requested_start Requested inclusive start date.
	 * @param string|null $requested_end   Requested inclusive end date.
	 * @return array<string,mixed>
	 */
	public static function build( ?string $requested_start = null, ?string $requested_end = null ): array {
		$period = self::normalize_period( $requested_start, $requested_end );
		$page = 1;
		$max_pages = 1;
		$groups = array();
		$orders = 0;
		$covered = 0;
		$mismatched_currency = 0;
		$comparable_orders = 0;

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

				$order_currency = (string) $order->get_currency();
				if ( '' === $order_currency ) {
					continue;
				}

				if ( ! isset( $groups[ $order_currency ] ) ) {
					$groups[ $order_currency ] = array(
						'currency'                => $order_currency,
						'orders'                  => 0,
						'charged_minor'           => 0,
						'confirmed_minor'         => 0,
						'confirmed_orders'        => 0,
						'currency_mismatch'       => 0,
						'comparable_charged_minor'=> 0,
						'comparable_cost_minor'   => 0,
						'variance_minor'          => 0,
						'comparable_orders'       => 0,
					);
				}

				++$orders;
				++$groups[ $order_currency ]['orders'];
				$shipping_refunded = min( (float) $order->get_shipping_total(), (float) $order->get_total_shipping_refunded() );
				$shipping_minor = wc_add_number_precision( (float) $order->get_shipping_total() - $shipping_refunded );
				$groups[ $order_currency ]['charged_minor'] += $shipping_minor;

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

				$cost_minor = wc_add_number_precision( (float) $cost_amount );
				$groups[ $order_currency ]['confirmed_minor'] += $cost_minor;
				++$groups[ $order_currency ]['confirmed_orders'];
				++$covered;

				$tax_basis = (string) $order->get_meta( '_wpe_scm_cost_tax_basis', true );
				if ( 'exclusive' !== $tax_basis ) {
					continue;
				}

				$groups[ $order_currency ]['comparable_charged_minor'] += $shipping_minor;
				$groups[ $order_currency ]['comparable_cost_minor'] += $cost_minor;
				$groups[ $order_currency ]['variance_minor'] += $shipping_minor - $cost_minor;
				++$groups[ $order_currency ]['comparable_orders'];
				++$comparable_orders;
			}

			++$page;
		} while ( $page <= $max_pages );

		$precision = wc_get_price_decimals();
		foreach ( $groups as &$group ) {
			$group['shipping_charged'] = wc_format_decimal( wc_remove_number_precision( $group['charged_minor'] ), $precision );
			$group['confirmed_cost']   = wc_format_decimal( wc_remove_number_precision( $group['confirmed_minor'] ), $precision );
			$group['comparable_shipping_charged'] = wc_format_decimal( wc_remove_number_precision( $group['comparable_charged_minor'] ), $precision );
			$group['comparable_confirmed_cost']   = wc_format_decimal( wc_remove_number_precision( $group['comparable_cost_minor'] ), $precision );
			$group['variance'] = wc_format_decimal( wc_remove_number_precision( $group['variance_minor'] ), $precision );
			unset(
				$group['charged_minor'],
				$group['confirmed_minor'],
				$group['comparable_charged_minor'],
				$group['comparable_cost_minor'],
				$group['variance_minor']
			);
		}
		unset( $group );

		ksort( $groups );

		return array(
			'period' => array(
				'start' => $period['start'],
				'end'   => $period['end'],
				'days'  => $period['days'],
				'timezone' => wp_timezone_string(),
			),
			'period_reset' => $period['reset'],
			'eligible_orders' => $orders,
			'confirmed_cost_orders' => $covered,
			'coverage_percent' => $orders > 0 ? round( ( $covered / $orders ) * 100, 1 ) : 0,
			'currency_mismatch_orders' => $mismatched_currency,
			'comparable_orders' => $comparable_orders,
			'groups' => array_values( $groups ),
			'variance_available' => $comparable_orders > 0,
			'refunds_adjusted' => false,
		);
	}

	/**
	 * Normalize a valid report range; otherwise return the default 30-day window.
	 *
	 * @param string|null $requested_start Requested inclusive start date.
	 * @param string|null $requested_end   Requested inclusive end date.
	 * @return array{start:string,end:string,days:int,reset:bool}
	 */
	public static function normalize_period( ?string $requested_start = null, ?string $requested_end = null ): array {
		$valid_start = self::valid_date( $requested_start );
		$valid_end   = self::valid_date( $requested_end );

		if ( $valid_start && $valid_end && $requested_start <= $requested_end ) {
			$start = new \DateTimeImmutable( $requested_start, wp_timezone() );
			$end   = new \DateTimeImmutable( $requested_end, wp_timezone() );
			$days  = (int) $start->diff( $end )->days + 1;

			if ( $days <= 366 ) {
				return array(
					'start' => $requested_start,
					'end'   => $requested_end,
					'days'  => $days,
					'reset' => false,
				);
			}
		}

		$now = current_datetime();
		return array(
			'start' => $now->modify( '-29 days' )->format( 'Y-m-d' ),
			'end'   => $now->format( 'Y-m-d' ),
			'days'  => 30,
			'reset' => null !== $requested_start || null !== $requested_end,
		);
	}

	/**
	 * Validate a calendar date without accepting parser normalization.
	 *
	 * @param string|null $value Date in YYYY-MM-DD form.
	 */
	private static function valid_date( ?string $value ): bool {
		if ( null === $value || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return false;
		}

		$date = \DateTimeImmutable::createFromFormat( '!Y-m-d', $value, wp_timezone() );
		return $date instanceof \DateTimeImmutable && $date->format( 'Y-m-d' ) === $value;
	}
}
