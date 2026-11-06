# WPEssential Shipping Cost Monitor

**A WooCommerce shipping-cost intelligence add-on by WPEssential.**

Find orders and delivery patterns where actual fulfilment spend is eroding margin. The proposed product combines a per-order cost ledger, courier invoice reconciliation and optional carrier API sync where reliable data exists.

> **Project status:** Pre-development planning  
> **Current overall progress:** `■■■■■░░░░░ 50%` — desk research and four planning phases have draft deliverables; product, UX and architecture reviews remain open; no plugin code or CLI.  
> **Last status update:** 2026-11-06  
> **Repository:** [wpessential-shipping-cost-monitor](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor)

## Product direction

Record what customers paid for shipping and what merchants paid to fulfil shipments, then surface margin loss and mismatched charges. Connect optional carriers/platforms where APIs support useful shipment or cost evidence.

**Evidence rule:** rate quotes are estimates; label/manifest amounts are booked charges; invoice rows or explicitly reviewed merchant values may represent final cost. Do not silently treat one class as another. Public API research did not confirm final-invoice endpoints for every shortlisted provider.

## Roadmap

Dates are proposed targets. Completed dates show actual document-work completion; progress shows phase deliverables, not calendar time. Phase 3 is a written UX specification, not an interactive React prototype. Phase 4 is a proposed decision/readiness package, not an approval. No CLI or build system has been added.

| Phase | Status | Progress | Start | Target end | Completed | Exit outcome |
|---|---|---:|---|---|---|---|
| 1. Competitor and public API desk research | Complete (desk review) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-13 | 2026-11-06 | Competitor overlap and region-aware provider evidence matrix |
| 2. Requirements, options bank and MVP proposal | Complete (decision-ready draft; Gate A pending) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-20 | 2026-11-06 | Deduplicated feature IDs, metric contract, settings map and testable MVP criteria documented |
| 3. UX flows and React admin specification | Complete (review draft; Gate B pending) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-27 | 2026-11-06 | Dashboard, cost records, invoice import, exceptions, integrations and UI states specified |
| 4. Architecture and pre-development readiness | Complete (proposed package; Gate C pending) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-12-04 | 2026-11-06 | Storage, money/evidence model, imports, adapters/auth, security, compatibility and readiness decisions documented |
| 5. Core plugin implementation | Not started; gates required | `░░░░░░░░░░ 0%` | 2026-12-07 | 2027-01-15 | — | Order cost ledger, reports, CSV import and integration foundation |
| 6. Reconciliation, API sync and Pro workflows | Not started | `░░░░░░░░░░ 0%` | 2027-01-18 | 2027-02-05 | — | Provider adapters, exception queue, alerts and scenario analysis |
| 7. Compatibility, quality and beta | Not started | `░░░░░░░░░░ 0%` | 2027-02-08 | 2027-02-19 | — | Supported-platform/provider verification and beta feedback |
| 8. Marketplace readiness | Not started | `░░░░░░░░░░ 0%` | 2027-02-22 | 2027-03-05 | — | Documentation, packaging, review checklist and submission candidate |

**Overall planning progress:** `■■■■■░░░░░ 50%` (four of eight planning phases have draft deliverables; implementation remains 0%).

## Phase 2 decision pack

Recommended first release: Free manual order-cost ledger, provenance, basic summary and CSV export; Pro universal invoice CSV mapping/matching, exception review and shipping loss reports. One or two API adapters may be selected only after account-level evidence. Read the exact metric contract, acceptance criteria and single-home settings map in [Product requirements](docs/PRODUCT-SPEC.md).

**Still pending at Gate A:** product-owner review of the proposed scope and future commercial Free/Pro boundary. This is not represented as approved or customer-validated.

## Phase 3 UX specification

[UX flows and React admin interface specification](docs/UX-FLOWS.md) defines navigation, dashboard, cost records, import wizard, exception queue, integrations, reports, settings and screen states. It is a reviewable UX document; no interactive prototype/code was created.

**Still pending at Gate B:** owner review of the flows. React implementation/toolchain remains deferred until the relevant gates.

## Phase 4 architecture and readiness pack

[Architecture decisions](docs/ARCHITECTURE-DECISIONS.md) records proposed storage boundaries, money/evidence handling, matching/import lifecycle, jobs, adapters, credential security, HPOS/language compatibility, performance and privacy controls. [Pre-development readiness](docs/PREDEVELOPMENT-READINESS.md) lists the remaining decisions and gates.

**Still pending at Gate C:** approve the schema direction, credential key strategy and retention/uninstall behavior. A dated [compatibility baseline proposal](docs/COMPATIBILITY-BASELINE.md) now recommends a PHP/WP/WC/database baseline and WPML/Polylang verification matrix; it is not a plugin support claim. The proposed controls are not implementation or security-test evidence. No provider account/API access or compatibility test was performed.

## Planning documents

- [Market research](docs/MARKET-RESEARCH.md)
- [Carrier and platform API capability matrix](docs/INTEGRATION-MATRIX.md)
- [Product requirements and options bank](docs/PRODUCT-SPEC.md)
- [UX flows and interface states](docs/UX-FLOWS.md)
- [Systems architecture, adapters and data flow](docs/ARCHITECTURE.md)
- [Architecture decisions](docs/ARCHITECTURE-DECISIONS.md)
- [Pre-development readiness checklist](docs/PREDEVELOPMENT-READINESS.md)
- [PHP, WordPress, WooCommerce and multilingual compatibility baseline](docs/COMPATIBILITY-BASELINE.md)
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
- No CLI, build pipeline or plugin code until pre-development gates and toolchain approval are complete.

## For AI coding tools

Repository-aware agents should load [AGENTS.md](AGENTS.md) before acting. Tool entry points are included for Codex, Cursor, Claude Code, Gemini CLI and GitHub Copilot. General ChatGPT requires repository/file access and the prompt in [AI-START-HERE](docs/AI-START-HERE.md).
