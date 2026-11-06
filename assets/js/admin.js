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


	function parseCsvRecords( text, delimiter ) {
		const rows = [];
		let row = [];
		let field = '';
		let inQuotes = false;

		for ( let index = 0; index < text.length; index++ ) {
			const character = text[ index ];

			if ( '"' === character ) {
				if ( inQuotes && '"' === text[ index + 1 ] ) {
					field += '"';
					index++;
				} else if ( inQuotes || '' === field ) {
					inQuotes = ! inQuotes;
				} else {
					throw new Error( __( 'A quote appears in an invalid position.', 'wpessential-shipping-cost-monitor' ) );
				}
				continue;
			}

			if ( ! inQuotes && character === delimiter ) {
				row.push( field );
				field = '';
				continue;
			}

			if ( ! inQuotes && ( '\n' === character || '\r' === character ) ) {
				if ( '\r' === character && '\n' === text[ index + 1 ] ) {
					index++;
				}
				row.push( field );
				rows.push( row );
				row = [];
				field = '';
				if ( rows.length > 5001 ) {
					throw new Error( __( 'This preview is limited to 5,000 data rows.', 'wpessential-shipping-cost-monitor' ) );
				}
				continue;
			}

			field += character;
		}

		if ( inQuotes ) {
			throw new Error( __( 'The CSV has an unclosed quoted field.', 'wpessential-shipping-cost-monitor' ) );
		}

		if ( '' !== field || row.length > 0 ) {
			row.push( field );
			rows.push( row );
		}

		if ( rows.length > 5001 ) {
			throw new Error( __( 'This preview is limited to 5,000 data rows.', 'wpessential-shipping-cost-monitor' ) );
		}

		return rows.filter( function ( values ) {
			return values.some( function ( value ) {
				return '' !== value.trim();
			} );
		} );
	}

	function parseInvoiceAmount( rawValue, decimalMark ) {
		let value = String( rawValue || '' ).trim().replace( /[\s\u00a0]/g, '' );
		if ( ',' === decimalMark ) {
			value = value.replace( /\./g, '' ).replace( ',', '.' );
		} else {
			value = value.replace( /,/g, '' );
		}

		if ( ! /^-?\d+(?:\.\d+)?$/.test( value ) ) {
			return null;
		}
		const amount = Number( value );
		return Number.isFinite( amount ) ? amount : null;
	}

	function CsvInvoicePreview() {
		const useState = wp.element.useState;
		const useEffect = wp.element.useEffect;
		const [ rawCsv, setRawCsv ] = useState( '' );
		const [ fileName, setFileName ] = useState( '' );
		const [ delimiter, setDelimiter ] = useState( ',' );
		const [ decimalMark, setDecimalMark ] = useState( '.' );
		const [ parsed, setParsed ] = useState( null );
		const [ mapping, setMapping ] = useState( { reference: '', amount: '', currency: '' } );
		const [ error, setError ] = useState( '' );
		const [ matchResults, setMatchResults ] = useState( {} );
		const [ matching, setMatching ] = useState( false );
		const [ matchError, setMatchError ] = useState( '' );

		useEffect( function () {
			if ( ! rawCsv ) {
				setParsed( null );
				return;
			}

			try {
				const text = rawCsv.replace( /^\uFEFF/, '' );
				const records = parseCsvRecords( text, delimiter );
				if ( records.length < 2 ) {
					throw new Error( __( 'Select a CSV with a header row and at least one data row.', 'wpessential-shipping-cost-monitor' ) );
				}

				const headers = records[ 0 ].map( function ( value ) {
					return value.trim();
				} );
				if ( headers.some( function ( value ) { return '' === value; } ) ) {
					throw new Error( __( 'Every CSV column needs a non-empty header.', 'wpessential-shipping-cost-monitor' ) );
				}

				const rows = records.slice( 1 ).map( function ( values, index ) {
					return { number: index + 2, values: values };
				} );
				const findHeader = function ( aliases ) {
					const index = headers.findIndex( function ( header ) {
						const normalized = header.toLowerCase().replace( /[^a-z0-9]/g, '' );
						return aliases.indexOf( normalized ) !== -1;
					} );
					return index >= 0 ? String( index ) : '';
				};

				setParsed( { headers: headers, rows: rows } );
				setMatchResults( {} );
				setMapping( {
					reference: findHeader( [ 'order', 'ordernumber', 'orderid', 'tracking', 'trackingnumber', 'awb', 'shipmentid', 'referencenumber' ] ),
					amount: findHeader( [ 'amount', 'charge', 'shippingcost', 'shippingcharge', 'cost', 'total' ] ),
					currency: findHeader( [ 'currency', 'currencycode', 'iso' ] ),
				} );
				setError( '' );
			} catch ( parseError ) {
				setParsed( null );
				setError( parseError.message || __( 'Could not read this CSV.', 'wpessential-shipping-cost-monitor' ) );
			}
		}, [ rawCsv, delimiter ] );

		function chooseFile( event ) {
			const file = event.target.files && event.target.files[ 0 ];
			if ( ! file ) {
				return;
			}
			setRawCsv( '' );
			setParsed( null );
			setMatchResults( {} );
			if ( file.size > 5242880 ) {
				setRawCsv( '' );
				setFileName( '' );
				setError( __( 'Files must be 5 MB or smaller.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}
			if ( ! /\.csv$/i.test( file.name ) ) {
				setRawCsv( '' );
				setFileName( '' );
				setError( __( 'Choose a .csv file.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}

			setFileName( file.name );
			setError( '' );
			const reader = new FileReader();
			reader.onload = function () {
				setRawCsv( String( reader.result || '' ) );
			};
			reader.onerror = function () {
				setError( __( 'The selected file could not be read.', 'wpessential-shipping-cost-monitor' ) );
			};
			reader.readAsText( file );
		}

		function clearPreview() {
			setRawCsv( '' );
			setFileName( '' );
			setParsed( null );
			setMapping( { reference: '', amount: '', currency: '' } );
			setError( '' );
		}

		const validRows = parsed && mapping.reference !== '' && mapping.amount !== '' && mapping.currency !== ''
			? parsed.rows.filter( function ( item ) {
				const reference = '' !== mapping.reference ? item.values[ Number( mapping.reference ) ] || '' : '';
				const amount = '' !== mapping.amount ? parseInvoiceAmount( item.values[ Number( mapping.amount ) ], decimalMark ) : null;
				const currency = '' !== mapping.currency ? String( item.values[ Number( mapping.currency ) ] || '' ).trim() : '';
				return '' !== reference.trim() && null !== amount && /^[A-Za-z]{3}$/.test( currency );
			} )
			: [];


		function matchReferences() {
			if ( ! report.ajax_url || ! report.match_nonce || ! parsed ) {
				setMatchError( __( 'Matching is unavailable. Reload the dashboard and try again.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}

			const rowsToMatch = validRows.slice( 0, 50 );
			const body = new FormData();
			body.append( 'action', 'wpe_scm_match_preview' );
			body.append( 'nonce', report.match_nonce );
			rowsToMatch.forEach( function ( item, index ) {
				body.append( 'references[' + index + '][row]', String( item.number ) );
				body.append( 'references[' + index + '][reference]', String( item.values[ Number( mapping.reference ) ] || '' ) );
			} );

			setMatching( true );
			setMatchError( '' );
			fetch( report.ajax_url, { method: 'POST', credentials: 'same-origin', body: body } )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( payload ) {
					if ( ! payload || ! payload.success || ! payload.data || ! Array.isArray( payload.data.results ) ) {
						throw new Error( payload && payload.data && payload.data.message ? payload.data.message : __( 'The matching request failed.', 'wpessential-shipping-cost-monitor' ) );
					}

					const resultMap = {};
					payload.data.results.forEach( function ( result ) {
						resultMap[ result.row ] = result;
					} );
					setMatchResults( resultMap );
				} )
				.catch( function ( requestError ) {
					setMatchError( requestError.message || __( 'The matching request failed.', 'wpessential-shipping-cost-monitor' ) );
				} )
				.finally( function () {
					setMatching( false );
				} );
		}

		const fieldSelect = function ( label, field ) {
			return h(
				'label',
				{ className: 'wpe-scm-import-field' },
				label,
				h(
					'select',
					{
						value: mapping[ field ],
						onChange: function ( event ) {
							setMapping( Object.assign( {}, mapping, { [ field ]: event.target.value } ) );
							setMatchResults( {} );
						},
					},
					h( 'option', { value: '' }, __( 'Choose a column', 'wpessential-shipping-cost-monitor' ) ),
					parsed ? parsed.headers.map( function ( header, index ) {
						return h( 'option', { key: index, value: String( index ) }, header );
					} ) : null
				)
			);
		};

		return h(
			'section',
			{ className: 'wpe-scm-card wpe-scm-import-card' },
			h( 'p', { className: 'wpe-scm-eyebrow' }, __( 'INVOICE CSV PREVIEW', 'wpessential-shipping-cost-monitor' ) ),
			h( 'h2', null, __( 'Preview a courier invoice CSV', 'wpessential-shipping-cost-monitor' ) ),
			h( 'p', { className: 'wpe-scm-import-privacy' }, __( 'Preview-only: the file stays in this browser. If you request exact matching, only mapped references are sent to WordPress; no file or cost rows are saved and no order costs change.', 'wpessential-shipping-cost-monitor' ) ),
			h(
				'div',
				{ className: 'wpe-scm-import-toolbar' },
				h( 'label', { className: 'wpe-scm-file-field' }, __( 'Choose CSV file (5 MB max)', 'wpessential-shipping-cost-monitor' ), h( 'input', { type: 'file', accept: '.csv,text/csv', onChange: chooseFile } ) ),
				h(
					'label',
					{ className: 'wpe-scm-import-field' },
					__( 'Column separator', 'wpessential-shipping-cost-monitor' ),
					h( 'select', { value: delimiter, onChange: function ( event ) { setDelimiter( event.target.value ); } },
						h( 'option', { value: ',' }, __( 'Comma', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: ';' }, __( 'Semicolon', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: '\t' }, __( 'Tab', 'wpessential-shipping-cost-monitor' ) )
					)
				),
				h(
					'label',
					{ className: 'wpe-scm-import-field' },
					__( 'Decimal mark', 'wpessential-shipping-cost-monitor' ),
					h( 'select', { value: decimalMark, onChange: function ( event ) { setDecimalMark( event.target.value ); } },
						h( 'option', { value: '.' }, __( 'Period (1,234.56)', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: ',' }, __( 'Comma (1.234,56)', 'wpessential-shipping-cost-monitor' ) )
					)
				)
			),
			fileName ? h( 'p', { className: 'wpe-scm-import-file' }, __( 'Selected:', 'wpessential-shipping-cost-monitor' ) + ' ' + fileName ) : null,
			error ? h( 'p', { className: 'wpe-scm-report-notice', role: 'alert' }, error ) : null,
			parsed ? h(
				'div',
				{ className: 'wpe-scm-import-preview' },
				h( 'div', { className: 'wpe-scm-import-mapping' },
					fieldSelect( __( 'Order / shipment reference', 'wpessential-shipping-cost-monitor' ), 'reference' ),
					fieldSelect( __( 'Charge amount', 'wpessential-shipping-cost-monitor' ), 'amount' ),
					fieldSelect( __( 'Currency code', 'wpessential-shipping-cost-monitor' ), 'currency' )
				),
				h( 'p', { className: 'wpe-scm-import-count' }, validRows.length + ' / ' + parsed.rows.length + ' ' + __( 'rows pass basic field checks. Exact order-number/ID matching is available for the first 50 valid rows; committing remains disabled.', 'wpessential-shipping-cost-monitor' ) ),
				matchError ? h( 'p', { className: 'wpe-scm-report-notice', role: 'alert' }, matchError ) : null,
				h(
					'div',
					{ className: 'wpe-scm-import-table-wrap' },
					h( 'table', { className: 'widefat striped wpe-scm-import-table' },
						h( 'thead', null, h( 'tr', null,
							h( 'th', null, __( 'Source row', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Reference', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Amount', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Currency', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Validation', 'wpessential-shipping-cost-monitor' ) )
						) ),
						h( 'tbody', null, parsed.rows.slice( 0, 10 ).map( function ( item ) {
							const reference = '' !== mapping.reference ? item.values[ Number( mapping.reference ) ] || '' : '';
							const amountRaw = '' !== mapping.amount ? item.values[ Number( mapping.amount ) ] || '' : '';
							const currency = '' !== mapping.currency ? String( item.values[ Number( mapping.currency ) ] || '' ).trim() : '';
							const valid = '' !== reference.trim() && null !== parseInvoiceAmount( amountRaw, decimalMark ) && /^[A-Za-z]{3}$/.test( currency );
							const match = matchResults[ item.number ];
							const rowStatus = match ? match.status + ( match.order_number ? ': #' + match.order_number + ' (' + match.order_status + ')' : '' ) : valid ? __( 'Ready to match', 'wpessential-shipping-cost-monitor' ) : __( 'Missing or invalid required value', 'wpessential-shipping-cost-monitor' );
							return h( 'tr', { key: item.number },
								h( 'td', null, item.number ),
								h( 'td', null, reference ),
								h( 'td', null, amountRaw ),
								h( 'td', null, currency ),
								h( 'td', null, rowStatus )
							);
						} ) )
					)
				),
				h( 'div', { className: 'wpe-scm-import-actions' },
					h( 'button', { type: 'button', className: 'button button-primary', onClick: matchReferences, disabled: matching || validRows.length < 1 }, matching ? __( 'Matching…', 'wpessential-shipping-cost-monitor' ) : __( 'Match first 50 valid rows', 'wpessential-shipping-cost-monitor' ) ),
					h( 'button', { type: 'button', className: 'button', onClick: clearPreview }, __( 'Clear preview', 'wpessential-shipping-cost-monitor' ) )
				)
			) : null
		);
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
		const varianceValue = report.variance_available ? moneyByCurrency( 'variance', 'comparable_orders' ) : __( 'Not available', 'wpessential-shipping-cost-monitor' );
		const hasOrders = eligible > 0;
		const periodLabel = period.start && period.end ? period.start + ' — ' + period.end + ' (' + ( period.timezone || 'UTC' ) + ')' : __( 'Last 30 days', 'wpessential-shipping-cost-monitor' );

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
				'div',
				{ className: 'wpe-scm-report-toolbar' },
				h(
					'form',
					{ className: 'wpe-scm-report-controls', method: 'get' },
					h( 'input', { type: 'hidden', name: 'page', value: 'wpessential-shipping-cost-monitor' } ),
					h( 'label', null, __( 'From', 'wpessential-shipping-cost-monitor' ), h( 'input', { type: 'date', name: 'wpe_scm_start', value: period.start || '' } ) ),
					h( 'label', null, __( 'To', 'wpessential-shipping-cost-monitor' ), h( 'input', { type: 'date', name: 'wpe_scm_end', value: period.end || '' } ) ),
					h( 'button', { type: 'submit', className: 'button button-primary' }, __( 'Update report', 'wpessential-shipping-cost-monitor' ) )
				),
				report.export_url ? h( 'a', { className: 'button wpe-scm-export-action', href: report.export_url }, __( 'Export CSV', 'wpessential-shipping-cost-monitor' ) ) : null
			),
			report.period_reset ? h( 'p', { className: 'wpe-scm-report-notice', role: 'status' }, __( 'The selected dates were invalid or exceeded 366 days. Showing the default 30-day period.', 'wpessential-shipping-cost-monitor' ) ) : null,
			h( CsvInvoicePreview ),
			h(
				'section',
				{ className: 'wpe-scm-metrics', 'aria-label': __( 'Shipping cost summary', 'wpessential-shipping-cost-monitor' ) },
				h( MetricCard, {
					label: __( 'Shipping charged net', 'wpessential-shipping-cost-monitor' ),
					value: moneyByCurrency( 'shipping_charged', 'orders' ),
					note: __( 'After allocated shipping refunds; before shipping tax', 'wpessential-shipping-cost-monitor' ),
				} ),
				h( MetricCard, {
					label: __( 'Confirmed cost', 'wpessential-shipping-cost-monitor' ),
					value: confirmedValue,
					note: __( 'Invoice-final or merchant-confirmed; matching currency only', 'wpessential-shipping-cost-monitor' ),
				} ),
				h( MetricCard, {
					label: __( 'Shipping variance', 'wpessential-shipping-cost-monitor' ),
					value: varianceValue,
					note: report.variance_available ? report.comparable_orders + ' ' + __( 'orders with matching currency and tax-exclusive cost', 'wpessential-shipping-cost-monitor' ) : __( 'No comparable confirmed costs in this period', 'wpessential-shipping-cost-monitor' ),
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
					h( 'p', null, hasOrders ? __( 'Variance uses net shipping after allocated refunds and only currency-matched, tax-exclusive confirmed costs.', 'wpessential-shipping-cost-monitor' ) : __( 'Eligible orders are Processing or Completed orders with a WooCommerce shipping line, created during the displayed period.', 'wpessential-shipping-cost-monitor' ) )
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
					h( 'p', null, __( 'Choose a date range of up to 366 days. CSV export includes refund allocation; custom statuses and multi-currency conversion are not supported.', 'wpessential-shipping-cost-monitor' ) ),
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
