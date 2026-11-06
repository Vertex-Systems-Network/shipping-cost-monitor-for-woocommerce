# Systems architecture and data flow

**Status:** Proposed architecture for pre-development; interfaces and schema are not frozen until phase 4 review.

## Design boundaries

- WordPress plugin owns admin UI registration, permissions, settings, translations, lifecycle and integration hooks.
- WooCommerce owns orders and order lifecycle. Read/write through WooCommerce CRUD APIs; avoid direct order-table assumptions so HPOS and legacy storage can both work.
- React is the admin presentation layer for the dashboard, import wizard, exception queue, reports, connector setup and settings where a distinct app-like workflow helps. WooCommerce officially supports React-powered extension admin pages through its page registration APIs; React is not a reason to move business rules into the browser.
- PHP services own validation, matching, persistence, aggregation, export generation, API orchestration and background work.
- Optional source adapters normalize external label/courier evidence into the plugin's internal representation.
- Shipping rates are never mutated by the reporting/simulation system.

## Logical components

1. **Admin shell (React):** dashboard, filters, order cost editor surface, CSV mapping/preview, reconciliation queue, reports, provider connection wizard, sync history and settings.
2. **WordPress/WooCommerce bridge (PHP):** capability checks, nonces, REST routes, WooCommerce page registration, order CRUD integration, notices and background scheduling.
3. **Domain services (PHP):** cost ledger, variance calculation, import validation, matching, reconciliation state machine, report queries, export formatting and sync orchestration.
4. **Provider adapter contract:** each carrier/platform adapter declares its supported features (shipments, label charges, final invoices, credits, webhooks), auth method/scopes, regional/account limitations and normalization rules. Unsupported capabilities must be explicit; do not infer a feature from a provider brand.
5. **Persistence:** WooCommerce order metadata for order-linked cost values; plugin-owned tables only for import/sync batches, source rows, mappings and auditable reconciliation records where querying/order volume justifies them. Final schema decision requires volume and retention review.
6. **Background jobs:** bounded, retry-safe processing for large imports and scheduled API sync/alerts; no full-store scans on ordinary admin page loads.
7. **Adapters:** manual entry and CSV first-class; verified WooCommerce Shipping integration and provider API adapters added in priority order.
8. **Compatibility layer:** feature detection for WooCommerce APIs; translations; HPOS declaration only after evidence-backed compatibility testing.

## Data flow

`Order created/updated → order CRUD service → customer-paid shipping snapshot`

`Manual entry OR CSV/API source → authenticate/fetch → parse → validate → normalize → preview/match → exception review → commit`

`Committed cost evidence → order cost ledger → variance service → server-side aggregates → React dashboard/reports/export`

`Scheduled sync or provider webhook → idempotency check → bounded queue → adapter → normalized evidence → match/review/commit`

`Scheduled threshold check → eligible reconciled records → de-duplicated alert → admin notice/email (merchant-configured)`

## Core record concepts

- **Order cost snapshot:** order ID, currency, customer-paid shipping snapshot, actual cost amount, evidence class, source type, recorded/updated timestamps, record status and provenance reference.
- **Cost component:** optional parcel/cost component amount, label, currency and source reference.
- **Import/sync batch:** source profile, connection ID, sync window, initiating user or schedule, start/end state, totals, cursor and error summary. Do not retain original files or API payloads longer than required.
- **External charge row:** normalized provider shipment/charge identifiers, charge date, amount/currency, evidence class, matched order and match method, duplicate group, reconciliation status and source reference.
- **Connection record:** provider identifier, non-secret account label, encrypted credentials/token reference, granted scopes, expiry/refresh metadata and status. Never log or display secrets. Encryption key-management and disconnect/revocation behavior are phase-4 decisions.
- **Audit event:** actor, action, before/after summary, time and related record ID. Avoid copying unnecessary customer personal data into the audit trail.
- **Mapping profile:** merchant-defined column mapping and parsing rules. Do not store credentials in mapping profiles.

## Evidence classes and matching

- **Rate quote:** estimate only; never actual cost automatically.
- **Label/manifest amount:** booked/label evidence; may be provisional until billed.
- **Invoice-final charge:** final billed evidence if provider source semantics establish it.
- **Credit/refund/adjustment:** separate negative or adjustment evidence linked to its original shipment/charge.
- **Merchant-confirmed:** explicit human review selecting an amount as current actual cost.
- Exact identifiers selected by merchant (e.g. order number, AWB/tracking or provider shipment ID) are preferred.
- Fallback matching by date, recipient or amount is disabled until explicitly configured and evidence/confidence is shown. Ambiguous rows go to review; no fuzzy auto-commit in MVP.
- Currency mismatch, duplicate row, split parcel, return-to-origin, reship and partial sync are explicit states, not hidden adjustments.
- Imported evidence is immutable source evidence; correcting the order's chosen actual cost creates a new audited decision rather than invisibly editing the source row.

## API connection and sync rules

- Each adapter owns authentication, token refresh, API versioning, provider-specific pagination, rate limits, webhook verification and error translation behind a shared interface.
- Request only required scopes. Explain provider data sharing and connection prerequisites before credential submission; support disconnect and revoke guidance.
- Use scheduled/manual sync where supported; use signed webhooks only where the provider offers them. Make retries idempotent and bounded with backoff; record cursor/window and partial failures.
- Store secrets using an explicit encryption/key strategy chosen at architecture review; redact auth headers, tokens, personal data and raw payloads from logs.
- Show users last sync time, source, rows processed, exceptions and actionable failures. Never state “connected/synced” without a provider-confirmed result.
- API availability, account approval, geography and final-charge access are adapter-specific—not global assumptions.

## Security, privacy and performance

- Check capabilities for every privileged action; use nonces for state-changing admin requests, sanitize on input and escape on output.
- Use prepared queries and bounded pagination; never accept a table/column name from request data.
- Validate MIME/size/encoding/columns for uploads; protect CSV exports against formula injection.
- Minimize imported personal data, disclose provider data flows, and avoid telemetry by default.
- Queue long imports and API syncs; make jobs retry-safe and idempotent; surface failures and allow safe retry.
- Keep frontend bundles and API payloads small; do not hydrate entire order histories into React.
