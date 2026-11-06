( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.element || ! wp.i18n ) {
		return;
	}

	const h = wp.element.createElement;
	const __ = wp.i18n.__;
	const report = window.wpeScmDashboard || {};
	const groups = Array.isArray( report.groups ) ? report.groups : [];

	function moneyByCurrency( amountKey, countKey ) {
		return groups
			.filter( function ( group ) {
				return Number( group[ countKey ] ) > 0;
			} )
			.map( function ( group ) {
				return group.currency + ' ' + group[ amountKey ];
			} )
			.join( ' · ' ) || '—';
	}

	function MetricCard( props ) {
		return h(
			'section',
			{ className: 'wpe-scm-card wpe-scm-metric-card' },
			h( 'span', { className: 'wpe-scm-card-label' }, props.label ),
			h( 'strong', { className: 'wpe-scm-card-value' }, props.value ),
			h( 'span', { className: 'wpe-scm-card-note' }, props.note )
		);
	}

	function App() {
		const eligible = Number( report.eligible_orders || 0 );
		const covered = Number( report.confirmed_cost_orders || 0 );
		const coverage = Number( report.coverage_percent || 0 );
		const period = report.period || {};
		const mismatchCount = Number( report.currency_mismatch_orders || 0 );
		const confirmedValue = moneyByCurrency( 'confirmed_cost', 'confirmed_orders' );
		const hasOrders = eligible > 0;
		const periodLabel = period.start && period.end ? period.start + ' — ' + period.end : __( 'Last 30 days', 'wpessential-shipping-cost-monitor' );

		return h(
			'main',
			{ className: 'wpe-scm-app' },
			h(
				'header',
				{ className: 'wpe-scm-header' },
				h(
					'div',
					null,
					h( 'p', { className: 'wpe-scm-eyebrow' }, __( 'SHIPPING INTELLIGENCE', 'wpessential-shipping-cost-monitor' ) ),
					h( 'h1', null, __( 'Shipping Cost Monitor', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', { className: 'wpe-scm-intro' }, __( 'Understand what customers paid for delivery and what your store paid to fulfil it.', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', { className: 'wpe-scm-period' }, __( 'Order period:', 'wpessential-shipping-cost-monitor' ) + ' ' + periodLabel )
				),
				h(
					'a',
					{
						className: 'button button-primary wpe-scm-primary-action',
						href: 'admin.php?page=wc-orders',
					},
					__( 'View WooCommerce orders', 'wpessential-shipping-cost-monitor' )
				)
			),
			h(
				'section',
				{ className: 'wpe-scm-metrics', 'aria-label': __( 'Shipping cost summary', 'wpessential-shipping-cost-monitor' ) },
				h( MetricCard, {
					label: __( 'Shipping charged', 'wpessential-shipping-cost-monitor' ),
					value: moneyByCurrency( 'shipping_charged', 'orders' ),
					note: __( 'Shipping line totals, before shipping tax', 'wpessential-shipping-cost-monitor' ),
				} ),
				h( MetricCard, {
					label: __( 'Confirmed cost', 'wpessential-shipping-cost-monitor' ),
					value: confirmedValue,
					note: __( 'Invoice-final or merchant-confirmed; matching currency only', 'wpessential-shipping-cost-monitor' ),
				} ),
				h( MetricCard, {
					label: __( 'Shipping variance', 'wpessential-shipping-cost-monitor' ),
					value: __( 'Not available', 'wpessential-shipping-cost-monitor' ),
					note: __( 'Cost tax treatment is not recorded yet', 'wpessential-shipping-cost-monitor' ),
				} ),
				h( MetricCard, {
					label: __( 'Cost coverage', 'wpessential-shipping-cost-monitor' ),
					value: hasOrders ? coverage + '%' : '—',
					note: hasOrders ? covered + ' / ' + eligible + ' ' + __( 'eligible orders', 'wpessential-shipping-cost-monitor' ) : __( 'No eligible orders in this period', 'wpessential-shipping-cost-monitor' ),
				} )
			),
			mismatchCount > 0 ? h(
				'p',
				{ className: 'wpe-scm-report-notice', role: 'status' },
				mismatchCount + ' ' + __( 'confirmed cost record(s) use a different currency and are excluded from the confirmed-cost total and coverage.', 'wpessential-shipping-cost-monitor' )
			) : null,
			h(
				'section',
				{ className: 'wpe-scm-card wpe-scm-empty-state', 'aria-live': 'polite' },
				h( 'div', { className: 'wpe-scm-empty-icon', 'aria-hidden': 'true' }, hasOrders ? '✓' : '↗' ),
				h( 'div', null,
					h( 'h2', null, hasOrders ? __( 'Currency-separated order summary', 'wpessential-shipping-cost-monitor' ) : __( 'No eligible shipping orders found', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', null, hasOrders ? __( 'This first report includes Processing and Completed orders with shipping lines. Shipping refunds are not adjusted yet; variance remains hidden until cost tax treatment is recorded.', 'wpessential-shipping-cost-monitor' ) : __( 'Eligible orders are non-virtual Processing or Completed orders with a shipping line, created during the displayed period.', 'wpessential-shipping-cost-monitor' ) )
				)
			),
			h(
				'div',
				{ className: 'wpe-scm-lower-grid' },
				h(
					'section',
					{ className: 'wpe-scm-card wpe-scm-next-card' },
					h( 'p', { className: 'wpe-scm-eyebrow' }, __( 'NEXT: REPORT CONTROLS', 'wpessential-shipping-cost-monitor' ) ),
					h( 'h2', null, __( 'Reporting scope', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', null, __( 'The initial summary separates currencies and counts only invoice-final or merchant-confirmed costs. Date filters, shipping refund allocation and tax-basis comparison remain to be implemented.', 'wpessential-shipping-cost-monitor' ) ),
					h( 'span', { className: 'wpe-scm-status-pill' }, __( 'Initial summary', 'wpessential-shipping-cost-monitor' ) )
				),
				h(
					'aside',
					{ className: 'wpe-scm-card wpe-scm-principle-card' },
					h( 'span', { className: 'wpe-scm-principle-mark', 'aria-hidden': 'true' }, '“' ),
					h( 'h2', null, __( 'Evidence before estimates', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', null, __( 'A quote is an estimate. A booked label is provisional. Only invoice-final or merchant-confirmed costs count as confirmed.', 'wpessential-shipping-cost-monitor' ) )
				)
			)
		);
	}

	function mount() {
		const root = document.getElementById( 'wpe-scm-root' );
		if ( ! root ) {
			return;
		}

		if ( wp.element.createRoot ) {
			wp.element.createRoot( root ).render( h( App ) );
		} else if ( wp.element.render ) {
			wp.element.render( h( App ), root );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', mount );
	} else {
		mount();
	}
} )( window.wp );
