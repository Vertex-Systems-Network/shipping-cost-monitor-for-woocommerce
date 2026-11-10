# Architecture decisions — Phase 4

**Status:** ADR-08 is implemented for ShipStation at source level; other ADRs retain their stated proposal/verification states. This is not independent security approval.  
**Date:** 2026-11-06

## Decision summary

| ID | Area | Proposed decision | State |
|---|---|---|---|
| ADR-01 | WooCommerce order linkage | Store the selected per-order cost snapshot using WooCommerce order CRUD/meta APIs; never read/write order storage through direct SQL. | Proposed |
| ADR-02 | Supporting records | Keep normalized invoice/source rows, import batches, mapping profiles and reconciliation/audit events in plugin-owned tables when needed for cross-order queries. Use WooCommerce schema/version-aware table creation and indexes; avoid foreign keys to WooCommerce tables. | Proposed; schema needs volume review |
| ADR-03 | Money and currency | Persist decimal amounts as canonical decimal strings with explicit ISO currency and tax/charge basis; perform arithmetic with decimal-safe routines, never binary float accumulation. Preserve source amount separately from normalized amount. | Proposed; validate precision and currency edge cases |
| ADR-04 | Evidence precedence | Keep quote, booked label/manifest, invoice-final, credit/refund/adjustment and merchant-confirmed evidence distinct. Quotes never count as actual cost; final status requires source semantics or explicit merchant confirmation. | Proposed |
| ADR-05 | Matching/import | Prefer exact merchant-selected order/shipment identifiers. Ambiguous, duplicate, split, currency-mismatched and unmatched rows require review. Validate and preview before commit; retries use stable source identity for idempotency. | Proposed |
| ADR-06 | Jobs | Use WooCommerce Action Scheduler when available for bounded import/sync batches, with idempotency, locks, retry limits/backoff and visible partial failures. No long work in an admin request. | Proposed; verify availability/version support |
| ADR-07 | Carrier adapters | Define capability-declared adapters with provider-specific auth, scopes, regions, pagination, limits, webhook verification and evidence semantics. Begin with manual/CSV; promote APIs only after account-level proof. | Proposed |
| ADR-08 | Credentials | ShipStation V2 API key is probed read-only, stored only as AES-256-GCM ciphertext in a non-autoload option, and decrypted using a key derived from non-default WordPress AUTH_KEY and SECURE_AUTH_KEY values. Missing/default salts fail closed; salt rotation requires reconnect; disconnect deletes ciphertext. No plaintext fallback or secret logging. | Implemented for the adapter; independent security review, key-rotation recovery review and merchant runtime test remain open |
| ADR-09 | REST/admin security | Enforce capability checks on every privileged endpoint, nonce/REST authentication appropriate to context, strict input validation, output escaping, prepared SQL and least-privilege provider scopes. | Proposed; requires implementation review |
| ADR-10 | HPOS | Use WooCommerce CRUD/query APIs and declare HPOS compatibility only after supported-version test evidence; test legacy order storage too. | Requirement; verification pending |
| ADR-11 | Languages | Keep UI strings translatable and separate from monetary/order data. Treat WPML and Polylang as optional compatibility targets; avoid hard dependency and verify admin strings, exports and localized dates/currencies. | Proposed; verification pending |
| ADR-12 | Query/performance | Bound all list/report requests with date filters, pagination and indexed plugin-table queries. Aggregate on the server. Avoid full-store scans on page load; benchmark before claiming scale. | Proposed; benchmark pending |
| ADR-13 | Privacy/retention | Minimize recipient/customer data in imports and audit events. Define retention, export/delete behavior, uninstall policy, provider disclosure and raw-file/payload deletion before release. | Gate C/E decision required |
| ADR-14 | CSV/webhook safety | Validate file size/type/encoding and mapped fields; neutralize spreadsheet formula-leading cells in exports. Verify webhook signatures and timestamps where available, reject replay, and never expose internal endpoints to arbitrary URLs. | Proposed; implementation review required |

## Data lifecycle proposal

1. Capture customer-paid shipping from the order via WooCommerce CRUD; preserve the order currency and snapshot time.
2. Receive manual, CSV or provider evidence into a staging batch. Keep only necessary source fields and a provenance reference.
3. Validate amount/currency/date/identifiers and map columns; show a preview with rejected and ambiguous row reasons.
4. Match exact identifiers first. Do not automatically commit fuzzy recipient/date/amount matches.
5. Commit accepted evidence idempotently, then update the selected order-cost decision with an audit reference. Keep source evidence and merchant decision distinguishable.
6. Aggregate only comparable currency and basis. Report coverage and excluded/unresolved rows beside totals.
7. Delete temporary upload bytes and raw payloads after the approved retention window; retain only the minimum normalized/audit data needed for merchant records.

## Core data model proposal

- **Order cost snapshot:** WooCommerce order ID, customer-paid shipping amount/currency/basis, selected actual amount/currency/basis, evidence class, status, source reference and timestamps.
- **Charge evidence row:** provider/source key, shipment/charge identifiers, source and normalized amount/currency, evidence class, charge date, match method, linked order ID, duplicate group and review state.
- **Import batch:** source profile, uploader/connection ID, start/end, state, row counts, cursor/window and redacted error summary.
- **Mapping profile:** column mapping, locale/date/decimal parsing and identifier rules; no secrets.
- **Connection:** provider, non-secret account label, credential reference/ciphertext only after security approval, scope, expiry and connection state.
- **Audit event:** actor, action, timestamp, related record and concise before/after decision summary; avoid customer PII.

The exact schema, indexes, migration strategy, cleanup and uninstall behavior remain Gate C decisions. Plugin tables must use WordPress database upgrade conventions; do not assume a foreign key into WooCommerce order tables.

## Failure and recovery rules

- Validation errors remain in staging with row-level reasons; a failed batch must not partially appear as a completed import.
- A retry with the same source identity must not duplicate evidence or audit entries.
- Partial provider sync must show the successful window and failed cursor/window; resume safely from a checkpoint.
- Expired/revoked credentials stop sync and prompt reconnection; logs redact tokens and personal fields.
- Reconciliation must be reversible through a new audited decision, not destructive source-row mutation.

## Gate C review questions

1. Approve the proposed hybrid storage boundary and the exact minimum schema after representative order/CSV volume assumptions are supplied.
2. Approve decimal precision, tax/fee basis and multi-currency comparability rules against WooCommerce store configuration.
3. Select credential encryption/key lifecycle and fail-closed behavior after security review.
4. Confirm Action Scheduler support range and job ownership/cleanup semantics.
5. Confirm retention, privacy export/delete and uninstall policy.
6. Confirm the supported PHP, WordPress, WooCommerce and database version matrix before implementation.

## Evidence limits

This file records architecture proposals. It does not prove provider API access, encrypted credential implementation, security review, HPOS compatibility, WPML/Polylang behavior, performance, or runtime correctness. Those require account access, human decisions and implementation-level verification.
