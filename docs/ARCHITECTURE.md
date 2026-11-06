# Systems architecture and data flow

**Status:** Proposed architecture for pre-development; interfaces and schema are not frozen until phase 4 review.

## Design boundaries

- WordPress plugin owns admin UI registration, permissions, settings, translations, lifecycle and integration hooks.
- WooCommerce owns orders and order lifecycle. Read/write through WooCommerce CRUD APIs; avoid direct order-table assumptions so HPOS and legacy storage can both work.
- React is the admin presentation layer for the dashboard, import wizard, exception queue, reports and settings where a distinct app-like workflow helps. WooCommerce officially supports React-powered extension admin pages through its page registration APIs; React is not a reason to move business rules into the browser.
- PHP services own validation, matching, persistence, aggregation, export generation and background work.
- Optional source adapters normalize external label/courier evidence into the plugin's internal representation.
- Shipping rates are never mutated by the reporting/simulation system.

## Logical components

1. **Admin shell (React):** dashboard, filter controls, order cost editor surface, CSV mapping/preview, reconciliation queue, reports, settings.
2. **WordPress/WooCommerce bridge (PHP):** capability checks, REST/AJAX routes, nonces, WooCommerce page registration, order CRUD integration, notices.
3. **Domain services (PHP):** cost ledger, variance calculation, import validation, matching, reconciliation state machine, report queries, export formatting.
4. **Persistence:** WooCommerce order metadata for order-linked cost values; plugin-owned tables only for import batches, source rows, mappings and auditable reconciliation records where querying/order volume justifies them. Final schema decision requires volume and retention review.
5. **Background jobs:** bounded, retry-safe processing for large imports and scheduled alert/report work; no full-store scans on ordinary admin requests.
6. **Adapters:** manual entry, CSV import, and verified WooCommerce Shipping integration first; other providers behind provider-specific adapters.
7. **Compatibility layer:** feature detection for WooCommerce APIs; translations; HPOS declaration only after evidence-backed compatibility testing.

## Data flow

`Order created/updated → order CRUD service → customer-paid shipping snapshot`

`Merchant manual entry OR CSV/API source → parse → validate → normalize → preview → match → exception review → commit`

`Committed cost evidence → order cost ledger → variance service → server-side aggregates → React dashboard/reports/export`

`Scheduled threshold check → eligible reconciled records → de-duplicated alert → admin notice/email (merchant-configured)`

## Core record concepts

- **Order cost snapshot:** order ID, currency, customer-paid shipping snapshot, actual cost amount, source type, recorded/updated timestamps, record status and provenance reference.
- **Cost component:** optional parcel/cost component amount, label, currency and source reference.
- **Import batch:** source profile, upload metadata, initiating user, start/end state, totals and error summary. Do not retain original files longer than required; retention decision is configurable/documented.
- **Imported charge row:** normalized identifiers, charge date, amount/currency, matched order and match method, duplicate group, reconciliation status and source-row reference.
- **Audit event:** actor, action, before/after summary, time and related record ID. Avoid copying unnecessary customer personal data into the audit trail.
- **Mapping profile:** merchant-defined column mapping and parsing rules. Do not store credentials in mapping profiles.

## Matching and amount rules

- Exact identifiers selected by merchant (e.g. order number, tracking/AWB) are preferred.
- Fallback matching by date, recipient or amount is disabled until the merchant enables it and sees its confidence/evidence. Ambiguous rows go to review; no fuzzy auto-commit in MVP.
- Currency mismatch, missing identifier, duplicate row, refund/credit, split parcel and reship are explicit states, not hidden adjustments.
- Imported evidence is immutable source evidence; correcting the order's chosen actual cost creates a new audited decision rather than editing the source row invisibly.
- Variance definition: `customer-paid shipping − actual fulfilment cost`. Negative values indicate a shipping deficit. Taxes, discounts, refunds and other revenue definitions must be separately documented.

## Security, privacy and performance

- Check capabilities for every privileged action; use nonces for state-changing admin requests, sanitize on input and escape on output.
- Use prepared queries and bounded pagination; never accept a table/column name from request data.
- Validate MIME/size/encoding/columns for uploads; protect CSV exports against formula injection.
- Minimize imported personal data, disclose optional external service data flows, and avoid telemetry by default.
- Queue long imports; make jobs retry-safe and idempotent; surface failures and allow safe retry.
- Keep frontend bundles and API payloads small; do not hydrate entire order histories into React.
