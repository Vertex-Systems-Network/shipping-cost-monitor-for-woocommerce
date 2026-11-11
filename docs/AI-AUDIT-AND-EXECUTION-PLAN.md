# AI audit and execution plan

**Last reconciled:** 2026-11-11 against repository head `4e568c4` plus the release-preparation change set that introduced this revision.  
**Repository:** `Vertex-Systems-Network/shipping-cost-monitor-for-woocommerce`  
**Purpose:** Route ChatGPT, Claude, Gemini, Cursor, Copilot and human contributors to the right source of truth and the next unblocked task.

This document holds **rules and routing only**. It deliberately carries no gate counts, build IDs or staging snapshots, because copies of status drift. Read status where it is owned:

| Question | Owner document |
|---|---|
| What has the owner approved or deferred? | `OWNER-DECISION-REGISTER.md` |
| Which Phase 7 gates pass, and on what evidence? | `PHASE-7-GATES.md` (gate table and "Current count" line) |
| What is the status of a task? | `AI-BACKLOG.md` (backlog table) |
| What is left before each marketplace submission? | `SUBMISSION-STRATEGY.md` ("Submission readiness checklist") |
| What does the product do and not do? | `PRODUCT-SPEC.md` |

## Source-of-truth order

1. `OWNER-DECISION-REGISTER.md` — accepted authority, deferrals and owner-only decisions.
2. `PRODUCT-SPEC.md` — canonical feature IDs, Free/Pro options and acceptance criteria.
3. `ARCHITECTURE.md` and `ARCHITECTURE-DECISIONS.md` — system and data boundaries.
4. `AI-BACKLOG.md` — task queue and task-level evidence.
5. `PHASE-7-GATES.md` — sole authority for Phase 7 gate status and closure.
6. `SUBMISSION-STRATEGY.md` — marketplace tracks and the readiness checklist.
7. `README.md` — concise status mirror; not an evidence ledger.
8. `PHASE-1-6-DEEP-AUDIT.md` and dated notes — history; superseded where newer evidence exists.

`AGENTS.md` lists what to *read first*; this list decides which document *wins* when two disagree. If sources conflict, follow this order, fix the stale lower-level reference in the same change, and never pick the more convenient status.

## What is implemented, and what is not

**In source (version 1.0.0, F-6):** Free — order-level cost record through WooCommerce CRUD; dashboard report with currency, tax-basis and evidence guardrails; shipping-method breakdown; privacy-minimized CSV export; Pro add-on — browser-local invoice CSV preview, mapping, exact-reference matching and reviewed commit; read-only ShipStation V2 booked-label adapter with AES-256-GCM encrypted key storage, bounded sync preview and hand-off to the same review/commit flow; HPOS compatibility declaration; uninstall cleanup of the stored credential; `readme.txt`, `changelog.txt` and `LICENSE` in the CI package; user guide with screenshots in `docs/user-guide/`.

**Implemented but not validated with a real account:** the ShipStation adapter. No merchant API key has been used against it. Owner decision C-3 records the credential design as implemented with independent security review still pending. Do not describe it as merchant-verified.

**Not delivered:** zone/category loss analysis, scheduled alerts, historical comparisons, rate-change simulation, PDF reports, per-parcel allocation, background reconciliation, persistent invoice archive, server-synchronized mapping profiles, and a Free/Pro split in code. The repository builds two plugins (decision F-7): the free plugin at the root and the Pro add-on in `pro/`.

**Guardrails that still apply:** never persist raw invoice uploads or customer profile data; use WooCommerce CRUD and query abstractions; keep currency and tax basis comparable; never silently overwrite an existing cost; a quote or booked label is never a final cost; no runtime LLM or customer-data transmission.

## Ordered execution queue

Take the first row you can act on. Rows 1–3 need no external party.

| Order | ID | Action and acceptance evidence | Blocker / stop rule |
|---:|---|---|---|
| 1 | P7-12 / AI-08 | Finish the security and privacy review: someone other than the code's author reviews capability/nonce boundaries, import bounds, export escaping, credential storage and uninstall behaviour, and records findings and fixes. Complete the remaining browser upload-boundary cases (5 MB, 5,000 rows, 100 columns, 65,536-character field). | Open. Source hardening and handler/HTTP probes exist; they are not an independent review. |
| 2 | P7-13 / AI-39 | Extend the benchmark from the recorded 100-order lane to 1,000 and 10,000 eligible orders, five runs per size and workflow; capture elapsed time, peak PHP memory, query count, slow queries, CSV row count and correctness. Agree budgets after the baseline. | Open. Do not fabricate results. A real-database staging copy is preferred over Playground. |
| 3 | AI-45 | Install the CI ZIP built from the release-preparation change set and confirm: activation, HPOS compatibility shown as compatible in WooCommerce > Settings > Advanced > Features, dashboard layout, uninstall removes `wpe_scm_shipstation_credentials`, and Plugin Check reports no errors. | Open. Needs a CI run on the merged change and a test site. |
| 4 | AI-31 | Connect a real ShipStation V2 key on a test site; record connect, sync, review and commit results and the exact fields returned. | Blocked: owner must supply a merchant or sandbox API key. Does not block rows 1–3. |
| 5 | P7-14 | Run the approved beta with consenting merchants; record sanitized, reproducible feedback and resolutions. | Blocked: needs participating merchants and anonymized invoice samples (decision A-3). |
| 6 | Phase 8 | Work through the submission readiness checklist in `SUBMISSION-STRATEGY.md`. Free goes to WordPress.org; Pro goes to WooCommerce.com (F-8), whose billing and update system replaces any licensing code. | Starts when Phase 7 closes or the owner narrows scope. Several items are owner-only. |

Gates P7-08, P7-09 and P7-11 are passed for their stated scopes. Do not re-run them unless the build, dataset, environment or test path changes.

## Who decides

The repository is AI-operated (Gate F in `OWNER-DECISION-REGISTER.md`). When a task needs a product or engineering decision, make it, record it in Gate F with the reasoning, then act. Ask the owner only for what an AI cannot supply: external-account actions, secrets, and real merchants.

## Contributor protocol (all AI tools and humans)

1. Confirm the repository is exactly `Vertex-Systems-Network/shipping-cost-monitor-for-woocommerce` and inspect the current default branch head before editing.
2. Read `AGENTS.md`, this plan, the README status block, the assigned backlog row and its cited sources. Check current files rather than trusting copied chat context.
3. Take the first actionable row above, or the highest-priority unblocked backlog item. Do a concrete implementation or verification in the session and meet its acceptance evidence where possible.
4. Check existing feature IDs before adding any option or setting. Do not duplicate controls, reports, integrations or counters.
5. If blocked, record the missing fixture, permission, provider or owner decision once, with its smallest unblock action, and continue with the next independent task. Repeat a check only when the artifact, environment, fixture, permission or code changed.
6. Keep **implemented**, **static-verified**, **runtime-verified**, **blocked** and **deferred** separate. Runtime claims name the commit or build ID, environment, fixture and exact flow. An attempted check is not a pass.
7. Update the task row and the owning evidence document first, then the README mirror. When a status changes, change it in its owner document and delete or date-stamp every other copy; do not add a new copy here.
8. If a tool cannot reach GitHub or staging, state the access gap and leave a bounded hand-off. Do not pretend the work occurred.
9. Never put credentials, non-anonymized customer data or raw invoice contents into docs, logs, prompts or AI context.
10. No in-plugin CLI, npm package or JavaScript bundler as a project requirement. GitHub Actions is the authorized Composer/PHP/package route. Use WordPress-bundled `wp.element` for React. A contributor may run read-only syntax checks in their own environment; label those as static checks, never as CI or runtime results.

## Environment evidence rules

Evidence belongs to the site it came from. The TasteWP site, the `wpessential.org/el-test` staging site and WordPress Playground lanes are separate; never transfer order or report results between them. Playground runs on PHP.wasm and does not establish MySQL or MariaDB behaviour.

## Artifact handling

Do not deliver a ZIP or test file to the user in chat or keep one in a personal file library. When authorized runtime testing needs an artifact, install the successful CI artifact directly on the private test site if tools permit, then remove temporary copies. Keep only source and truthful evidence in the repository. Do not publish a test artifact or expose staging credentials.

## Definition of complete

A task closes only when its acceptance evidence is recorded and independently retrievable. Phase 6 implementation is complete in source; its provider workflow is not merchant-validated until AI-31 is evidenced. Phase 7 closes only under `PHASE-7-GATES.md`. Marketplace submission is Phase 8 and has not started; preparing files for it is not a submission.
