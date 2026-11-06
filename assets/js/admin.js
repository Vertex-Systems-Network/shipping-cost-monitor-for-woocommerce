( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.element || ! wp.i18n ) {
		return;
	}

	const h = wp.element.createElement;
	const __ = wp.i18n.__;

	function MetricCard( props ) {
		return h(
			'section',
			{ className: 'wpe-scm-card wpe-scm-metric-card' },
			h( 'span', { className: 'wpe-scm-card-label' }, props.label ),
			h( 'strong', { className: 'wpe-scm-card-value' }, '—' ),
			h( 'span', { className: 'wpe-scm-card-note' }, props.note )
		);
	}

	function App() {
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
					h( 'p', { className: 'wpe-scm-intro' }, __( 'Understand what customers paid for delivery and what your store paid to fulfil it.', 'wpessential-shipping-cost-monitor' ) )
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
				'div',
				{ className: 'wpe-scm-metrics', 'aria-label': __( 'Shipping cost summary', 'wpessential-shipping-cost-monitor' ) },
				h( MetricCard, { label: __( 'Shipping charged', 'wpessential-shipping-cost-monitor' ), note: __( 'Customer-paid shipping', 'wpessential-shipping-cost-monitor' ) } ),
				h( MetricCard, { label: __( 'Confirmed cost', 'wpessential-shipping-cost-monitor' ), note: __( 'Invoice-final or merchant-confirmed', 'wpessential-shipping-cost-monitor' ) } ),
				h( MetricCard, { label: __( 'Shipping variance', 'wpessential-shipping-cost-monitor' ), note: __( 'Compared only when currency and basis match', 'wpessential-shipping-cost-monitor' ) } ),
				h( MetricCard, { label: __( 'Cost coverage', 'wpessential-shipping-cost-monitor' ), note: __( 'Eligible orders with confirmed cost', 'wpessential-shipping-cost-monitor' ) } )
			),
			h(
				'section',
				{ className: 'wpe-scm-card wpe-scm-empty-state', 'aria-live': 'polite' },
				h( 'div', { className: 'wpe-scm-empty-icon', 'aria-hidden': 'true' }, '↗' ),
				h( 'div', null,
					h( 'h2', null, __( 'Your shipping picture will appear here', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', null, __( 'The dashboard is ready. Order cost entry and verified reporting are the next implementation steps; no sample figures are shown.', 'wpessential-shipping-cost-monitor' ) )
				)
			),
			h(
				'div',
				{ className: 'wpe-scm-lower-grid' },
				h(
					'section',
					{ className: 'wpe-scm-card wpe-scm-next-card' },
					h( 'p', { className: 'wpe-scm-eyebrow' }, __( 'NEXT IN THE BUILD', 'wpessential-shipping-cost-monitor' ) ),
					h( 'h2', null, __( 'Start with reliable cost evidence', 'wpessential-shipping-cost-monitor' ) ),
					h( 'p', null, __( 'The first workflow will record a shipping cost against an order, keep its evidence type visible and preserve an audit trail.', 'wpessential-shipping-cost-monitor' ) ),
					h( 'span', { className: 'wpe-scm-status-pill' }, __( 'In progress', 'wpessential-shipping-cost-monitor' ) )
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
		if ( root && wp.element.render ) {
			wp.element.render( h( App ), root );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', mount );
	} else {
		mount();
	}
} )( window.wp );
