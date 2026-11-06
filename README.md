# WPEssential Shipping Cost Monitor

**A WooCommerce shipping-cost intelligence add-on by WPEssential.**

Find orders and delivery patterns where actual fulfilment spend is eroding margin. The product is planned as a focused Free + Pro plugin, with a dependable order-level cost ledger at its core and reconciliation and decision support layered on top.

> **Project status:** Pre-development planning  
> **Current overall progress:** `■■░░░░░░░░ 15%` — research and product definition underway; no plugin code has started.  
> **Repository:** [wpessential-shipping-cost-monitor](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor)

## Product direction

The plugin records what the customer paid for shipping and what the merchant actually paid to fulfil the shipment, then explains the difference in useful ways. Its strongest product opportunity is a merchant workflow that connects courier invoice rows to WooCommerce orders, highlights unlinked or mismatched charges, and turns reconciled costs into actionable loss reports.

This is a differentiation hypothesis, not a claim that no competitor has related features. Several products already track shipping costs and profit; see [Market research](docs/MARKET-RESEARCH.md).

## Roadmap

Dates below are **proposed planning targets** and will be re-baselined after each phase. Progress measures completed project work, not elapsed time. Phase 1 is active; all future phases are queued. No development code or CLI has been added.

| Phase | Status | Progress | Start | Target end | Exit outcome |
|---|---|---:|---|---|---|
| 1. Market research & product definition | In progress | `■■■■░░░░░░ 40%` | 2026-11-06 | 2026-11-13 | Competitor map, validated differentiation, MVP boundary |
| 2. Requirements & options bank | Planned | `░░░░░░░░░░ 0%` | 2026-11-14 | 2026-11-20 | Deduplicated feature IDs, priorities, acceptance criteria |
| 3. UX flows & React admin prototype | Planned | `░░░░░░░░░░ 0%` | 2026-11-21 | 2026-11-27 | Reviewed information architecture and interface states |
| 4. Architecture & pre-development readiness | Planned | `░░░░░░░░░░ 0%` | 2026-11-28 | 2026-12-04 | Data model, integrations, security, compatibility decisions |
| 5. Core plugin implementation | Planned | `░░░░░░░░░░ 0%` | 2026-12-07 | 2027-01-15 | Order cost ledger, reports, import pipeline |
| 6. Reconciliation, alerts & Pro workflows | Planned | `░░░░░░░░░░ 0%` | 2027-01-18 | 2027-02-05 | Matching, exception queue, alerts and scenario analysis |
| 7. Compatibility, quality & beta | Planned | `░░░░░░░░░░ 0%` | 2027-02-08 | 2027-02-19 | Supported-platform verification and beta feedback |
| 8. Marketplace readiness | Planned | `░░░░░░░░░░ 0%` | 2027-02-22 | 2027-03-05 | Documentation, packaging, review checklist and submission candidate |

**Overall progress:** `■■░░░░░░░░ 15%` — planning/research only. The percentages are working estimates and will be revised against phase exit outcomes.

## Planning documents

- [Market research and competitor analysis](docs/MARKET-RESEARCH.md)
- [Product requirements and deduplicated options bank](docs/PRODUCT-SPEC.md)
- [Systems architecture and data flow](docs/ARCHITECTURE.md)
- [AI-assisted development backlog and working rules](docs/AI-BACKLOG.md)

## Product principles

1. **One source of truth:** order-level shipping cost records are auditable and preserve historical values.
2. **Reconcile before advising:** reports distinguish actual/imported costs from estimates and never silently treat an estimate as a courier invoice.
3. **Read-only by default:** the plugin does not alter checkout rates or customer order totals.
4. **Useful without a carrier account:** manual entry and CSV import are first-class paths; carrier integrations are optional adapters.
5. **Built for WooCommerce data:** use WooCommerce CRUD APIs and declare/test HPOS compatibility.
6. **Fast by design:** aggregate efficiently, paginate tables, avoid loading all orders into React, and keep heavy reconciliation off ordinary admin page loads.
7. **Clear trust boundaries:** explain what data is stored, imported, exported, or sent to an optional external service.

## Free and Pro direction

**Free:** order-level actual shipping cost entry, a basic recent-period summary, simple filters, and CSV export of recorded costs.

**Pro:** courier invoice CSV mapping and reconciliation, duplicate/missing/mismatched charge detection, zone/method/category loss breakdowns, threshold alerts, historical comparisons, rate-change scenario analysis, scheduled/exportable reports, and additional workflow automation.

This is a planning boundary. Final packaging and pricing will follow competitor review, merchant validation, and marketplace requirements.

## Current scope guardrails

- WooCommerce analytics for all business costs is outside the core scope; this product focuses on shipping cost accuracy and decisions.
- Live carrier rates, label purchasing, tracking pages, and fulfilment management are not the product's core.
- Automatic carrier APIs will be prioritized only after a stable adapter contract and customer demand are established.
- “AI” means AI-assisted product development and engineering discipline at this stage. No AI service, customer data transmission, or runtime AI feature is assumed.
- No CLI, build pipeline, or plugin code is part of this pre-development work. Any future toolchain decision is deferred until the product and UI architecture are approved.
