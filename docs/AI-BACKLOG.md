# AI-assisted development backlog and working protocol

This backlog is for ChatGPT, Cursor, Claude, Gemini, and human contributors. It is a shared project plan, not authorization for an agent to invent scope or skip review.

## Rules for every contributor

1. Read README.md and the task-relevant documents in docs/ before proposing or changing code.
2. Treat `docs/PRODUCT-SPEC.md` as the single feature bank; check feature IDs before adding options.
3. Treat `docs/ARCHITECTURE.md` and `docs/ARCHITECTURE-DECISIONS.md` as the system boundary and proposed decisions; raise conflicts instead of silently overriding them.
4. Initial implementation is authorized within OWNER-DECISION-REGISTER.md. Do not add CLI, package manager, bundler or build pipeline; use WordPress-bundled React only. Do not claim tests or compatibility verification that were not performed.
5. Mark work complete only with evidence. Distinguish public desk research from account-level API verification and merchant validation.
6. Do not claim compatibility, Marketplace acceptance, provider access, final invoice data or test results without evidence.
7. Report changed files, decisions, assumptions, evidence and unresolved items.

## Backlog

| ID | Work item | Status | Exit evidence |
|---|---|---|---|
| AI-01 | Competitor feature matrix from primary listings/docs | Complete (desk review) | Overlaps and plausible differentiation recorded in MARKET-RESEARCH.md; no exclusivity claim |
| AI-02 | Validate merchant workflow and invoice CSV examples | Needs merchant inputs | Three anonymized invoice schemas and edge cases; request files only with sensitive data removed |
| AI-03 | Approve Free/Pro boundary and MVP acceptance criteria | MVP boundary accepted; pricing/merchant validation pending | Decision-ready MVP, acceptance criteria and settings map in PRODUCT-SPEC.md |
| AI-04 | Define metrics: charged, actual/confirmed cost, variance, coverage, refunds, currency | Complete (proposed metric contract) | Metric glossary, comparable-cost rules and examples/guardrails in PRODUCT-SPEC.md |
| AI-05 | Decide storage for cost snapshots, charge rows and audit history | Initial order-cost storage implemented; full schema pending | Manual order values use WooCommerce CRUD/order metadata and private order notes for change history; no custom tables or credential storage. Revisit CSV evidence schema after sample invoices and retention decision. |
| AI-06 | Map React admin screens, navigation and all screen states | UX baseline approved; implementation in progress | UX-FLOWS.md defines workflow states; initial React dashboard shell is in assets/js/admin.js and assets/css/admin.css |
| AI-07 | Platform compatibility baseline | Complete (dated proposal; verification pending) | PHP/WP/WC/database minimum and WPML/Polylang integration test matrix in COMPATIBILITY-BASELINE.md; not a plugin support claim |
| AI-08 | Security/privacy and import/API threat model | Complete (proposed controls; review pending) | Credential, endpoint, CSV, webhook, logging and data-minimization controls plus open security decisions in ARCHITECTURE-DECISIONS.md |
| AI-09 | Define reconciliation matching and review controls | Complete (proposed MVP rules) | Exact-ID precedence, exception states, ambiguity/manual review and audit behavior in PRODUCT-SPEC.md |
| AI-10 | Define reporting/performance/background jobs | Initial dashboard summary implemented; benchmark pending | Dashboard uses paginated WooCommerce order queries (100 per page) for a 30-day read-only snapshot; broader report controls and performance profiling remain open. Architecture proposal remains in ARCHITECTURE-DECISIONS.md. |
| AI-11 | Marketplace readiness checklist | Submission strategy drafted; release evidence pending | SUBMISSION-STRATEGY.md defines WordPress.org Free and WooCommerce.com Pro tracks, open freemium pairing question, licensing, QIT and vendor requirements |
| AI-12 | Pre-development exit review | Initial gates accepted with constraints | OWNER-DECISION-REGISTER.md records manual/no-API scope and no-build/no-CLI toolchain; credential, sample-data and retention items remain deferred |
| AI-13 | Carrier/platform public API capability matrix | Complete (public-doc desk review) | Region-aware evidence matrix in INTEGRATION-MATRIX.md; actual invoice endpoints/account permissions remain unverified |
| AI-14 | Verify candidate APIs with merchant accounts and account-specific docs | Deferred; outside initial build | Reopen only after merchant usage, account scopes, invoice-final fields, regional terms and security review are available |
| AI-15 | Bootstrap plugin and React admin shell without CLI/build tooling | Complete (implementation slice) | Plugin entry point, coordinator, WooCommerce submenu, truthful empty-state dashboard and scoped styles added; runtime verification not performed |
| AI-16 | Implement manual per-order cost ledger via WooCommerce CRUD | Implemented; runtime verification pending | Order editor stores amount, ISO currency, evidence class, tax basis, source note and update metadata through WC_Order CRUD; private order notes record amount/currency/evidence changes and removal; no custom order SQL. Tests and compatibility checks not run. |
| AI-17 | Plan WordPress.org Free and WooCommerce.com Paid submissions | Strategy drafted; submission mechanics need confirmation | SUBMISSION-STRATEGY.md has both tracks; confirm whether Woo Marketplace requires a separate Free submission for merged freemium listing |
| AI-18 | Implement initial currency-aware dashboard summary | Implemented; runtime verification pending | Read-only paginated summary for Processing/Completed orders with WooCommerce shipping lines; separates order currencies and evidence, displays coverage and comparable variance, supports date ranges up to 366 days, and subtracts allocated shipping refunds. No tests or compatibility claims. |
| AI-19 | Capture cost tax basis and add report controls | Implemented; runtime verification pending | Order editor records unknown/tax-exclusive/tax-inclusive basis. Dashboard compares only confirmed costs with matching currency and tax-exclusive basis; invalid or over-366-day ranges fall back to default 30 days with a notice. |
| AI-20 | Export a privacy-minimized report CSV | Implemented; runtime verification pending | Capability- and nonce-protected admin-post export follows the selected date range, paginates WooCommerce orders, includes allocated shipping refunds, excludes customer PII/freeform notes, and marks currency/tax/evidence comparison status; text fields are guarded against spreadsheet formula prefixes. |
| AI-21 | Complete core implementation phase and confirm deferred boundaries | Complete (code delivery; runtime verification pending) | Free core is manual order ledger, date/currency/tax-aware reports and CSV export. Pro invoice import/reconciliation and live carrier APIs move to Phase 6 because invoice samples, data-retention review and credential security gates remain open. |
| AI-22 | Build a browser-local courier CSV preview and column mapper | Implemented; runtime verification pending | CSV is parsed in browser only, max 5 MB/5,000 data rows; comma/semicolon/tab and decimal mark can be selected; map reference/amount/currency and preview validation. No file or invoice rows are sent/stored. |
| AI-23 | Add exact order-reference match preview | Implemented; runtime verification pending | Capability- and nonce-protected AJAX matches up to 50 mapped references to exact WooCommerce order IDs/numbers; repeated invoice refs are marked duplicate; ambiguous/unmatched remain explicit. Read-only; no cost updates or persisted rows. |\n| AI-24 | Commit explicitly approved matched invoice costs | Implemented; runtime verification pending | Capability/nonce-checked commit revalidates order ID/number and shipping-line presence, accepts non-negative decimal values plus currency/evidence/tax basis, never replaces an existing cost, writes WooCommerce CRUD metadata/private order note, and stores a row fingerprint for idempotency. Raw files and unrelated columns are not persisted. |
| AI-25 | Triage invoice preview rows by review state | Implemented; runtime verification pending | Browser-local filters group preview rows into needs-review, ready-to-save and saved states for the current batch. Decisions remain in memory and no exception resolution is persisted. |
| AI-26 | Guard invoice commits against order-currency mismatch | Implemented; runtime verification pending | Currency mismatches are explicitly surfaced as review items and excluded from bulk commit; the workflow does not convert currencies. |

## Agent task template

- **Task ID(s):** exact backlog/feature IDs.
- **Goal:** one bounded outcome.
- **Allowed files/scope:** explicit files.
- **Constraints:** compatibility, security, UI, performance, privacy and no-CLI rule.
- **Evidence required:** sources or decision record.
- **Completion report:** files changed, decisions, assumptions, unresolved items and status proposal.

## Human approval gates

- **Gate A — Product:** initial manual/no-API MVP boundary accepted; pricing and merchant validation remain open.
- **Gate B — UX:** UX-FLOWS.md accepted as the implementation baseline.
- **Gate C — Architecture:** initial WooCommerce CRUD/manual-entry boundary accepted; credentials and CSV persistence remain deferred pending security, merchant samples and retention decisions.
- **Gate D — Toolchain:** no-build React via WordPress `wp.element` accepted. CLI, package managers, bundlers and build pipelines remain prohibited until the plugin is final.
- **Gate E — Release:** verify compatibility, provider API terms/access, privacy, docs and Marketplace requirements.

## Current handoff

Phase 5 code delivery is complete. Phase 6 has delivered four of six implementation slices: browser-local CSV parsing/mapping, exact read-only order ID/number matching restricted to shipping orders, and explicit commit for rows with a unique match and no existing cost. The commit stores normalized cost fields through WooCommerce order CRUD and a row fingerprint for idempotency; it does not store the raw file or unrelated columns. Duplicate, unmatched, ambiguous, invalid, existing-cost and already-imported rows are not automatically committed. Persistent import mapping profiles, exception resolution, carrier API adapters and sync remain to be implemented. Merchant invoice format coverage is not yet validated against supplied samples; credential storage remains gated on security design. Runtime verification, compatibility checks and tests remain pending for their dedicated quality phase.