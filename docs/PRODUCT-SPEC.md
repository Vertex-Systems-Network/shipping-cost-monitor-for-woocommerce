# Product requirements and options bank

**Status:** Draft for pre-development review  
**Product:** WPEssential Shipping Cost Monitor  
**Parent suite:** WPEssential  
**Audience:** WooCommerce merchants who need to compare customer-paid shipping with the real cost of fulfilment.

## Problem statement

WooCommerce records the shipping amount charged to a customer, but merchants may pay a different amount to couriers. Those differences can be hidden across orders, invoices, shipping methods, locations, surcharges, and returns. The plugin should make actual costs recordable, reconcile evidence, and show where shipping margin is being lost.

## Target outcomes

- A merchant can see the customer-paid shipping, actual fulfilment cost, and variance for each eligible order.
- Imported invoice charges are matched to orders with a visible confidence/status and a review path.
- Duplicate, unmatched, and materially different charges are surfaced without silently overwriting a merchant's record.
- Reports help merchants identify repeat loss patterns by time period and available shipping dimensions.
- The plugin remains useful with manual entry and CSV import, without requiring a third-party courier account.

## Feature ID policy

Every distinct user-facing capability receives one stable ID. Related options are grouped under that ID rather than repeated across pages. The table below is the single planning bank; new proposals must be checked against it before addition.

| ID | Capability | Free / Pro | Priority | Acceptance direction |
|---|---|---|---|---|
| SCM-ORD-01 | Order-level actual shipping cost field | Free | MVP | Save/read via WooCommerce order CRUD; display customer-paid shipping, actual cost, variance and source separately. |
| SCM-ORD-02 | Cost source and audit history | Free | MVP | Record manual/import/rule source, timestamp, actor where available, and changes; never silently erase prior values. |
| SCM-ORD-03 | Parcel-level cost components | Pro | Later | Support multiple parcels and component labels such as base, fuel, COD, insurance, return; totals reconcile to order. |
| SCM-IMP-01 | CSV upload with column mapping | Pro | MVP | Preview file, map columns, validate rows, show errors before import; no silent partial success. |
| SCM-IMP-02 | Match invoice rows to orders | Pro | MVP | Match using user-selected identifiers (order/AWB/tracking) and fallback fields only when configured; show match basis. |
| SCM-IMP-03 | Reconciliation exception queue | Pro | MVP | Separate matched, unmatched, duplicate, ambiguous and amount-mismatch records; provide resolve/skip actions with audit. |
| SCM-IMP-04 | Import template profiles | Pro | Later | Save reusable mappings per courier/source; users can edit or delete profiles. |
| SCM-REP-01 | Basic period summary | Free | MVP | Show order count, customer-paid shipping, recorded actual cost, known variance and coverage percentage for selected period. |
| SCM-REP-02 | Shipping loss breakdowns | Pro | MVP | Aggregate variance by WooCommerce shipping method and zone where source data supports; expose missing-data caveat. |
| SCM-REP-03 | Trend and period comparison | Pro | Later | Compare selected periods using consistent filters and definitions; no extrapolation presented as actual. |
| SCM-REP-04 | CSV report export | Free (basic); Pro (filtered advanced) | MVP | Export only filtered rows/columns; escape spreadsheet formula-leading cells. |
| SCM-REP-05 | Print/PDF report | Pro | Later | Provide a print-ready report with date range, definitions, filters, generated timestamp and totals. |
| SCM-ALR-01 | User-configured variance threshold alerts | Pro | Later | Alert only when required cost data exists; configure threshold and frequency; avoid duplicate notifications. |
| SCM-SIM-01 | Shipping-rate what-if simulator | Pro | Later | Model proposed fixed/percentage changes over historical orders; mark all outputs as estimates, never alter live methods/rates. |
| SCM-INT-01 | WooCommerce Shipping cost import | Free | MVP if API/data path verified | Import supported label cost evidence idempotently; show source and import result. |
| SCM-INT-02 | ShipStation or other provider adapters | Pro | Later | Add only after supported API, permissions, data handling and demand are documented. |
| SCM-INT-03 | WPML / Polylang compatibility | Free | MVP | Translate plugin-owned strings; preserve order values; verify admin screen and notices in multilingual sites. |
| SCM-SET-01 | Data retention and uninstall controls | Free | MVP | Explain retained order metadata; offer explicit opt-in purge setting; default uninstall must not unexpectedly delete merchant records. |
| SCM-SET-02 | Permissions, privacy and diagnostics | Free | MVP | Least privilege, privacy notice for external integrations, opt-in diagnostics only, actionable status/errors. |

## Platform and compatibility baseline

This is the initial support policy for development and the first release, based on official platform recommendations checked on 2026-11-06. Recheck it before implementation and Marketplace submission because platform support windows change.

| Platform | Initial baseline | Compatibility policy |
|---|---|---|
| PHP | 8.3+ | Target currently recommended WooCommerce runtime; verify on PHP 8.3 and 8.4. Do not claim PHP 8.5 until the supported WooCommerce release is documented as tested there. |
| WordPress | 6.9+ | Follow the selected WooCommerce baseline; test current stable and one earlier supported release where the release matrix permits. |
| WooCommerce | 10.8+ | Follow WooCommerce 10.8 server recommendations at planning time; test current stable and the previous supported release at release time. |
| Database | MySQL 8.0+ or MariaDB 10.6+ | Follow WooCommerce server recommendations; avoid version-specific SQL assumptions. |
| HPOS | Supported | Use WooCommerce CRUD APIs and declare compatibility only after tests pass on HPOS and legacy order storage. |
| React admin | React bundled through WordPress/WooCommerce-supported extension APIs | React is for admin presentation; PHP/domain services remain authoritative. Toolchain/build decision waits for pre-development Gate D. |
| WPML | Optional compatibility target | Translate plugin-owned admin/UI strings via standard localization and WPML String Translation mechanisms where needed. Do not require WPML or alter order language/currency data. Test with the latest stable WPML stack available at release. |
| Polylang | Optional compatibility target | Translate plugin-owned strings; do not require Polylang. If WooCommerce multilingual features are installed, test with Polylang for WooCommerce as an optional integration. Record exact tested versions at release. |
| Memory | 256 MB recommended by WooCommerce | The plugin should avoid unnecessary memory growth; document import size limits and process large jobs in bounded batches. |

WPML and Polylang are compatibility targets, not plugin dependencies. They must not create duplicated settings or change numeric cost records based on display language. Currency conversion, if ever added, requires its own feature and explicit source-rate rules; it is not implied by translation support.

Official references:
- [WordPress requirements](https://wordpress.org/about/requirements/)
- [WooCommerce server recommendations](https://woocommerce.com/document/server-requirements/)
- [WooCommerce HPOS extension recipe book](https://developer.woocommerce.com/docs/features/orders/high-performance-order-storage/recipe-book/)
- [WPML minimum requirements](https://wpml.org/home/minimum-requirements)
- [Polylang for WooCommerce changelog and compatibility requirements](https://polylang.pro/polylang-for-woocommerce-changelog/)
- [WooCommerce React-powered admin pages](https://developer.woocommerce.com/docs/extensions/settings-and-config/working-with-woocommerce-admin-pages/)

## Explicitly out of scope for first release

- Checkout rate calculation or modifying customer shipping prices.
- Label purchase, shipment tracking portal, fulfilment dispatch, or courier account replacement.
- Full business profit-and-loss, product COGS, gateway fee, tax, inventory or ad-spend suite.
- Automatic recommendations that change shipping settings.
- Runtime AI analysis or sending order/invoice data to an LLM.

## Status vocabulary

- **MVP:** needed for first useful release, still subject to technical validation.
- **Later:** valid product direction, deferred until MVP evidence and performance are sound.
- **Free / Pro:** proposed packaging, to validate commercially and against Marketplace rules before release.
- **Priority:** planning order, not a promise or implementation status.

## Product acceptance rules

1. Every metric defines its numerator, denominator, currency behavior, order statuses, refunds and missing-data handling.
2. “Actual cost” must be backed by manual input, imported evidence, or an explicitly-labelled estimate/rule.
3. Imports are previewable, idempotent where possible, and reversible through a clear correction workflow.
4. Report tables are paginated/server-filtered; no unbounded load of all order data into the browser.
5. Missing/incomplete costs are reported as coverage gaps, not silently treated as zero.
