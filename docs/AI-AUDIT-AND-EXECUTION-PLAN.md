# AI audit and execution plan

**Audit date:** 2026-11-11  
**Repository:** `Vertex-Systems-Network/wpessential-shipping-cost-monitor`  
**Purpose:** Give ChatGPT, Claude, Gemini, Cursor, Copilot and human contributors one unambiguous, evidence-led handoff for continuing the product. This document repairs task routing and status references. Phase 6 implementation is marked complete; compatibility, security review, provider-account runtime validation, beta and release remain unverified.

## Current verified snapshot

| Area | Current evidence | Boundary |
|---|---|---|
| Product scope | WooCommerce shipping-cost ledger, invoice reconciliation and optional carrier evidence; free and paid distribution targets are documented. | Pricing, merchant validation and final freemium packaging mechanics remain open. |
| Implementation | Phase 6 has 6 of 6 implementation packages (100%): read-only ShipStation V2 adapter, encrypted credentials, bounded label sync, and review/match/explicit commit handoff are in source. | Runtime connection/sync with an eligible merchant API key and independent security review remain pending. `shipment_cost` is booked-label evidence, not final carrier invoice cost. |
| CI artifact | Latest source changes include `e1c0621` repeated-header labels, `fd81d99` build-specific asset cache keys, and `e08f91b` duplicate-order preflight protection for invoice commits. Actions run #42837923258 passed PHP 8.1–8.4 Composer/autoload/lint and ZIP packaging; artifact ID 13773738796, archive digest `sha256:599780a23c4a83750b4c6b2fd58e8ed4ca2499cf493d3dc27081c2a635415fbd`, installable ZIP SHA-256 `a232d9d5ca84b5e6f83e51a2840dcf4f60d4a89fa2bd5cff487ab22e96467922`. The workflow does not execute JavaScript. Staging build ID `e08f91b6af9d` installed and rendered; two references `# 51` and `51` were blocked as duplicate before commit. The server duplicate guard is source/CI verified but was not directly called because the UI blocked the rows. Prior `fd81d99` staging flows verified repeated-header labels and malformed trailing-quote rejection. Earlier TasteWP runtime artifact build remains `9a7ae7e35951384eba21213cf390baa247e6765e`. | CI does not prove WordPress runtime compatibility. |
| TasteWP site | On 2026-11-10, build ID `9a7ae7e35951` was visible after install/activation on a separate TasteWP site (WP 7.1.4, WooCommerce 11.2.2). Dashboard rendered; 3 synthetic products were imported and there were 0 eligible orders. | This verifies install identity and dashboard shell only; no report calculation, order workflow, environment PHP/database fingerprint or compatibility matrix was exercised. |
| wpessential.org staging | Separate historical environment: WP 7.1.4, WooCommerce 11.2.2, PHP 8.3.36, MySQL 8.0.46, HTTPS, HPOS selected. Synthetic orders #51/#52 supported selected report, manual cost, CSV preview/match/commit, export and no-overwrite observations. On 2026-11-10, artifact `e08f91b` was replaced and active on this staging site; dashboard build ID `e08f91b6af9d` rendered and retained its PKR 750/PKR 1,950/PKR -1,200, 2/2 synthetic report. A duplicate invoice preview containing references `# 51` and `51` classified both as duplicates before match/commit; no order was modified. Previous artifact `fd81d99` verified repeated-header labels and malformed trailing-quote rejection. | Selected synthetic flows only. Do not attribute these orders/results to TasteWP or call them broad runtime coverage. |
| Phase 7 | Canonical gate register: 7 narrow passes, 2 partial and 5 pending. | Counts are heterogeneous and not a readiness percentage. Current exact gates and evidence live only in `PHASE-7-GATES.md`. |
| Automated tests | No repository `tests/` suite or WordPress integration harness was found in the audited tree. CI currently covers Composer PSR-4 generation, PHP lint/autoload smoke and artifact packaging. | Do not say automated functional/regression tests passed. Manual browser/staging observations are identified individually. |
| Integrations | Public API research is recorded; no live carrier integration/account credentials are enabled. | Quotes, labels and booked charges are not proof of invoice-final cost. Provider work stays blocked until its evidence/security gates are met. |

## Audit findings and repairs required

| Finding | Risk | Canonical repair |
|---|---|---|
| README, Phase 7 register, backlog and pre-development summary contain stale or conflicting current status. | An AI may repeat a completed build-ID check or overstate runtime evidence. | Apply the environment-specific corrections in this document; then keep each source aligned. |
| Old audit chronology contains retired gate counts and pre-hardening snapshots. | Older paragraphs can be mistaken for current state. | Preserve dated history, add a current-authority note and append the new evidence; do not rewrite history as though the check happened earlier. |
| Test sites have different roles and evidence. | TasteWP package smoke may be conflated with wpessential.org order-report evidence. | Always name the site, build, environment fingerprint, fixture and exact flow for each runtime claim. |
| Source/static checks are stronger than runtime and independent review. | Security, compatibility and data correctness could be overclaimed. | Keep P7-08 through P7-14 open as specified until their exit evidence is attached. |
| Active work is not consistently routed to one next task, and no performance task is currently defined as a backlog row. | Contributors can loop on blocked staging checks or cite a missing AI-39. | Use the ordered queue below; add AI-39 with the exact P7-13 benchmark acceptance criteria. |
| Product proposal can be confused with delivered code. | Zone/category analytics, alerts, simulation and carrier automation may be promised prematurely. | Use the feature-status distinction below and the Product Spec as the canonical feature bank. |

## Source-of-truth order

1. `OWNER-DECISION-REGISTER.md` — accepted authority, deferrals and owner-only decisions.
2. `PRODUCT-SPEC.md` — canonical feature IDs, Free/Pro options and acceptance criteria.
3. `ARCHITECTURE.md` and `ARCHITECTURE-DECISIONS.md` — approved system/data boundaries and proposals.
4. `AI-BACKLOG.md` — implementation task queue and task-level evidence.
5. `PHASE-7-GATES.md` — sole authority for Phase 7 gate status/closure.
6. `README.md` — concise current status and roadmap mirror; not a competing evidence ledger.
7. `PHASE-1-6-DEEP-AUDIT.md` and dated notes — historical audit trail; older statements are superseded where newer evidence is linked.

If sources conflict, do not choose a convenient status. Follow this order, fix the stale lower-level reference, and record the change in README when current state changes.

## Product boundary: proposed vs implemented

**Observed implementation slices:** order-level cost entry via WooCommerce CRUD; reporting for recorded cost and customer-paid shipping with currency/evidence guardrails; browser-local CSV preview/mapping and reviewed exact-reference commit; duplicate/unmatched/invalid/refund review states; CI-generated PSR-4 autoloader and traceable ZIP.

**Not established as delivered:** zone/category loss analysis, scheduled threshold alerts, historical comparisons, rate-change simulation, PDF reports, per-parcel allocation, background reconciliation, live carrier synchronization, merchant-ready provider support, persistent invoice archive, or final Free/Pro entitlements. These remain proposed/deferred unless code and acceptance evidence are added.

**Architecture/privacy guardrails:** never persist raw invoice uploads or customer profile data without approved retention; do not store provider credentials before the credential-security decision; use WooCommerce CRUD/query abstractions; keep currency and tax basis comparable; never silently overwrite an existing cost; no runtime LLM/customer-data transmission is authorized.

## Ordered execution queue

**Latest staging QA update (2026-11-10):** Artifact `e08f91b` is installed on the owner-designated `wpessential.org/el-test` site. Dashboard/report remained stable and duplicate references `# 51` / `51` were blocked at preview before commit. This adds no Phase 7 gate closure; direct server-guard runtime, replay, ambiguity, security and compatibility requirements remain.

The gate register is definitive. This queue identifies the next action so a contributor can make progress without waiting on unrelated external blockers.

| Order | ID | Action and acceptance evidence | Status / stop rule |
|---:|---|---|---|
| 1 | P7-11 / AI-24 | On the existing staging lane, verify exact same AJAX commit replay returns an explicit already-imported/idempotent result, and an ambiguous duplicate order-number reference remains review-only. Save sanitized request/result evidence. Reuse fixture only if it is safe and available. | Partial. Make one focused fixture attempt. If the staging system cannot create the required duplicate number safely, record the exact constraint and proceed; do not loop or mutate production data. |
| 2 | P7-12 / AI-08 | Independent source/security review plus staging/browser malformed and oversized raw input, capability and nonce rejection probes. Record exact requests and safe responses without secrets/customer data. | Partial. Source hardening/static CI is not runtime security verification. |
| 3 | P7-09 | Exercise manual save, invoice commit, refund-adjusted report/export against HPOS and legacy storage; compare order metadata, totals and no-overwrite behavior. | Pending. Use isolated disposable fixtures; keep each datastore/environment labelled. |
| 4 | P7-08 | Run install/upgrade and core flows across the declared minimum and latest WP/WC/PHP matrix; publish actual versions. | Pending. PHP lint matrix is not a runtime version matrix. |
| 5 | P7-10 | Test WPML+WCML and Polylang+its WooCommerce add-on separately, including locale formatting, metadata and cross-language/currency boundaries. | Pending; source-only dry-runs do not close runtime support. |
| 6 | P7-13 / AI-39 | Benchmark 100, 1,000 and 10,000 eligible orders, five runs per size/workflow; capture elapsed time, PHP peak memory, query count/slow queries, CSV row count and correctness. Agree budgets only after baseline. | Pending. Do not fabricate results or block unrelated tasks while fixtures are prepared. |
| 7 | P7-14 | Run approved beta with merchants; capture consented, sanitized, reproducible feedback and resolutions. | Pending. Requires merchant participation; continue independent engineering meanwhile. |
| — | AI-31 | Live carrier adapter requires merchant/provider account evidence, shipment-level final-cost fields, scopes/terms and approved credential protection. | Blocked by external evidence/security decision; this does not block P7 QA. |

## Contributor protocol (all AI tools and humans)

1. Confirm the repository is exactly `Vertex-Systems-Network/wpessential-shipping-cost-monitor` and inspect the current default branch/head before edits. Never infer that a similarly named repo is the target.
2. Read `AGENTS.md`, this execution plan, current README status, the assigned active backlog row, and its cited source documents. Check current files rather than trusting copied chat context.
3. Select the first unblocked task above or the highest-priority unblocked backlog item. In one session, perform a concrete implementation or verification and meet its acceptance evidence where possible.
4. Check existing feature IDs before adding any option or setting. Do not duplicate controls, reports, integrations or status counters.
5. If blocked, document the exact missing fixture/permission/provider/owner decision once, its smallest unblock action, and continue the next independent task. Repeat a check only when the artifact, environment, fixture, permission or code changed.
6. Keep **implemented**, **static-verified**, **runtime-verified**, **blocked**, and **deferred** separate. Include commit/build ID, environment, fixture and exact flow in runtime claims. An attempted check is not a pass.
7. Update the task row and the relevant canonical evidence document, then update README for meaningful state changes. Do not edit README alone to imply progress. Never mark a whole phase done from a code change or a partial gate.
8. If an AI cannot access GitHub/staging, state the precise access gap and provide a ready-to-execute bounded handoff; do not pretend the work occurred.
9. Never put credentials, non-anonymized customer data, or raw invoice contents into repository docs, logs, prompts or AI context. No secrets from prior chat messages belong in source control.
10. No local/in-plugin CLI, npm package or JavaScript bundler. GitHub Actions is the authorized Composer/PHP/package route. Use WordPress-bundled `wp.element` for React.

## Artifact handling

No ZIP or test file should be delivered to the user in chat or saved to a personal file library for this project. When authorized runtime testing requires an artifact, install the successful CI artifact directly in the private test site if the available tools permit, then remove temporary local/shared copies after the check. Keep only source and truthful evidence in the GitHub repository. Do not publish a test artifact or expose staging credentials.

## Definition of complete

A task closes only when its task-specific acceptance evidence is recorded and independently retrievable. Phase 6 closes only when all six implementation packages, including its provider workflow under approved evidence/security boundaries, meet their defined exit outcomes or the owner explicitly narrows that scope. Phase 7 closes only under `PHASE-7-GATES.md`. Marketplace readiness/submission is Phase 8 and has not started.
