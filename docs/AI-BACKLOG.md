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
| AI-17 | Plan WordPress.org Free and WooCommerce.com Paid submissions | Strategy drafted; submission mechanics need confirmation | SUBMISSION-STRATEGY.md has both tracks; confirm whether Woo Marketplace requires a separate Free submission for merged freemium listing |\n| AI-18 | Implement initial currency-aware dashboard summary | Implemented; runtime verification pending | Read-only paginated summary for Processing/Completed orders with WooCommerce shipping lines; separates order currencies and evidence, displays coverage and comparable variance, supports date ranges up to 366 days, and subtracts allocated shipping refunds. No tests or compatibility claims. |\n| AI-19 | Capture cost tax basis and add report controls | Implemented; runtime verification pending | Order editor records unknown/tax-exclusive/tax-inclusive basis. Dashboard compares only confirmed costs with matching currency and tax-exclusive basis; invalid or over-366-day ranges fall back to default 30 days with a notice. |\n| AI-20 | Export a privacy-minimized report CSV | Implemented; runtime verification pending | Capability- and nonce-protected admin-post export follows the selected date range, paginates WooCommerce orders, includes allocated shipping refunds, excludes customer PII/freeform notes, and marks currency/tax/evidence comparison status; text fields are guarded against spreadsheet formula prefixes. |\n| AI-21 | Complete core implementation phase and confirm deferred boundaries | Complete (code delivery; runtime verification pending) | Free core is manual order ledger, date/currency/tax-aware reports and CSV export. Pro invoice import/reconciliation and live carrier APIs move to Phase 6 because invoice samples, data-retention review and credential security gates remain open. |

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

The owner authorized the initial manual/no-API implementation and approved the documented UX baseline on 2026-11-06. The implementation is constrained to WordPress-bundled React with no CLI, package manager, bundler or build pipeline. The manual order-cost slice and dashboard summary/report controls are implemented but have not been runtime-verified. Summary limitation: shipping-refund allocation is not implemented. Report CSV export is implemented; invoice CSV import remains gated on anonymized merchant samples and retention review. Variance is restricted to same-currency, confirmed, tax-exclusive costs. The planned release channels are WordPress.org Free and WooCommerce.com Paid; the dual-submission mechanics need vendor confirmation. API credentials, CSV persistence, compatibility claims and tests remain out of scope until their evidence/review gates are met.
