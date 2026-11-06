# WPEssential Shipping Cost Monitor

**A WooCommerce shipping-cost intelligence add-on by WPEssential.**

Find orders and delivery patterns where actual fulfilment spend is eroding margin. The proposed product combines a per-order cost ledger, courier invoice reconciliation and optional carrier API sync where reliable data exists.

> **Project status:** Phase 5 code complete; Phase 6 invoice workflow in progress  
> **Current overall progress:** `■■■■■■▋░░░ 67%` — Phase 5 code scope is complete; Phase 6 has four of six implementation slices delivered; runtime and compatibility verification remain pending; no CLI or build toolchain.  
> **Last status update:** 2026-11-06  
> **Repository:** [wpessential-shipping-cost-monitor](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor)

## Product direction

Record what customers paid for shipping and what merchants paid to fulfil shipments, then surface margin loss and mismatched charges. Connect optional carriers/platforms where APIs support useful shipment or cost evidence.

**Evidence rule:** rate quotes are estimates; label/manifest amounts are booked charges; invoice rows or explicitly reviewed merchant values may represent final cost. Do not silently treat one class as another. Public API research did not confirm final-invoice endpoints for every shortlisted provider.

## Roadmap

Dates are proposed targets. Completed dates show actual document-work completion; progress shows phase deliverables, not calendar time. Phase 3 is a written UX specification, not an interactive React prototype. Phase 4 decisions authorize the initial manual/no-API foundation, while credential security and CSV retention decisions remain open. No CLI, package manager or build system has been added.

| Phase | Status | Progress | Start | Target end | Completed | Exit outcome |
|---|---|---:|---|---|---|---|
| 1. Competitor and public API desk research | Complete (desk review) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-13 | 2026-11-06 | Competitor overlap and region-aware provider evidence matrix |
| 2. Requirements, options bank and MVP proposal | MVP boundary accepted; pricing/merchant validation pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-20 | 2026-11-06 | Deduplicated feature IDs, metric contract, settings map and testable MVP criteria documented |
| 3. UX flows and React admin specification | UX baseline approved; visual QA pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-27 | 2026-11-06 | Dashboard, cost records, invoice import, exceptions, integrations and UI states specified |
| 4. Architecture and pre-development readiness | Initial manual/no-API boundary accepted; security/retention pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-12-04 | 2026-11-06 | Storage, money/evidence model, imports, adapters/auth, security, compatibility and readiness decisions documented |
| 5. Core plugin implementation | Complete (code delivery; runtime verification pending) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2027-01-15 | 2026-11-06 | Admin shell, manual ledger, tax/refund-aware reports and privacy-minimized CSV export |
| 6. Reconciliation, API sync and Pro workflows | In progress (preview, matching, row commit + exception triage; API work remains) | `██████▋░░░ 67%` | 2027-01-18 | 2027-02-05 | — | Pro invoice CSV import/reconciliation, provider adapters and verified API workflows |
| 7. Compatibility, quality and beta | Not started | `░░░░░░░░░░ 0%` | 2027-02-08 | 2027-02-19 | — | Supported-platform/provider verification and beta feedback |
| 8. Marketplace readiness | Not started | `░░░░░░░░░░ 0%` | 2027-02-22 | 2027-03-05 | — | Free WordPress.org and Paid WooCommerce.com submission tracks, review evidence and release candidate |

**Overall project progress:** `■■■■■■▋░░░ 71%` (Phases 1–5 code delivery is complete; Phase 6 has completed four of six implementation packages). Phase 5 is 100%; Phase 6 is 67%. Runtime verification and compatibility checks are deferred to Phase 7. Tests have not been run.

## Phase 2 decision pack

Recommended first release: Free manual order-cost ledger, provenance, basic summary and CSV export; Pro universal invoice CSV mapping/matching, exception review and shipping loss reports. One or two API adapters may be selected only after account-level evidence. Read the exact metric contract, acceptance criteria and single-home settings map in [Product requirements](docs/PRODUCT-SPEC.md).

**MVP boundary accepted:** manual ledger/basic reporting/CSV export in Free and invoice reconciliation in Pro. Pricing and merchant validation remain unapproved; invoice samples are needed before CSV parsing is finalized.

## Phase 3 UX specification

[UX flows and React admin interface specification](docs/UX-FLOWS.md) defines navigation, dashboard, cost records, import wizard, exception queue, integrations, reports, settings and screen states. It is a reviewable UX document; no interactive prototype/code was created.

**UX baseline approved:** the documented flows guide implementation. The initial dashboard shell uses WordPress-bundled React through `wp.element`; detailed visual review and functional states remain in progress.

## Phase 4 architecture and readiness pack

[Architecture decisions](docs/ARCHITECTURE-DECISIONS.md) records proposed storage boundaries, money/evidence handling, matching/import lifecycle, jobs, adapters, credential security, HPOS/language compatibility, performance and privacy controls. [Pre-development readiness](docs/PREDEVELOPMENT-READINESS.md) lists the remaining decisions and gates.

**Initial implementation boundary accepted:** WooCommerce CRUD for order data; manual cost entry first; no carrier API credentials. Credential security must be reviewed before integrations, and anonymized invoices plus retention policy are needed before CSV upload/persistence. The dated [compatibility baseline proposal](docs/COMPATIBILITY-BASELINE.md) is not a support claim. See the [owner decision register](docs/OWNER-DECISION-REGISTER.md).

## Phase 5 implementation — complete code delivery

The plugin bootstrap, React admin shell, manual order-cost ledger, date-filtered reports and privacy-minimized CSV export are implemented:

- [Plugin entry point](wpessential-shipping-cost-monitor.php)
- [Plugin coordinator](includes/Plugin.php)
- [WooCommerce admin page](includes/Admin/Page.php)
- [Order cost fields and CRUD persistence](includes/Admin/OrderCostFields.php)
- [Read-only dashboard summary](includes/Admin/Summary.php)
- [Protected report CSV export](includes/Admin/CsvExport.php)
- [React dashboard and metrics](assets/js/admin.js)
- [Admin styles](assets/css/admin.css)

The order editor stores merchant-paid amount, currency, evidence type, tax treatment and source note through WooCommerce CRUD. Private order notes audit amount/currency/evidence/tax changes, source-note edits and record removal. The dashboard supports store-timezone date ranges up to 366 days, separates currencies, subtracts allocated shipping refunds and calculates variance only for confirmed, same-currency, tax-exclusive costs.

The CSV export uses capability and nonce checks, paginated WooCommerce order queries, and the selected date range. It exports order and cost evidence without customer details or freeform notes, and guards text cells against spreadsheet formula prefixes. Invoice CSV import and live carrier connections move to Phase 6 because anonymized merchant invoice samples, retention review and credential-security decisions remain open.

**Phase 5 code delivery is complete.** Runtime verification, compatibility checks and tests are pending their dedicated quality phase. No HPOS, PHP/WP/WC, WPML or Polylang support claim is made.

## Phase 6 implementation — current slices

The Pro invoice workflow now supports browser-local preview, read-only matching, and an explicit reviewed-row commit:

- [CSV preview, mapping, matching and commit UI](assets/js/admin.js)
- [Exact order-reference matcher](includes/Admin/ReferenceMatcher.php)
- [Explicit invoice row importer](includes/Admin/InvoiceImporter.php)
- [Dashboard request wiring](includes/Admin/Page.php)

The preview reads CSV files in the browser (maximum 5 MB and 5,000 data rows), supports comma/semicolon/tab delimiters and decimal-mark selection, and maps reference, amount and currency columns. The file and full invoice rows are not uploaded or persisted. Matching sends only mapped references for up to 50 valid rows. It accepts exact WooCommerce order ID/order-number matches only for standard shipping orders; duplicate, ambiguous, unmatched, and orders with an existing cost are identified without changing order data. Tracking/AWB matching is not supported.

A merchant must explicitly confirm the commit action. The server rechecks capability, nonce, order identity, shipping-line presence and editable-order permission, then saves only the normalized amount, currency, evidence, tax basis and source-row reference through WooCommerce order CRUD. Existing costs are never overwritten. A row fingerprint supports idempotent retry handling. The raw invoice file and unrelated columns are not stored; negative credit/refund rows are held for review and are not committed.

Persistent mapping profiles, durable exception resolution, carrier API adapters/sync and rate simulation remain incomplete. The preview table now filters the current in-browser batch into needs-review, ready-to-save and saved rows; it does not persist or resolve exception decisions. Invoice currencies that differ from the matched order are shown as review items and excluded from the bulk commit; no currency conversion is performed. Merchant invoice samples are needed to validate template coverage; API credentials remain gated on security design. This Phase 6 code has not been runtime-verified, compatibility-checked or tested.
## Marketplace submission plan

The target is **Free on WordPress.org** and **Paid/Pro on WooCommerce.com**. [Submission strategy](docs/SUBMISSION-STRATEGY.md) documents both tracks, package boundaries, review criteria and open Marketplace questions.

WordPress.org Free must remain useful on its own and comply with directory rules. WooCommerce.com Paid requires an approved vendor account, technical/security/UX review, HPOS support and its current QIT checks. Woo's freemium workflow may require separate Free and Paid submissions that later merge into one product page; confirm whether a WordPress.org Free package satisfies that requirement before finalizing distribution.

This repository is not a submission package. Neither channel has been submitted to, and no approval, vendor account, production ZIP or Marketplace test result is claimed.

## Planning documents

- [Market research](docs/MARKET-RESEARCH.md)
- [Carrier and platform API capability matrix](docs/INTEGRATION-MATRIX.md)
- [Product requirements and options bank](docs/PRODUCT-SPEC.md)
- [UX flows and interface states](docs/UX-FLOWS.md)
- [Systems architecture, adapters and data flow](docs/ARCHITECTURE.md)
- [Architecture decisions](docs/ARCHITECTURE-DECISIONS.md)
- [Pre-development readiness checklist](docs/PREDEVELOPMENT-READINESS.md)
- [PHP, WordPress, WooCommerce and multilingual compatibility baseline](docs/COMPATIBILITY-BASELINE.md)
- [Owner decision register for Gates A–D](docs/OWNER-DECISION-REGISTER.md)
- [WordPress.org Free and WooCommerce.com Paid submission strategy](docs/SUBMISSION-STRATEGY.md)
- [AI-assisted development backlog](docs/AI-BACKLOG.md)
- [AI start-here instructions](docs/AI-START-HERE.md)

## Product principles

1. One auditable source of shipping-cost evidence per order.
2. Keep estimates, booked charges, invoice-final charges, credits/refunds and merchant-confirmed costs distinct.
3. Read-only by default: never change checkout rates or order totals.
4. Manual entry and CSV invoices work without a connected carrier account.
5. Use WooCommerce CRUD APIs and verify HPOS compatibility.
6. Paginate server-side and queue heavy import/sync work.
7. Explain data storage and external provider transmissions.

## Free and Pro direction

**Free:** order-level manual cost entry, source/history, basic date-filtered summary, simple filters and CSV export.

**Pro:** invoice CSV mapping/reconciliation, mismatch queue, shipping loss breakdowns and selected API sync. Alerts, historical comparisons, rate scenarios, scheduled reports and broad provider coverage remain later candidates.

Pricing and final packaging await owner and merchant validation and Marketplace review.

## Scope guardrails

- No general P&L/COGS suite, label platform, tracking portal or fulfilment replacement.
- Carrier integrations are optional, adapter-based and prioritized by merchant usage, final-cost evidence, regional coverage, access terms and maintenance cost.
- No runtime AI service or customer-data transmission to LLMs.
- CLI, package managers, bundlers and build pipelines remain prohibited until the plugin is final; the initial React shell uses WordPress-bundled `wp.element`.

## For AI coding tools

Repository-aware agents should load [AGENTS.md](AGENTS.md) before acting. Tool entry points are included for Codex, Cursor, Claude Code, Gemini CLI and GitHub Copilot. General ChatGPT requires repository/file access and the prompt in [AI-START-HERE](docs/AI-START-HERE.md).
