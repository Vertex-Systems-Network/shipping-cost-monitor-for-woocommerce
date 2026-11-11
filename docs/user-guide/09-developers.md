# 9. For developers: hooks and data

[← Guide contents](README.md)

## Order meta keys

All values are stored through WooCommerce order CRUD (`$order->update_meta_data()`), so they work with HPOS and legacy storage. Read them with `$order->get_meta( $key, true )`.

| Key | Type | Values |
|---|---|---|
| `_wpe_scm_cost_amount` | decimal string | Non-negative, store decimals |
| `_wpe_scm_cost_currency` | string | ISO 4217 code |
| `_wpe_scm_cost_evidence` | string | `invoice_final`, `merchant_confirmed`, `booked_label`, `estimate` |
| `_wpe_scm_cost_tax_basis` | string | `exclusive`, `inclusive`, `unknown` |
| `_wpe_scm_cost_note` | string | Up to 2,000 characters |
| `_wpe_scm_cost_updated_at` | string | UTC `Y-m-d H:i:s` |
| `_wpe_scm_cost_updated_by` | int | User ID |
| `_wpe_scm_cost_import_fingerprint` | string | Pro: SHA-256 of batch, row and order; used to detect replays |
| `_wpe_scm_cost_import_batch` | string | Pro: SHA-256 of the imported file |
| `_wpe_scm_cost_import_row` | int | Pro: source row number |

A comparable cost is `invoice_final` or `merchant_confirmed`, `exclusive`, and in the order currency.

## Actions and filters (free plugin)

| Hook | Type | Arguments | Use |
|---|---|---|---|
| `wpe_scm_init` | action | `Plugin $plugin` | Fires after the core modules register, only when WooCommerce is active. Register add-on modules here. |
| `wpe_scm_dashboard_data` | filter | `array $data` | Data passed to the dashboard script as `window.wpeScmDashboard`. Add keys; do not remove core ones. |
| `wpe_scm_dashboard_enqueued` | action | — | Fires after the dashboard script `wpe-scm-admin` is enqueued. Enqueue scripts that depend on it. |
| `wpe_scm_carrier_adapters` | filter | `array $adapters` | Register objects implementing `WPEssential\ShippingCostMonitor\Integrations\CarrierAdapterInterface`. |

## Adding a dashboard panel

The dashboard renders `window.wpeScmPro.InvoicePanel` when it is a component. An add-on can provide its own panel the same way:

```js
// Enqueued on wpe_scm_dashboard_enqueued with 'wpe-scm-admin' as a dependency.
( function ( wp ) {
	function MyPanel() {
		return wp.element.createElement( 'section', { className: 'wpe-scm-card' }, 'Hello' );
	}
	window.wpeScmPro = Object.assign( window.wpeScmPro || {}, { InvoicePanel: MyPanel } );
} )( window.wp );
```

Scripts enqueued on `wpe_scm_dashboard_enqueued` run after `wpe-scm-admin`, so `window.wpeScmDashboard` is already defined; the dashboard mounts on `DOMContentLoaded`, after footer scripts.

## Pro AJAX endpoints

All require `manage_woocommerce`, a nonce from the dashboard data, and POST.

| Action | Nonce key | Purpose |
|---|---|---|
| `wpe_scm_match_preview` | `match_nonce` | Match up to 50 `references[i][row]` / `references[i][reference]`. Read-only. |
| `wpe_scm_commit_invoice_rows` | `import_nonce` | Save up to 50 matched rows. Never overwrites an existing cost. Idempotent per batch, row and order. |
| `wpe_scm_shipstation_connect` | `carrier_nonce` | Test and store an API key. |
| `wpe_scm_shipstation_disconnect` | `carrier_nonce` | Delete the stored key. |
| `wpe_scm_shipstation_sync` | `carrier_sync_nonce` | Read one page of labels. Read-only. |

## Building the plugins

The repository builds both ZIPs in GitHub Actions (`.github/workflows/build-installable-zip.yml`): Composer generates each plugin's PSR-4 autoloader, PHP 8.1–8.4 lint runs, versions are checked, the free package is checked for Pro code, and two artifacts are uploaded. There is no npm or JavaScript build step; the admin UI uses WordPress's bundled React (`wp.element`).
