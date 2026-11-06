# Product requirements and options bank

**Status:** Draft for product-owner review  
**Product:** WPEssential Shipping Cost Monitor  
**Parent suite:** WPEssential  
**Audience:** WooCommerce merchants who need to compare customer-paid shipping with their actual fulfilment spend.

## Problem statement

WooCommerce records the shipping amount charged to a customer, but a merchant may pay a different amount to a courier. Differences can be hidden across orders, invoices, shipping methods, locations, surcharges, splits, returns and adjustments. The plugin should make costs recordable, reconcile evidence, and show where shipping margin is being lost.

## Target outcomes

- Show customer-paid shipping, recorded actual/confirmed cost, evidence type and variance per eligible order.
- Match invoice/API charge evidence to orders using identifiers, with visible basis and review.
- Surface duplicate, unmatched, ambiguous and materially different charges without silently overwriting merchant data.
- Report recurring shipping loss patterns by period and supported shipping dimensions.
- Remain useful with manual entry and invoice CSV even without carrier API access.
- Automate shipment/cost evidence sync where provider APIs support it, while keeping quotes, booked charges and final billed costs distinct.

## Proposed MVP decision pack

**Status: proposal, not yet product-owner approved.** This is the recommended first useful release based on the competitor/API desk review.

### Free

- Manual actual shipping cost per WooCommerce order.
- Provenance/audit history and clear split between customer-paid shipping, entered cost and variance.
- Basic date-filtered summary: order count, shipping charged, recorded costs, known variance and coverage rate.
- Basic filters and CSV export.
- Optional WooCommerce Shipping label-cost import only after the plugin can verify and safely read the purchase amount.

### Pro

- Generic courier invoice CSV upload, field mapping, preview and validation.
- Exact-ID matching (order number, tracking/AWB or provider shipment ID) and a reviewable exception queue.
- Duplicate, unmatched, ambiguous and mismatch handling with auditable corrections.
- Shipping variance reports by method and WooCommerce zone where data exists.
- One or two optional API adapters chosen after beta merchant usage and account-level endpoint verification. First sync shipment identifiers/booked label amounts; final billing automation only if invoice-final data is documented and accessible.
- Pro remains useful without API access because CSV is the cross-provider reconciliation path.

### Defer until evidence supports them

- Integrating every carrier, rate shopping, label purchase, tracking portal or fulfilment automation.
- Scheduled alerts, period comparisons, PDF/email reports and rate-change simulator.
- Fine-grained per-parcel costing if the first merchant workflows can reconcile invoices at order-total level.
- Any AI service or customer/order data sent to an LLM.

This reduces overlap with broad profit plugins while still enabling future API automation. Any Free/Pro pricing or boundaries require merchant validation and Marketplace review.

## Feature ID policy

Every distinct capability gets one stable ID. This is the single options bank; search it before adding settings. A new screen must reuse existing capability/settings rather than duplicating them.

| ID | Capability | Free / Pro | Priority | Acceptance direction |
|---|---|---|---|---|
| SCM-ORD-01 | Order-level actual shipping cost field | Free | MVP | Store through WooCommerce order CRUD; display charged amount, selected cost, evidence type and variance. |
| SCM-ORD-02 | Cost source and audit history | Free | MVP | Store source, timestamp and actor where available; preserve prior decisions. |
| SCM-ORD-03 | Parcel-level cost components | Pro | Later | Support multiple parcels and labelled components (base, fuel, COD, insurance, return); order total reconciles. |
| SCM-IMP-01 | CSV upload with column mapping | Pro | MVP | Preview/map/validate before commit; show row errors and import totals. |
| SCM-IMP-02 | Match invoice rows to orders | Pro | MVP | Prefer configured exact order/AWB/tracking IDs; show match basis. |
| SCM-IMP-03 | Reconciliation exception queue | Pro | MVP | Handle unmatched, duplicate, ambiguous and amount mismatch; resolve/skip actions are audited. |
| SCM-IMP-04 | Import template profiles | Pro | Later | Save reusable field/date/amount/currency mappings per source. |
| SCM-REP-01 | Basic period summary | Free | MVP | Show order count, shipping charged, recorded actual cost, known variance and coverage. |
| SCM-REP-02 | Shipping loss breakdowns | Pro | MVP | Break down by method and zone where source data supports; disclose missing/incomplete data. |
| SCM-REP-03 | Trend and period comparison | Pro | Later | Compare equivalent periods under consistent filters and metric definitions. |
| SCM-REP-04 | CSV report export | Free (basic); Pro (filtered advanced) | MVP | Export only filtered fields/rows; protect against spreadsheet formula injection. |
| SCM-REP-05 | Print/PDF report | Pro | Later | Include date range, filters, metric definitions and generated timestamp. |
| SCM-ALR-01 | Variance threshold alerts | Pro | Later | Configurable threshold/frequency; require actual/eligible data; deduplicate notices. |
| SCM-SIM-01 | Shipping-rate what-if simulator | Pro | Later | Run explicit assumptions on historical data; label estimate; never alter live shipping rates. |
| SCM-INT-01 | WooCommerce Shipping cost import | Free | Conditional MVP | Import purchase evidence only if supported API/data path is verified; label as booked, not invoice-final. |
| SCM-INT-02 | ShipStation/other provider adapters | Pro | Later | Add only after API semantics, permissions, demand and maintenance review. |
| SCM-INT-03 | WPML / Polylang compatibility | Free | MVP | Translate plugin-owned strings; preserve underlying numeric records. |
| SCM-INT-04 | Carrier/platform API adapter framework | Pro | MVP foundation | Shared connection, capability, sync, normalization, error and disconnect contract. |
| SCM-INT-05 | Automatic API sync controls | Pro | MVP foundation | Manual/scheduled sync; signed webhooks only where provider supports; status, retries and idempotency. |
| SCM-INT-06 | API connection/security management | Pro | MVP foundation | Least scopes, protected credentials, masked values, disconnect/revoke path, redacted logs, data disclosure. |
| SCM-INT-07 | API evidence classification | Pro | MVP | Distinguish quote, booked/label, invoice-final, adjustment/refund and unknown; never silently promote quote to actual. |
| SCM-SET-01 | Retention and uninstall controls | Free | MVP | Explain retained order data; purge only with explicit setting/choice. |
| SCM-SET-02 | Permissions, privacy and diagnostics | Free | MVP | Least privilege, opt-in diagnostics, clear external-service disclosures and actionable errors. |

### Provider priority

Public desk research shortlist: UPS, FedEx, DHL Express; Aramex, Emirates Post and iMile for GCC; TCS, PostEx and Leopards for Pakistan/South Asia; plus WooCommerce Shipping and ShipStation as existing shipping platforms. See [carrier/API evidence matrix](INTEGRATION-MATRIX.md).

This is not a promised connector roadmap. Select the first one or two from beta usage and account-level proof of shipment identifiers and cost fields. Public docs currently establish many rating/shipping/tracking features but do not establish universally accessible final invoice feeds.

## Platform and compatibility baseline

Initial policy based on official recommendations checked 2026-11-06. Recheck at release because platform support windows change.

| Platform | Initial baseline | Compatibility policy |
|---|---|---|
| PHP | 8.3+ | Verify on PHP 8.3 and 8.4; do not claim newer versions until supported WooCommerce release is tested there. |
| WordPress | 6.9+ | Follow WooCommerce baseline; test current stable and an earlier supported version at release where viable. |
| WooCommerce | 10.8+ | Follow current server recommendations; verify current and previous supported release at release. |
| Database | MySQL 8.0+ or MariaDB 10.6+ | Avoid version-specific SQL assumptions. |
| HPOS | Supported | Use WooCommerce CRUD APIs; declare only after HPOS and legacy storage tests pass. |
| React admin | WordPress/WooCommerce-supported React extension APIs | React for presentation; PHP/domain services authoritative. Toolchain choice waits for Gate D. |
| WPML | Optional compatibility target | Translate plugin-owned strings, test latest stable stack; do not require it or alter order values. |
| Polylang | Optional compatibility target | Translate plugin-owned strings; test Polylang for WooCommerce only if installed; do not require it. |
| Memory | 256 MB WooCommerce recommendation | Bounded imports/jobs; document file-size limits and avoid unbounded data loads. |

WPML and Polylang are compatibility targets, not plugin dependencies. They must not duplicate settings or change numeric costs based on display language. Currency conversion is out of scope unless designed as its own feature.

Official references:
- [WordPress requirements](https://wordpress.org/about/requirements/)
- [WooCommerce server recommendations](https://woocommerce.com/document/server-requirements/)
- [WooCommerce HPOS extension recipe book](https://developer.woocommerce.com/docs/features/orders/high-performance-order-storage/recipe-book/)
- [WPML minimum requirements](https://wpml.org/home/minimum-requirements)
- [Polylang for WooCommerce compatibility changelog](https://polylang.pro/polylang-for-woocommerce-changelog/)
- [React-powered WooCommerce admin pages](https://developer.woocommerce.com/docs/extensions/settings-and-config/working-with-woocommerce-admin-pages/)

## Out of scope for first release

- Checkout rate calculation or changing customer shipping prices.
- Replacing carrier label, tracking or fulfilment systems.
- Full business profit-and-loss, COGS, gateway fee, tax, inventory or ad-spend suite.
- Automatic recommendations that edit shipping settings.
- Runtime AI analysis or order-data transmission to LLMs.

## Product acceptance rules

1. Define metric denominator, currency, statuses, refunds and missing-data behavior.
2. Actual cost must come from manual confirmation, final invoice evidence, or be clearly identified as booked/estimated.
3. Imports are previewable, auditable, idempotent where possible and correctable.
4. Tables are paginated/server-filtered; React never loads the entire store history.
5. Missing costs are coverage gaps, never silently treated as zero.
