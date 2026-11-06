# Product requirements and options bank

**Status:** Initial MVP boundary accepted; pricing, merchant validation and distribution mechanics pending  
**Product:** WPEssential Shipping Cost Monitor  
**Parent suite:** WPEssential  
**Audience:** WooCommerce merchants comparing customer-paid shipping with fulfilment spend.

## Problem statement

WooCommerce records what the customer paid for shipping, but merchants may pay a different amount to couriers. Differences can be hidden across orders, invoices, shipping methods, locations, surcharges, split parcels, returns and adjustments. The plugin should make shipping-cost evidence recordable, reconcile that evidence, and show where shipping margin is being lost.

## Target outcomes

- Show customer-paid shipping, selected actual/confirmed cost, evidence type and variance per eligible order.
- Match invoice/API charge evidence to orders using identifiers, with visible match basis and review.
- Surface duplicate, unmatched, ambiguous and materially different charges without silently overwriting merchant data.
- Report recurring shipping loss patterns by period and supported shipping dimensions.
- Remain useful with manual entry and invoice CSV even without carrier API access.
- Automate shipment/cost evidence sync where APIs support it, while keeping quotes, booked charges and final billed costs distinct.

## Proposed MVP decision pack

**This is a product proposal, not a record of owner approval or customer validation.**

### Free

- Manual actual shipping cost per WooCommerce order.
- Source and audit history, separating customer-paid shipping, recorded cost and variance.
- Date-filtered summary with order count, shipping charged, recorded costs, known variance and coverage rate.
- Basic filters and CSV export.
- Optional WooCommerce Shipping label-cost import only after its available order data path is verified.

### Pro

- Generic courier invoice CSV upload, column mapping, preview and validation.
- Exact-ID matching (order number, tracking/AWB, or provider shipment ID) with visible match basis.
- Reviewable exception queue for duplicate, unmatched, ambiguous and mismatched rows.
- Shipping variance reports by method and WooCommerce zone where source data exists.
- One or two API adapters chosen after beta merchant usage and account-level endpoint verification. Initial API sync may record shipment identifiers/booked label amounts; final billing automation requires verified invoice-final data access.
- Pro remains useful without a carrier API because CSV is the cross-provider final-cost path.

### Defer until evidence supports them

- Integrating every carrier, rate shopping, label purchase, tracking portal or fulfilment automation.
- Scheduled alerts, period comparisons, PDF/email reports and rate-change simulator.
- Fine-grained per-parcel costing if order-total reconciliation satisfies first user workflows.
- Runtime AI analysis or sending customer/order data to LLMs.

Pricing and final Free/Pro entitlement await merchant validation and Marketplace review.

## Distribution and submission plan

**Target:** submit the Free edition to the WordPress.org Plugin Directory and the Paid/Pro edition to the WooCommerce.com Marketplace. See [SUBMISSION-STRATEGY.md](SUBMISSION-STRATEGY.md) for the full release tracks and current public requirements.

- Free must be a stable, useful standalone plugin; do not make it a trial or disable existing Free functionality behind payment.
- Pro is a separate paid release/package through WooCommerce.com. Final decision on standalone vs add-on packaging depends on Marketplace guidance and a safe upgrade/migration design.
- WordPress.org Free must not download/install executable Pro code from an external server; distribution and updates follow each destination's approved flow.
- Keep shared product IDs, names, versioning, license compatibility and migration behavior aligned. Do not claim either directory/Marketplace approval until received.
- Woo Marketplace's current freemium submission flow may require separate Free and Paid submissions that are later merged. Confirm whether the WordPress.org Free package can satisfy its Free submission requirement, or whether Woo.com needs a separate Free submission too.
- The submission tracks must meet their different readme, licensing, compatibility, security, UI, QIT, vendor-account and support requirements. Recheck exact rules at release.

## Metric definitions

These definitions are the single reporting contract; use them across dashboard, order detail, exports and help text.

| Metric | Definition for MVP | Guardrail |
|---|---|---|
| Customer-paid shipping | Sum of WooCommerce shipping line amounts, excluding shipping tax by default, less refunds explicitly allocated to shipping. | Show shipping tax separately if available. Never silently mix tax-inclusive and tax-exclusive amounts. |
| Recorded fulfilment cost | Sum of current selected cost evidence for eligible orders, using the source's recorded currency and configured tax treatment. | Keep quote, booked/label, invoice-final, adjustment and merchant-confirmed classes distinct. Only invoice-final or explicitly merchant-confirmed evidence counts as “actual/confirmed” in the primary KPI. |
| Shipping variance | Comparable customer-paid shipping minus comparable selected fulfilment cost. Positive is surplus; negative is deficit. | Suppress the numeric comparison or flag “not comparable” if currency/tax basis differs or evidence is only an estimate. |
| Cost coverage | Eligible orders with invoice-final or merchant-confirmed cost ÷ all eligible orders in the selected report period. | Show numerator and denominator; missing cost is unknown, never zero. Booked/estimated evidence can have a separate coverage count. |
| Eligible order | Non-virtual order with a shipping line and a status included by the report status filter; default statuses are Processing and Completed. | Cancelled, Failed, Draft and Pending orders are excluded by default. Merchant-selected custom statuses are shown explicitly. |
| Report period | Order creation date in the store timezone for MVP. | State the selected date range/timezone. Shipment-date reporting is later unless a reliable shipment date exists. |
| Currency aggregation | Calculate within each order currency and show separate currency groups. | No implicit FX conversion or cross-currency sum in MVP. |
| Zone breakdown | Group by a shipping-zone snapshot/source reliably available for that order. | If historical zone cannot be established, use “Zone unavailable”; never infer from a current zone rule and present it as historical fact. |
| Credit/refund | Preserve original courier charge and attach a separate linked negative adjustment/credit; shipping refunds reduce customer-paid shipping only when the refund is allocated to a shipping line. | Do not delete or overwrite the source charge. |

## MVP acceptance criteria

### SCM-ORD-01 — order cost field

- On an eligible WooCommerce order, an authorized user can save or edit a non-negative order-level cost, currency and evidence class.
- Order detail displays customer-paid shipping, selected cost, variance, source and comparison status.
- The display distinguishes booked/estimated from invoice-final or merchant-confirmed amounts.
- Values are read/written through WooCommerce order CRUD APIs.

### SCM-ORD-02 — provenance and audit

- Every create/edit/replace action stores timestamp, actor where available, source and before/after value summary.
- Source invoice rows are not silently modified when a selected order cost changes.
- Audit records avoid unnecessary customer personal data.

### SCM-IMP-01/02 — CSV import and exact matching

- User can upload a supported CSV, map required columns, select date/decimal/currency treatment, preview parsed rows and errors, then explicitly commit.
- Import supports an idempotency key derived from source batch/row identity where possible; repeat upload is detected and previewed rather than silently double-counted.
- Exact configured order/tracking/AWB/provider shipment IDs are tried first. Fallback matching is off by default.
- Every match exposes its identifier and source row. Ambiguous rows never auto-commit.
- File size/row limits are shown before processing; long imports are bounded/background work.

### SCM-IMP-03 — exception review

- Each source row has exactly one reconciliation state: Matched, Unmatched, Duplicate, Ambiguous, Amount mismatch, Credit/Refund, or Invalid.
- User can review, link, skip, or correct a row; actions create audit history and retain original source evidence.
- Partial failures show counts and row-specific reasons, with safe retry.

### SCM-REP-01/02 — reports

- Dashboard/report reuses shared date, status and currency filters rather than implementing duplicate filter settings.
- All totals expose cost coverage and separate confirmed from provisional evidence.
- Reports group only by dimensions with valid historical data; unavailable data is labelled.
- Tables are server-paginated and export respects the visible filters.
- CSV output protects formula-leading cells.

### SCM-INT-04/05/06/07 — API foundation

- Every adapter declares supported regions, authentication/scopes, shipment IDs, quote/booked/invoice/adjustment evidence, webhook/polling capability and known gaps.
- Sync is optional and reports last success, records processed, exceptions and actionable errors.
- Retries are bounded/idempotent; expired credentials and rate limits do not create duplicate costs.
- Secrets never appear in UI after saving or in logs. Disconnect/revoke guidance and provider data disclosure are available.
- A quote never becomes actual cost automatically. The user can see and review evidence class before it affects confirmed-cost reports.

## Single-home options/settings map

These are the only planned homes for configurable options. Reuse shared settings in screens rather than adding duplicate copies.

| Setting group | Canonical home | Contains | Do not duplicate in |
|---|---|---|---|
| Report filters | Shared report-filter model | Date range, order status, currency, shipping method, zone if available | Separate dashboard/exports/date widgets with conflicting defaults |
| Manual order cost | WooCommerce order detail | Amount, currency, evidence class, source/note | Global settings or another per-order metabox |
| Import profile | Imports | Column map, date/decimal format, currency and source identifier | Each report or unrelated settings tab |
| Provider connection | Integrations → provider | Auth, account label, scopes/status, connection test | Global options, order screen secret fields |
| Sync policy | Per provider connection, with one documented default | Manual/schedule/webhook mode, cadence/window, eligible statuses | A separate frequency setting per dashboard/report |
| Variance alerts | Alerts (later) | Threshold, frequency, recipient | Dashboard filter or integration settings |
| Retention/uninstall | Data & privacy | Retain/purge behavior, diagnostics opt-in | Import, integration and order settings copies |
| Free/Pro entitlement | Licensing | License state and feature access | Separate feature toggles that contradict license state |

## Feature ID policy and deduplication

Every distinct capability gets one stable ID. This table is the canonical options bank; check it before adding a new feature. A new screen must reuse existing capability/settings rather than duplicate them.

| ID | Capability | Free / Pro | Priority |
|---|---|---|---|
| SCM-ORD-01 | Order-level actual shipping cost field | Free | MVP |
| SCM-ORD-02 | Cost source and audit history | Free | MVP |
| SCM-ORD-03 | Parcel-level cost components | Pro | Later |
| SCM-IMP-01 | CSV upload with column mapping | Pro | MVP |
| SCM-IMP-02 | Match invoice rows to orders | Pro | MVP |
| SCM-IMP-03 | Reconciliation exception queue | Pro | MVP |
| SCM-IMP-04 | Reusable import template profiles | Pro | Later |
| SCM-REP-01 | Basic period summary | Free | MVP |
| SCM-REP-02 | Shipping loss breakdowns by available method/zone data | Pro | MVP |
| SCM-REP-03 | Trend and period comparison | Pro | Later |
| SCM-REP-04 | CSV report export | Free basic / Pro filtered | MVP |
| SCM-REP-05 | Print/PDF report | Pro | Later |
| SCM-ALR-01 | Variance threshold alerts | Pro | Later |
| SCM-SIM-01 | Shipping-rate what-if simulator | Pro | Later |
| SCM-INT-01 | WooCommerce Shipping cost import | Free | Conditional MVP; data path verification required |
| SCM-INT-02 | ShipStation/other provider adapters | Pro | Later; one or two selected after validation |
| SCM-INT-03 | WPML / Polylang compatibility | Free | MVP |
| SCM-INT-04 | Carrier/platform API adapter framework | Pro | MVP foundation |
| SCM-INT-05 | Automatic API sync controls | Pro | MVP foundation |
| SCM-INT-06 | API connection/security management | Pro | MVP foundation |
| SCM-INT-07 | API evidence classification | Pro | MVP |
| SCM-SET-01 | Retention and uninstall controls | Free | MVP |
| SCM-SET-02 | Permissions, privacy and diagnostics | Free | MVP |

No second option is defined elsewhere: acceptance criteria describe behavior of these IDs; settings map defines their single UI home.

## Provider priority

Public desk research shortlist: UPS, FedEx, DHL Express; Aramex, Emirates Post and iMile for GCC; TCS, PostEx and Leopards for Pakistan/South Asia; plus WooCommerce Shipping and ShipStation as existing platforms. See [carrier/API evidence matrix](INTEGRATION-MATRIX.md). Select one or two API adapters only after beta usage and account-level proof of IDs/cost fields.

## Platform and compatibility baseline

Initial policy based on official recommendations checked 2026-11-06. Recheck at release.

| Platform | Initial baseline | Compatibility policy |
|---|---|---|
| PHP | 8.1 minimum; 8.3+ recommended | Test supported versions at release; Marketplace requires current published PHP minimum and strongly recommends 8.3+. |
| WordPress | 7.0+ | Follow WooCommerce baseline; test current stable and an earlier supported version at release where viable. |
| WooCommerce | 11.2+ development baseline | Recheck release-time latest two major WooCommerce releases for Marketplace support; this baseline is not a compatibility claim. |
| Database | MySQL 8.0+ or MariaDB 10.6+ | Avoid version-specific SQL assumptions. |
| HPOS | Supported | Use WooCommerce CRUD APIs; declare only after HPOS and legacy storage tests pass. |
| React admin | WordPress/WooCommerce-supported React extension APIs | React for presentation; PHP/domain services authoritative. Toolchain choice waits for Gate D. |
| WPML | Optional compatibility target | Translate plugin-owned strings, test latest stable stack; do not require it or alter order values. |
| Polylang | Optional compatibility target | Translate plugin-owned strings; test Polylang for WooCommerce only if installed; do not require it. |
| Memory | 256 MB WooCommerce recommendation | Bounded imports/jobs; document file-size limits and avoid unbounded data loads. |

WPML and Polylang are compatibility targets, not plugin dependencies. Currency conversion is out of scope unless designed as its own feature.

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

1. Missing costs are coverage gaps, never silently treated as zero.
2. Imported values preserve source and evidence class.
3. Use matching identifiers only according to the documented priority; ambiguity requires review.
4. Reuse one canonical setting per options map.
5. No API connector is listed as automatic final-cost integration until provider/account evidence supports that claim.
