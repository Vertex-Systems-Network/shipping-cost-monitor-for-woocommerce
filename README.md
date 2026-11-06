# WPEssential Shipping Cost Monitor

**A WooCommerce shipping-cost intelligence add-on by WPEssential.**

Find orders and delivery patterns where actual fulfilment spend is eroding margin. The proposed product combines a per-order cost ledger, courier invoice reconciliation and optional carrier API sync where reliable data exists.

> **Project status:** Phase 5 code complete; Phase 6 invoice workflow in progress  
> **Current overall progress:** `■■■■■■█░░░ 73%` — Phase 5 code scope is complete; Phase 6 has five of six implementation packages delivered; dashboard smoke render is verified; full compatibility/workflow verification remains pending; no CLI or build toolchain.  
> **Last status update:** 2026-11-06 — staging evidence and AI backlog wording reconciled; progress unchanged.  
> **Repository:** [wpessential-shipping-cost-monitor](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor)

## Product direction

Record what customers paid for shipping and what merchants paid to fulfil shipments, then surface margin loss and mismatched charges. Connect optional carriers/platforms where APIs support useful shipment or cost evidence.

**Evidence rule:** rate quotes are estimates; label/manifest amounts are booked charges; invoice rows or explicitly reviewed merchant values may represent final cost. Do not silently treat one class as another. Public API research identified one separate iMile Express platform with settled billing fields on some shipment records; provider/account fit remains unverified. A 2026-11-06 official-source recheck also confirmed PostEx Pakistan advertises APIs and WooCommerce integration, but did not establish shipment-level final billed-cost fields. Final-invoice endpoints are not established for every shortlisted carrier.

## Roadmap

The progress bars measure documented or implemented deliverables only; they do not certify runtime QA, merchant validation, compatibility, or release readiness. Overall progress is the equally weighted mean of the eight phase percentages, rounded to a whole percent: (5 × 100% + 83% + 0% + 0%) ÷ 8 = 73%. Completed dates are actual; future start/end dates are schedule targets. Phase 3 is a written UX specification, not an interactive React prototype. Phase 4 authorizes only the initial manual/no-API foundation; credential security and CSV retention remain open. A temporary Playground smoke setup uses WordPress 7.1.4, WooCommerce 11.2.2 and PHP 8.3. The owner-designated staging site reported WP 7.1.4; official WooCommerce 11.2.2 has now been installed and activated there. The project plugin has not been installed: GitHub archive downloads aborted/timed out in the browser, including a fresh download-event attempt; staging offers standard ZIP upload and no usable archive was obtained. Woo telemetry opt-in was disabled and no store address was entered. No CLI, package manager or build system has been added.

| Phase | Status | Progress | Start (actual/planned) | Target end | Completed | Exit outcome |
|---|---|---:|---|---|---|---|
| 1. Competitor and public API desk research | Complete (desk review) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-13 | 2026-11-06 | Competitor overlap and region-aware provider evidence matrix |
| 2. Requirements, options bank and MVP proposal | MVP boundary accepted; pricing/merchant validation pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-20 | 2026-11-06 | Deduplicated feature IDs, metric contract, settings map and testable MVP criteria documented |
| 3. UX flows and React admin specification | UX baseline approved; visual QA pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-27 | 2026-11-06 | Dashboard, cost records, invoice import, exceptions, integrations and UI states specified |
| 4. Architecture and pre-development readiness | Initial manual/no-API boundary accepted; security/retention pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-12-04 | 2026-11-06 | Storage, money/evidence model, imports, adapters/auth, security, compatibility and readiness decisions documented |
| 5. Core plugin implementation | Complete (code delivery; runtime verification pending) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2027-01-15 | 2026-11-06 | Admin shell, manual ledger, tax/refund-aware reports and privacy-minimized CSV export |
| 6. Reconciliation, API sync and Pro workflows | Five of six implementation packages delivered; runtime CSV QA pending; live provider package blocked | `████████▍░ 83%` | 2026-11-06 (actual) | 2027-02-05 | — | Browser-local invoice preview through reviewed row commit, report breakdown, and account-verified provider workflow |
| 7. Compatibility, quality and beta | Not started | `░░░░░░░░░░ 0%` | 2027-02-08 (planned) | 2027-02-19 | — | Supported-platform/provider verification and beta feedback |
| 8. Marketplace readiness | Not started | `░░░░░░░░░░ 0%` | 2027-02-22 (planned) | 2027-03-05 | — | Free WordPress.org and Paid WooCommerce.com submission tracks, review evidence and release candidate |

**Overall project progress:** `■■■■■■█░░░ 73%`, calculated from the eight phase percentages shown above. Phases 1–4 document deliverables and Phase 5 code delivery are complete for their stated scope; Phase 6 has five implementation packages delivered out of six. These percentages do not include QA or support validation. Phase 5 is 100% code delivery; Phase 6 is 83% implementation coverage, with CSV runtime verification pending and the live provider package blocked. A temporary Playground smoke test verified installation, activation and a rendered Shipping Cost Monitor dashboard on WordPress 7.1.4 + WooCommerce 11.2.2 + PHP 8.3. A disposable test order (#11) was created in Playground with $5 flat-rate shipping and an $11 invoice-final, tax-exclusive cost. The rendered report showed $5 charged, $11 confirmed cost, -$6 variance, and 100% coverage (1/1), including the Flat rate method row. The CSV upload, parse, match and commit workflow was not exercised; no invoice file was uploaded. The owner-designated staging site runs WP 7.1.4 with official WooCommerce 11.2.2 active. The project plugin could not be installed because the repository ZIP download aborted/timed out, so no plugin workflow test ran there. The official PostEx Pakistan API/WooCommerce claim was recorded in the integration matrix, while final-cost schema and merchant-account access remain unknown. No compatibility matrix or automated suite was run.

### Phase 6 implementation package ledger

| Package | Deliverable | Status | Evidence / remaining proof |
|---|---|---|---|
| 1 | Browser-local CSV preview, parsing and column mapping, including WooCommerce Shipping label preset | Implemented | UI and source path documented; no real invoice file has been uploaded in runtime testing. |
| 2 | Exact WooCommerce order-reference matching and existing-cost evidence preview | Implemented | Read-only matching and duplicate/ambiguous/unmatched states are documented; runtime workflow remains unverified. |
| 3 | Explicit reviewed-row commit with currency, evidence and overwrite safeguards | Implemented | Server revalidation, WooCommerce CRUD storage and idempotency are documented; no commit has been exercised in runtime testing. |
| 4 | Exception triage, refund-status guard and browser-local mapping profiles | Implemented | Preview-state filters, refund/duplicate guards and local-only profiles are documented; carrier sample coverage remains unvalidated. |
| 5 | Currency-separated shipping-method variance breakdown | Implemented | One-order Playground report smoke showed the matching Flat rate row; broader data cases remain untested. |
| 6 | Live provider adapter and account-level final-cost verification | Blocked | No provider account or final billed-cost schema is verified; Gate C-3 still blocks credentials and live sync. |

**Next verification target:** exercise the CSV parse → match → review → explicit commit path using a disposable order and a synthetic, non-customer CSV after the required WooCommerce/plugin runtime is available. The owner-designated staging site now has WordPress 7.1.4 and WooCommerce 11.2.2 active, but the project plugin ZIP could not be loaded in this browser; the separate Playground smoke did not exercise CSV upload, match or commit. Keep implementation progress at 83% until the blocked sixth package or a formally revised phase scope is resolved.

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
- [Carrier evidence adapter contract](includes/Integrations/CarrierAdapterInterface.php)
- [Carrier adapter discovery registry](includes/Integrations/AdapterRegistry.php)
- [Carrier connection status panel](assets/js/admin.js)
- [Carrier connection status injection](includes/Admin/Page.php)

The preview reads CSV files in the browser (maximum 5 MB and 5,000 data rows), supports comma/semicolon/tab delimiters and decimal-mark selection, and maps reference, amount and currency columns. Price/label-price headers are recognized for amount mapping, and a merchant may choose to use the exact matched WooCommerce order currency when a source CSV has no currency column. The file and full invoice rows are not uploaded or persisted. Matching sends only mapped references for up to 50 valid rows. It accepts exact WooCommerce order ID/order-number matches only for standard shipping orders; duplicate, ambiguous, unmatched, and orders with an existing cost are identified without changing order data. Tracking/AWB matching is not supported.

A merchant must explicitly confirm the commit action. The server rechecks capability, nonce, order identity, shipping-line presence and editable-order permission, then saves only the normalized amount, currency, evidence, tax basis and source-row reference through WooCommerce order CRUD. Existing costs are never overwritten. A row fingerprint supports idempotent retry handling. The raw invoice file and unrelated columns are not stored; negative credit/refund rows are held for review and are not committed.

WooCommerce Shipping label-report CSV is a useful no-API source for label purchase prices, orders, services, tracking references and refund status. Rows with a populated refund-status value are held for review, and duplicate order references remain uncommitted, including `#123` and `123` references that normalize to the same order. The label amount is booked evidence; carrier adjustments mean it must not be called a final invoice cost. The generic importer recognizes common price/label-price headers and can map order columns and infer currency from a uniquely matched order. It strips only a leading `#` from order references before exact matching. A WooCommerce Shipping label-report preset sets booked-label evidence, unknown tax treatment and matched-order currency, and suggests order/price columns when recognized. Populated refund-status rows are review-only; multiple labels matching one order remain duplicate exceptions until merchant sample files establish a safe aggregation rule. The integration matrix now reflects the implemented preset while keeping its header and split-label validation status open. The preset can be selected before or after parsing the file. Refund-status handling is tracked as AI-30.

The carrier adapter contract and registry define bounded shipment-evidence retrieval, declared regions/capabilities, deduplicated adapter discovery and an exception-safe, sanitized non-secret status projection. The dashboard shows adapter state when an adapter is registered; this build has no live provider, credential storage or synchronization, so it reports that CSV/manual workflows remain available. No credentials are exposed. The foundation is recorded as AI-28; it does not complete the live API adapter slice. **Phase 6 is not complete:** AI-31 remains blocked pending provider identity/account confirmation and Gate C-3 security review. Public research identifies iMile Express (Merdi Express Ltd.) as a technical candidate with settled billing fields for some shipment records, but its Hong Kong/Far East scope is not confirmed to match the intended GCC/South Asia iMile carrier. The 2026-11-06 official-source recheck added PostEx Pakistan as a better regional-fit discovery candidate: its official pages advertise APIs and WooCommerce integration, but do not establish final shipment-cost/adjustment fields or account API scopes; COD upfront settlement is not final shipping-cost evidence. Neither candidate is an implemented or verified integration. Gate C-3 still blocks credential collection and live sync. Server-synchronized mapping profiles and durable exception resolution also remain open pending merchant samples and retention decisions; rate simulation remains deferred. The preview table now filters the current in-browser batch into needs-review, ready-to-save and saved rows; it does not persist or resolve exception decisions. When an order already has a cost, the read-only matcher returns its stored amount, currency, evidence and tax basis so the preview can compare it with the CSV row; the existing value stays untouched and no automatic variance is asserted. Merchants can save up to 20 CSV column-mapping profiles in browser local storage; profiles contain source format, headers and parsing/evidence settings only, never invoice rows. Profiles are browser-local, not synchronized or server-backed; up to 20 validated profiles may be saved. Applying one requires the same source format and exact header layout, then restores its saved comma, semicolon or tab delimiter and column mapping. The dashboard also breaks comparable variance down by WooCommerce shipping method within each currency. Mixed-method orders stay grouped under their method combination; the breakdown applies the same confirmed-cost and tax-exclusive rules as the main variance metric. Invoice currencies that differ from the matched order are shown as review items and excluded from bulk commit in both the UI and the server handler; no currency conversion is performed. Merchant invoice samples are needed to validate template coverage; API credentials remain gated on security design. The current GitHub build was installed and activated in a temporary WordPress Playground instance (WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3). The first dashboard load was blank because the invoice preview JSX-style `createElement` tree had a missing closing parenthesis; the parse issue is fixed in the current branch. A fresh load now visibly renders the dashboard, date controls, CSV preview controls, carrier-connection empty state and no-order empty state. This confirms only installation/activation and initial page rendering; no CSV was uploaded, no match or commit was exercised, and full workflow verification remains pending. The failed first environment attempt used WordPress 6.9.11 and WooCommerce 11.2.2, which reported that it requires WordPress 7.0; see the compatibility baseline update. The current Phase 6 smoke check confirms the initial dashboard render only; CSV parsing/matching/commit, HPOS, multilingual, lower-bound and performance checks remain pending.
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


### Update log

- **2026-11-06:** Cleaned a duplicated sentence in AI-34 and reconciled the README with the verified state: WordPress staging has official WooCommerce 11.2.2 active, but the project ZIP could not be loaded. Code review also found a stale hard-coded refund-status flag in the report response; it now reflects whether shipping refunds reduced charged totals in the selected period (AI-35). Refund scenario runtime verification remains pending. Phase 6 stays at 83% and overall progress stays at 73%; CSV import runtime QA and provider verification remain open.
- **2026-11-06:** Retried the GitHub ZIP download once through the browser's download event; it timed out again. The staging installer only offers standard ZIP upload, so no project plugin install or CSV runtime verification occurred. Public provider recheck confirms PostEx Pakistan advertises API/WooCommerce integration but still publishes no reviewed final-cost schema; iMile Express documents settled billing fields, but is a distinct HK/Far East service and merchant-account fit remains unverified. No live adapter is enabled. Phase 6 remains 83%, overall 73%; both blockers remain open pending a usable plugin archive and account-level provider/security evidence.
