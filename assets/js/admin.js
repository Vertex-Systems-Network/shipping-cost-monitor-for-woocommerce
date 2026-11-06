( function ( wp ) {
	'use strict';

	if ( ! wp || ! wp.element || ! wp.i18n ) {
		return;
	}

	const h = wp.element.createElement;
	const __ = wp.i18n.__;
	const report = window.wpeScmDashboard || {};
	const carrierAdapters = Array.isArray( report.carrier_adapters ) ? report.carrier_adapters : [];
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

	function normalizeOrderReference( value ) {
		return String( value || '' ).trim().replace( /^#\s*/, '' );
	}

	function parseInvoiceAmount( rawValue, decimalMark ) {
		let value = String( rawValue || '' ).trim().replace( /[\s\u00a0]/g, '' );
		if ( ',' === decimalMark ) {
			value = value.replace( /\./g, '' ).replace( ',', '.' );
		} else {
			value = value.replace( /,/g, '' );
		}

		return /^-?\d+(?:\.\d+)?$/.test( value ) ? value : null;
	}

	const mappingProfileStorageKey = 'wpeScmCsvMappingProfilesV1';

	function readMappingProfiles() {
		try {
			const stored = window.localStorage.getItem( mappingProfileStorageKey );
			const profiles = stored ? JSON.parse( stored ) : [];
			return Array.isArray( profiles ) ? profiles.filter( function ( profile ) {
				return profile &&
					'string' === typeof profile.id &&
					'string' === typeof profile.name &&
					profile.name.length <= 60 &&
					[ 'generic', 'woocommerce_shipping_labels' ].indexOf( profile.sourceFormat ) !== -1 &&
					Array.isArray( profile.headers ) &&
					profile.headers.length <= 500 &&
					profile.headers.every( function ( header ) { return 'string' === typeof header && header.length <= 200; } ) &&
					profile.mapping &&
					'string' === typeof profile.mapping.reference &&
					'string' === typeof profile.mapping.amount &&
					'string' === typeof profile.mapping.currency &&
					[ ',', ';', '\t' ].indexOf( profile.delimiter ) !== -1 &&
					[ '.', ',' ].indexOf( profile.decimalMark ) !== -1 &&
					[ 'invoice_final', 'booked_label', 'merchant_confirmed' ].indexOf( profile.evidence ) !== -1 &&
					[ 'unknown', 'exclusive', 'inclusive' ].indexOf( profile.taxBasis ) !== -1;
			} ).slice( 0, 20 ) : [];
		} catch ( error ) {
			return [];
		}
	}

	function CsvInvoicePreview() {
		const useState = wp.element.useState;
		const useEffect = wp.element.useEffect;
		const useRef = wp.element.useRef;
		const [ rawCsv, setRawCsv ] = useState( '' );
		const [ fileName, setFileName ] = useState( '' );
		const [ delimiter, setDelimiter ] = useState( ',' );
		const [ decimalMark, setDecimalMark ] = useState( '.' );
		const [ parsed, setParsed ] = useState( null );
		const [ mapping, setMapping ] = useState( { reference: '', amount: '', currency: '' } );
		const [ evidence, setEvidence ] = useState( 'invoice_final' );
		const [ taxBasis, setTaxBasis ] = useState( 'unknown' );
		const [ error, setError ] = useState( '' );
		const [ matchResults, setMatchResults ] = useState( {} );
		const [ matching, setMatching ] = useState( false );
		const [ matchError, setMatchError ] = useState( '' );
		const [ commitResults, setCommitResults ] = useState( {} );
		const [ committing, setCommitting ] = useState( false );
		const [ commitError, setCommitError ] = useState( '' );
		const [ rowFilter, setRowFilter ] = useState( 'all' );
		const [ sourceFormat, setSourceFormat ] = useState( 'generic' );
		const [ mappingProfiles, setMappingProfiles ] = useState( readMappingProfiles );
		const [ selectedProfileId, setSelectedProfileId ] = useState( '' );
		const [ profileNotice, setProfileNotice ] = useState( '' );
		const applyingProfileRef = useRef( false );

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
				setCommitResults( {} );
				if ( applyingProfileRef.current ) {
					applyingProfileRef.current = false;
				} else {
					const isWooCommerceShipping = 'woocommerce_shipping_labels' === sourceFormat;
					setMapping( {
						reference: findHeader( isWooCommerceShipping ? [ 'order', 'ordernumber', 'orderid', 'ordername' ] : [ 'order', 'ordernumber', 'orderid' ] ),
						amount: findHeader( isWooCommerceShipping ? [ 'price', 'labelprice', 'labelcost', 'amount', 'cost' ] : [ 'amount', 'charge', 'shippingcost', 'shippingcharge', 'cost', 'total', 'price', 'labelprice', 'labelcost' ] ),
						currency: isWooCommerceShipping ? '__order_currency__' : findHeader( [ 'currency', 'currencycode', 'iso' ] ),
					} );
					if ( isWooCommerceShipping ) {
						setEvidence( 'booked_label' );
						setTaxBasis( 'unknown' );
					}
				}
				setError( '' );
			} catch ( parseError ) {
				setParsed( null );
				setError( parseError.message || __( 'Could not read this CSV.', 'wpessential-shipping-cost-monitor' ) );
			}
		}, [ rawCsv, delimiter, sourceFormat ] );

		function saveMappingProfile() {
			if ( ! parsed || ! mapping.reference || ! mapping.amount ) {
				setProfileNotice( __( 'Choose a file and map its order reference and amount columns first.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}

			const name = window.prompt( __( 'Name this CSV mapping profile', 'wpessential-shipping-cost-monitor' ) );
			if ( ! name ) {
				return;
			}
			const cleanName = name.trim().slice( 0, 60 );
			if ( ! cleanName ) {
				return;
			}
			const profile = {
				id: String( Date.now() ),
				name: cleanName,
				sourceFormat: sourceFormat,
				headers: parsed.headers,
				mapping: mapping,
				delimiter: delimiter,
				decimalMark: decimalMark,
				evidence: evidence,
				taxBasis: taxBasis,
			};
			const nextProfiles = mappingProfiles.filter( function ( item ) {
				return item.name.toLowerCase() !== cleanName.toLowerCase();
			} ).concat( [ profile ] ).slice( -20 );
			try {
				window.localStorage.setItem( mappingProfileStorageKey, JSON.stringify( nextProfiles ) );
				setMappingProfiles( nextProfiles );
				setSelectedProfileId( profile.id );
				setProfileNotice( __( 'Mapping profile saved in this browser. It contains column names and import settings only.', 'wpessential-shipping-cost-monitor' ) );
			} catch ( error ) {
				setProfileNotice( __( 'Browser storage is unavailable, so the mapping profile was not saved.', 'wpessential-shipping-cost-monitor' ) );
			}
		}

		function applyMappingProfile() {
			const profile = mappingProfiles.find( function ( item ) { return item.id === selectedProfileId; } );
			if ( ! profile || ! parsed ) {
				return;
			}
			if ( profile.sourceFormat !== sourceFormat || JSON.stringify( profile.headers ) !== JSON.stringify( parsed.headers ) ) {
				setProfileNotice( __( 'This profile belongs to a different source format or header layout.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}
			setMapping( profile.mapping );
			if ( profile.delimiter !== delimiter ) {
				applyingProfileRef.current = true;
				setDelimiter( profile.delimiter );
			}
			setDecimalMark( profile.decimalMark );
			setEvidence( profile.evidence );
			setTaxBasis( profile.taxBasis );
			setMatchResults( {} );
			setCommitResults( {} );
			setProfileNotice( __( 'Mapping profile applied to the current preview.', 'wpessential-shipping-cost-monitor' ) );
		}

		function deleteMappingProfile() {
			const nextProfiles = mappingProfiles.filter( function ( item ) { return item.id !== selectedProfileId; } );
			try {
				window.localStorage.setItem( mappingProfileStorageKey, JSON.stringify( nextProfiles ) );
				setMappingProfiles( nextProfiles );
				setSelectedProfileId( '' );
				setProfileNotice( __( 'Mapping profile deleted from this browser.', 'wpessential-shipping-cost-monitor' ) );
			} catch ( error ) {
				setProfileNotice( __( 'Browser storage is unavailable, so the mapping profile could not be deleted.', 'wpessential-shipping-cost-monitor' ) );
			}
		}

		function chooseFile( event ) {
			const file = event.target.files && event.target.files[ 0 ];
			if ( ! file ) {
				return;
			}

			setRawCsv( '' );
			setParsed( null );
			setMatchResults( {} );
			setCommitResults( {} );
			if ( file.size > 5242880 ) {
				setFileName( '' );
				setError( __( 'Files must be 5 MB or smaller.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}
			if ( ! /\.csv$/i.test( file.name ) ) {
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

		function selectSourceFormat( event ) {
			const format = event.target.value;
			setSourceFormat( format );
			if ( 'woocommerce_shipping_labels' !== format ) {
				return;
			}

			const headers = parsed ? parsed.headers : [];
			const findColumn = function ( aliases ) {
				const index = headers.findIndex( function ( header ) {
					return aliases.indexOf( header.toLowerCase().replace( /[^a-z0-9]/g, '' ) ) !== -1;
				} );
				return index >= 0 ? String( index ) : '';
			};
			setMapping( {
				reference: findColumn( [ 'order', 'ordernumber', 'orderid', 'ordername' ] ),
				amount: findColumn( [ 'price', 'labelprice', 'labelcost', 'amount', 'cost' ] ),
				currency: '__order_currency__',
			} );
			setEvidence( 'booked_label' );
			setTaxBasis( 'unknown' );
			setDecimalMark( '.' );
			setMatchResults( {} );
			setCommitResults( {} );
			setMatchError( '' );
			setCommitError( '' );
		}

		function clearPreview() {
			setSourceFormat( 'generic' );
			setRawCsv( '' );
			setFileName( '' );
			setParsed( null );
			setMapping( { reference: '', amount: '', currency: '' } );
			setMatchResults( {} );
			setCommitResults( {} );
			setError( '' );
			setMatchError( '' );
			setCommitError( '' );
		}

		function hasRefundStatus( item ) {
			if ( ! parsed ) {
				return false;
			}
			const refundIndex = parsed.headers.findIndex( function ( header ) {
				const normalized = header.toLowerCase().replace( /[^a-z0-9]/g, '' );
				return [ 'refundstatus', 'refundstate', 'labelrefundstatus', 'refund' ].indexOf( normalized ) !== -1;
			} );
			if ( refundIndex < 0 ) {
				return false;
			}
			const value = String( item.values[ refundIndex ] || '' ).toLowerCase().replace( /[^a-z0-9]/g, '' );
			return '' !== value && [ 'none', 'norefund', 'notrefunded', 'no', 'false', 'na' ].indexOf( value ) === -1;
		}

		const validRows = parsed && mapping.reference !== '' && mapping.amount !== '' && mapping.currency !== ''
			? parsed.rows.filter( function ( item ) {
				const reference = item.values[ Number( mapping.reference ) ] || '';
				const amount = parseInvoiceAmount( item.values[ Number( mapping.amount ) ], decimalMark );
				const currency = '__order_currency__' === mapping.currency ? '' : String( item.values[ Number( mapping.currency ) ] || '' ).trim();
				const currencyValid = '__order_currency__' === mapping.currency || /^[A-Za-z]{3}$/.test( currency );
				return '' !== reference.trim() && null !== amount && '-' !== amount.charAt( 0 ) && currencyValid && ! hasRefundStatus( item );
			} )
			: [];

		const matchedRows = validRows.slice( 0, 50 ).filter( function ( item ) {
			const match = matchResults[ item.number ];
			const invoiceCurrency = '__order_currency__' === mapping.currency ? String( match && match.currency || '' ).toUpperCase() : String( item.values[ Number( mapping.currency ) ] || '' ).trim().toUpperCase();
			return match
				&& 'matched' === match.status
				&& invoiceCurrency === String( match.currency || '' ).toUpperCase()
				&& ! commitResults[ item.number ];
		} );

		function getRowStatus( item ) {
			const reference = '' !== mapping.reference ? String( item.values[ Number( mapping.reference ) ] || '' ).trim() : '';
			const amountRaw = '' !== mapping.amount ? item.values[ Number( mapping.amount ) ] || '' : '';
			const amount = '' !== mapping.amount ? parseInvoiceAmount( amountRaw, decimalMark ) : null;
			const currency = '__order_currency__' === mapping.currency ? '' : ( '' !== mapping.currency ? String( item.values[ Number( mapping.currency ) ] || '' ).trim() : '' );
			const currencyValid = '__order_currency__' === mapping.currency || /^[A-Za-z]{3}$/.test( currency );
			const valid = '' !== reference && null !== amount && '-' !== amount.charAt( 0 ) && currencyValid && ! hasRefundStatus( item );
			if ( hasRefundStatus( item ) ) { return 'refund_review'; }
			if ( commitResults[ item.number ] ) { return 'saved'; }
			if ( matchResults[ item.number ] ) {
				const match = matchResults[ item.number ];
				if ( 'matched' !== match.status ) { return 'exception'; }
				const invoiceCurrency = '__order_currency__' === mapping.currency ? String( match.currency || '' ).toUpperCase() : String( item.values[ Number( mapping.currency ) ] || '' ).trim().toUpperCase();
				return invoiceCurrency === String( match.currency || '' ).toUpperCase() ? 'ready' : 'currency_mismatch';
			}
			return valid ? 'unmatched' : 'invalid';
		}

		const previewRows = parsed
			? parsed.rows.slice( 0, Object.keys( matchResults ).length > 0 ? 50 : 10 ).filter( function ( item ) {
				if ( 'all' === rowFilter ) { return true; }
				if ( 'needs_review' === rowFilter ) { return [ 'exception', 'invalid', 'unmatched', 'currency_mismatch', 'refund_review' ].indexOf( getRowStatus( item ) ) !== -1; }
				return getRowStatus( item ) === rowFilter;
			} )
			: [];

		function matchReferences() {
			if ( ! report.ajax_url || ! report.match_nonce || ! parsed ) {
				setMatchError( __( 'Matching is unavailable. Reload the dashboard and try again.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}

			const rowsToMatch = validRows.slice( 0, 50 );
			if ( rowsToMatch.length < 1 ) {
				setMatchError( __( 'Map the order ID/number and amount columns, then map a currency column or choose the matched order currency.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}

			const body = new FormData();
			body.append( 'action', 'wpe_scm_match_preview' );
			body.append( 'nonce', report.match_nonce );
			rowsToMatch.forEach( function ( item, index ) {
				body.append( 'references[' + index + '][row]', String( item.number ) );
				body.append( 'references[' + index + '][reference]', normalizeOrderReference( item.values[ Number( mapping.reference ) ] || '' ) );
			} );

			setMatching( true );
			setMatchError( '' );
			setCommitError( '' );
			setCommitResults( {} );
			fetch( report.ajax_url, { method: 'POST', credentials: 'same-origin', body: body } )
				.then( function ( response ) { return response.json(); } )
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
				.finally( function () { setMatching( false ); } );
		}

		async function createBatchHash() {
			if ( ! window.crypto || ! window.crypto.subtle || ! window.TextEncoder ) {
				throw new Error( __( 'Secure file fingerprinting is unavailable in this browser. No invoice data was sent.', 'wpessential-shipping-cost-monitor' ) );
			}

			const signature = [
				rawCsv,
				delimiter,
				decimalMark,
				mapping.reference,
				mapping.amount,
				mapping.currency,
				evidence,
				taxBasis,
			].join( '\n' );
			const digest = await window.crypto.subtle.digest( 'SHA-256', new window.TextEncoder().encode( signature ) );
			return Array.from( new Uint8Array( digest ) ).map( function ( byte ) {
				return byte.toString( 16 ).padStart( 2, '0' );
			} ).join( '' );
		}

		async function commitMatchedRows() {
			if ( ! report.ajax_url || ! report.import_nonce || matchedRows.length < 1 ) {
				setCommitError( __( 'There are no matched rows ready to save.', 'wpessential-shipping-cost-monitor' ) );
				return;
			}

			if ( ! window.confirm( __( 'Save these matched courier costs to their WooCommerce orders? Existing cost records will be left unchanged.', 'wpessential-shipping-cost-monitor' ) ) ) {
				return;
			}

			setCommitting( true );
			setCommitError( '' );
			try {
				const batchHash = await createBatchHash();
				const body = new FormData();
				body.append( 'action', 'wpe_scm_commit_invoice_rows' );
				body.append( 'nonce', report.import_nonce );

				matchedRows.forEach( function ( item, index ) {
					const match = matchResults[ item.number ];
					const amount = parseInvoiceAmount( item.values[ Number( mapping.amount ) ], decimalMark );
					const currency = '__order_currency__' === mapping.currency ? String( match.currency || '' ).toUpperCase() : String( item.values[ Number( mapping.currency ) ] || '' ).trim().toUpperCase();
					body.append( 'rows[' + index + '][row]', String( item.number ) );
					body.append( 'rows[' + index + '][order_id]', String( match.order_id ) );
					body.append( 'rows[' + index + '][reference]', normalizeOrderReference( item.values[ Number( mapping.reference ) ] || '' ) );
					body.append( 'rows[' + index + '][amount]', amount );
					body.append( 'rows[' + index + '][currency]', currency );
					body.append( 'rows[' + index + '][batch]', batchHash );
					body.append( 'rows[' + index + '][evidence]', evidence );
					body.append( 'rows[' + index + '][tax_basis]', taxBasis );
				} );

				const response = await fetch( report.ajax_url, { method: 'POST', credentials: 'same-origin', body: body } );
				const payload = await response.json();
				if ( ! payload || ! payload.success || ! payload.data || ! Array.isArray( payload.data.results ) ) {
					throw new Error( payload && payload.data && payload.data.message ? payload.data.message : __( 'The invoice rows could not be saved.', 'wpessential-shipping-cost-monitor' ) );
				}

				const resultMap = {};
				payload.data.results.forEach( function ( result ) {
					resultMap[ result.row ] = result;
				} );
				setCommitResults( resultMap );
			} catch ( commitRequestError ) {
				setCommitError( commitRequestError.message || __( 'The invoice rows could not be saved.', 'wpessential-shipping-cost-monitor' ) );
			} finally {
				setCommitting( false );
			}
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
							setCommitResults( {} );
						},
					},
					h( 'option', { value: '' }, __( 'Choose a column', 'wpessential-shipping-cost-monitor' ) ),
					field === 'currency' ? h( 'option', { value: '__order_currency__' }, __( 'Use matched order currency', 'wpessential-shipping-cost-monitor' ) ) : null,
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
			h( 'p', { className: 'wpe-scm-import-privacy' }, __( 'The file stays in this browser. Matching sends mapped order references to WordPress. Saving sends only matched row values and order IDs; the raw file and other columns are not stored. Mapping profiles are saved only in this browser and contain column names/settings, not invoice rows.', 'wpessential-shipping-cost-monitor' ) ),
			mappingProfiles.length ? h( 'div', { className: 'wpe-scm-import-toolbar' },
				h( 'label', { className: 'wpe-scm-import-field' }, __( 'Saved mapping profile', 'wpessential-shipping-cost-monitor' ), h( 'select', { value: selectedProfileId, onChange: function ( event ) { setSelectedProfileId( event.target.value ); } }, h( 'option', { value: '' }, __( 'Choose a saved profile', 'wpessential-shipping-cost-monitor' ) ), mappingProfiles.map( function ( profile ) { return h( 'option', { key: profile.id, value: profile.id }, profile.name ); } ) ) ),
				h( 'button', { type: 'button', className: 'button', onClick: applyMappingProfile, disabled: ! parsed || ! selectedProfileId }, __( 'Apply profile', 'wpessential-shipping-cost-monitor' ) ),
				h( 'button', { type: 'button', className: 'button', onClick: deleteMappingProfile, disabled: ! selectedProfileId }, __( 'Delete profile', 'wpessential-shipping-cost-monitor' ) )
			) : null,
			parsed ? h( 'div', { className: 'wpe-scm-import-toolbar' }, h( 'button', { type: 'button', className: 'button', onClick: saveMappingProfile }, __( 'Save current mapping', 'wpessential-shipping-cost-monitor' ) ) ) : null,
			profileNotice ? h( 'p', { className: 'wpe-scm-report-notice', role: 'status' }, profileNotice ) : null,
			h(
				'div',
				{ className: 'wpe-scm-import-toolbar' },
				h( 'label', { className: 'wpe-scm-import-field' }, __( 'Source format', 'wpessential-shipping-cost-monitor' ), h( 'select', { value: sourceFormat, onChange: selectSourceFormat }, h( 'option', { value: 'generic' }, __( 'Generic courier CSV', 'wpessential-shipping-cost-monitor' ) ), h( 'option', { value: 'woocommerce_shipping_labels' }, __( 'WooCommerce Shipping label report', 'wpessential-shipping-cost-monitor' ) ) ) ),

				h( 'label', { className: 'wpe-scm-file-field' }, __( 'Choose CSV file (5 MB max)', 'wpessential-shipping-cost-monitor' ), h( 'input', { type: 'file', accept: '.csv,text/csv', onChange: chooseFile } ) ),
				h(
					'label',
					{ className: 'wpe-scm-import-field' },
					__( 'Column separator', 'wpessential-shipping-cost-monitor' ),
					h( 'select', { value: delimiter, onChange: function ( event ) { setDelimiter( event.target.value ); setMatchResults( {} ); setCommitResults( {} ); } },
						h( 'option', { value: ',' }, __( 'Comma', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: ';' }, __( 'Semicolon', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: '\t' }, __( 'Tab', 'wpessential-shipping-cost-monitor' ) )
					)
				),
				h(
					'label',
					{ className: 'wpe-scm-import-field' },
					__( 'Decimal mark', 'wpessential-shipping-cost-monitor' ),
					h( 'select', { value: decimalMark, onChange: function ( event ) { setDecimalMark( event.target.value ); setMatchResults( {} ); setCommitResults( {} ); } },
						h( 'option', { value: '.' }, __( 'Period (1,234.56)', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: ',' }, __( 'Comma (1.234,56)', 'wpessential-shipping-cost-monitor' ) )
					)
				),
				h(
					'label',
					{ className: 'wpe-scm-import-field' },
					__( 'Imported evidence', 'wpessential-shipping-cost-monitor' ),
					h( 'select', { value: evidence, onChange: function ( event ) { setEvidence( event.target.value ); setCommitResults( {} ); } },
						h( 'option', { value: 'invoice_final' }, __( 'Invoice-final', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: 'booked_label' }, __( 'Booked label / manifest', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: 'merchant_confirmed' }, __( 'Merchant-confirmed', 'wpessential-shipping-cost-monitor' ) )
					)
				),
				h(
					'label',
					{ className: 'wpe-scm-import-field' },
					__( 'Cost tax treatment', 'wpessential-shipping-cost-monitor' ),
					h( 'select', { value: taxBasis, onChange: function ( event ) { setTaxBasis( event.target.value ); setCommitResults( {} ); } },
						h( 'option', { value: 'unknown' }, __( 'Unknown — do not compare', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: 'exclusive' }, __( 'Tax-exclusive', 'wpessential-shipping-cost-monitor' ) ),
						h( 'option', { value: 'inclusive' }, __( 'Tax-inclusive', 'wpessential-shipping-cost-monitor' ) )
					)
				)
			),
			fileName ? h( 'p', { className: 'wpe-scm-import-file' }, __( 'Selected:', 'wpessential-shipping-cost-monitor' ) + ' ' + fileName ) : null,
			sourceFormat === 'woocommerce_shipping_labels' ? h( 'p', { className: 'wpe-scm-report-notice', role: 'status' }, __( 'This preset treats label prices as booked costs, uses the matched order currency and holds refund-status rows for review. Orders with multiple label rows may be marked duplicate and require separate reconciliation.', 'wpessential-shipping-cost-monitor' ) ) : null,
			error ? h( 'p', { className: 'wpe-scm-report-notice', role: 'alert' }, error ) : null,
			matchError ? h( 'p', { className: 'wpe-scm-report-notice', role: 'alert' }, matchError ) : null,
			commitError ? h( 'p', { className: 'wpe-scm-report-notice', role: 'alert' }, commitError ) : null,
			parsed ? h(
				'div',
				{ className: 'wpe-scm-import-preview' },
				h( 'div', { className: 'wpe-scm-import-mapping' },
					fieldSelect( __( 'Order ID / number', 'wpessential-shipping-cost-monitor' ), 'reference' ),
					fieldSelect( __( 'Charge amount', 'wpessential-shipping-cost-monitor' ), 'amount' ),
					fieldSelect( __( 'Currency code', 'wpessential-shipping-cost-monitor' ), 'currency' )
				),
				h( 'p', { className: 'wpe-scm-import-count' }, validRows.length + ' / ' + parsed.rows.length + ' ' + __( 'rows have a valid reference, non-negative amount and three-letter currency (or inferred order currency). Negative rows and rows with a refund status require review and are excluded.', 'wpessential-shipping-cost-monitor' ) ),
				h( 'label', { className: 'wpe-scm-import-field' }, __( 'Show rows', 'wpessential-shipping-cost-monitor' ), h( 'select', { value: rowFilter, onChange: function ( event ) { setRowFilter( event.target.value ); } }, h( 'option', { value: 'all' }, __( 'All preview rows', 'wpessential-shipping-cost-monitor' ) ), h( 'option', { value: 'needs_review' }, __( 'Needs review', 'wpessential-shipping-cost-monitor' ) ), h( 'option', { value: 'ready', }, __( 'Ready to save', 'wpessential-shipping-cost-monitor' ) ), h( 'option', { value: 'saved', }, __( 'Saved', 'wpessential-shipping-cost-monitor' ) ) ),
				h( 'div', { className: 'wpe-scm-import-actions' },
					h( 'button', { type: 'button', className: 'button', onClick: matchReferences, disabled: matching || validRows.length < 1 }, matching ? __( 'Matching…', 'wpessential-shipping-cost-monitor' ) : __( 'Match first 50 valid rows', 'wpessential-shipping-cost-monitor' ) ),
					matchedRows.length > 0 ? h( 'button', { type: 'button', className: 'button button-primary', onClick: commitMatchedRows, disabled: committing }, committing ? __( 'Saving…', 'wpessential-shipping-cost-monitor' ) : __( 'Save matched costs to orders', 'wpessential-shipping-cost-monitor' ) ) : null,
					h( 'button', { type: 'button', className: 'button', onClick: clearPreview, disabled: matching || committing }, __( 'Clear preview', 'wpessential-shipping-cost-monitor' ) )
				),
				matchedRows.length > 0 ? h( 'p', { className: 'wpe-scm-import-count' }, __( 'Saving updates only matched orders with no existing cost record. Existing costs are never replaced. After saving, reload the dashboard to refresh report totals.', 'wpessential-shipping-cost-monitor' ) ) : null,
				h(
					'div',
					{ className: 'wpe-scm-import-table-wrap' },
					h( 'table', { className: 'widefat striped wpe-scm-import-table' },
						h( 'thead', null, h( 'tr', null,
							h( 'th', null, __( 'Source row', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Reference', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Amount', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Currency', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Validation / match result', 'wpessential-shipping-cost-monitor' ) )
						) ),
						h( 'tbody', null, previewRows.map( function ( item ) {
							const reference = '' !== mapping.reference ? item.values[ Number( mapping.reference ) ] || '' : '';
							const amountRaw = '' !== mapping.amount ? item.values[ Number( mapping.amount ) ] || '' : '';
							const amount = '' !== mapping.amount ? parseInvoiceAmount( amountRaw, decimalMark ) : null;
							const currency = '__order_currency__' === mapping.currency ? __( 'Order currency', 'wpessential-shipping-cost-monitor' ) : ( '' !== mapping.currency ? String( item.values[ Number( mapping.currency ) ] || '' ).trim() : '' );
							const valid = '' !== reference.trim() && null !== amount && '-' !== amount.charAt( 0 ) && ( '__order_currency__' === mapping.currency || /^[A-Za-z]{3}$/.test( currency ) ) && ! hasRefundStatus( item );
							const match = matchResults[ item.number ];
							const commitResult = commitResults[ item.number ];
							const status = hasRefundStatus( item )
								? __( 'Refund status needs review', 'wpessential-shipping-cost-monitor' )
								: commitResult
									? commitResult.status
								: match && 'matched' === match.status && match.order_number && '__order_currency__' !== mapping.currency && currency.toUpperCase() !== String( match.currency || '' ).toUpperCase()
									? __( 'Currency differs from order', 'wpessential-shipping-cost-monitor' ) + ' (' + match.currency + ')'
									: match && 'existing_cost' === match.status && match.order_number
										? __( 'Existing cost on order', 'wpessential-shipping-cost-monitor' ) + ' #' + match.order_number
										: match && 'matched' === match.status && match.order_number
											? 'matched: #' + match.order_number + ' (' + match.order_status + ')'
											: match ? match.status : valid ? __( 'Ready to match', 'wpessential-shipping-cost-monitor' ) : amount && '-' === amount.charAt( 0 ) ? __( 'Credit/refund needs review', 'wpessential-shipping-cost-monitor' ) : __( 'Missing or invalid required value', 'wpessential-shipping-cost-monitor' );
							return h( 'tr', { key: item.number },
								h( 'td', null, item.number ),
								h( 'td', null, reference ),
								h( 'td', null, amountRaw ),
								h( 'td', null, currency ),
								h( 'td', null,
									status,
									match && 'existing_cost' === match.status && match.existing_cost
										? h( 'div', { className: 'wpe-scm-import-review-detail' },
											h( 'strong', null, __( 'Review comparison:', 'wpessential-shipping-cost-monitor' ) ),
											' ' + __( 'Recorded', 'wpessential-shipping-cost-monitor' ) + ': ' + match.existing_cost.currency + ' ' + match.existing_cost.amount + ' (' + match.existing_cost.evidence + '; tax ' + match.existing_cost.tax_basis + '). ',
											__( 'CSV row', 'wpessential-shipping-cost-monitor' ) + ': ' + ( '__order_currency__' === mapping.currency ? match.currency : currency.toUpperCase() ) + ' ' + ( amount || amountRaw ) + '. ',
											__( 'Existing value is kept; review the difference before changing the order.', 'wpessential-shipping-cost-monitor' )
										)
										: null
								)
							);
						} ) )
					)
				)
			) : null
		);
	}

	function CarrierConnections() {
		const stateLabels = {
			connected: __( 'Connected', 'wpessential-shipping-cost-monitor' ),
			disconnected: __( 'Not connected', 'wpessential-shipping-cost-monitor' ),
			reauth_required: __( 'Reconnect required', 'wpessential-shipping-cost-monitor' ),
			unavailable: __( 'Unavailable', 'wpessential-shipping-cost-monitor' ),
		};

		return h(
			'section',
			{ className: 'wpe-scm-card wpe-scm-carrier-connections' },
			h( 'p', { className: 'wpe-scm-eyebrow' }, __( 'OPTIONAL CARRIER DATA', 'wpessential-shipping-cost-monitor' ) ),
			h( 'h2', null, __( 'Carrier connections', 'wpessential-shipping-cost-monitor' ) ),
			h( 'p', null, __( 'Carrier adapters can add shipment IDs, tracking references or booked-cost evidence. They do not change checkout rates or order totals.', 'wpessential-shipping-cost-monitor' ) ),
			carrierAdapters.length > 0
				? h( 'ul', { className: 'wpe-scm-carrier-list' }, carrierAdapters.map( function ( adapter ) {
					return h( 'li', { key: adapter.id, className: 'wpe-scm-carrier-item' },
						h( 'strong', null, adapter.label ),
						h( 'span', { className: 'wpe-scm-status-pill' }, stateLabels[ adapter.state ] || stateLabels.unavailable ),
						adapter.regions && adapter.regions.length ? h( 'p', null, __( 'Regions:', 'wpessential-shipping-cost-monitor' ) + ' ' + adapter.regions.join( ', ' ) ) : null,
						adapter.capabilities && adapter.capabilities.length ? h( 'p', null, __( 'Evidence:', 'wpessential-shipping-cost-monitor' ) + ' ' + adapter.capabilities.join( ', ' ) ) : null
					);
				} ) )
				: h( 'p', { className: 'wpe-scm-report-notice', role: 'status' }, __( 'No carrier API adapter is enabled. Manual cost entry and invoice CSV reconciliation remain available; no provider credentials are configured or stored by this build.', 'wpessential-shipping-cost-monitor' ) )
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
		const methodRows = groups.reduce( function ( rows, group ) {
			return rows.concat( ( group.shipping_methods || [] ).filter( function ( method ) { return Number( method.comparable_orders ) > 0; } ).map( function ( method ) { return Object.assign( { currency: group.currency }, method ); } ) );
		}, [] );
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
			 h( CarrierConnections ),
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
			methodRows.length > 0 ? h(
				'section',
				{ className: 'wpe-scm-card wpe-scm-method-report' },
				h( 'p', { className: 'wpe-scm-eyebrow' }, __( 'COST BY SHIPPING METHOD', 'wpessential-shipping-cost-monitor' ) ),
				h( 'h2', null, __( 'Comparable shipping cost by method', 'wpessential-shipping-cost-monitor' ) ),
				h( 'p', { className: 'wpe-scm-import-privacy' }, __( 'Variance includes only currency-matched, confirmed tax-exclusive cost records. Mixed-method orders are grouped by their method combination.', 'wpessential-shipping-cost-monitor' ) ),
				h( 'div', { className: 'wpe-scm-import-table-wrap' },
					h( 'table', { className: 'widefat striped wpe-scm-import-table' },
						h( 'thead', null, h( 'tr', null,
							h( 'th', null, __( 'Shipping method', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Orders', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Shipping charged', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Confirmed cost', 'wpessential-shipping-cost-monitor' ) ),
							h( 'th', null, __( 'Variance', 'wpessential-shipping-cost-monitor' ) )
						) ),
						h( 'tbody', null, methodRows.map( function ( method, index ) {
							return h( 'tr', { key: method.currency + '-' + index },
								h( 'td', null, method.label + ' (' + method.currency + ')' ),
								h( 'td', null, method.comparable_orders ),
								h( 'td', null, method.currency + ' ' + method.shipping_charged ),
								h( 'td', null, method.currency + ' ' + method.comparable_confirmed_cost ),
								h( 'td', null, method.currency + ' ' + method.variance )
							);
						} ) )
					)
				)
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
