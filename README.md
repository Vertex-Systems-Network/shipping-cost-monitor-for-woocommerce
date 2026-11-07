# WPEssential Shipping Cost Monitor

**A WooCommerce shipping-cost intelligence add-on by WPEssential.**

Find orders and delivery patterns where actual fulfilment spend is eroding margin. The proposed product combines a per-order cost ledger, courier invoice reconciliation and optional carrier API sync where reliable data exists.

> **Project status:** Phases 1–5 documented/code-delivered for their defined scope; Phase 6 remains in progress and Phase 7 is active  
> **Current overall progress:** `■■■■■■■■░░ 81%` — Phase 6 remains 83%; Phase 7 has 6/9 planned verification items complete (67%): three staging/HPOS/CI execution checks and three source dry runs covering WPML/WCML, Polylang, and legacy-storage API usage. Actual runtime combinations, live integrations, translation catalogs, legacy-store execution, regression/security/performance and beta checks remain open. A CI-only artifact build workflow is authorized; no local CLI or JavaScript bundler.  
> **Last status update:** 2026-11-07 — WPML/WCML, Polylang and legacy-storage source dry runs completed; no hard dependencies or direct legacy order-table writes were found. Phase 7 is 67% (6/9 planned items: 3 execution checks + 3 dry runs), overall progress 81%; runtime checks remain open.  
> **Repository:** [wpessential-shipping-cost-monitor](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor)

## Product direction

Record what customers paid for shipping and what merchants paid to fulfil shipments, then surface margin loss and mismatched charges. Connect optional carriers/platforms where APIs support useful shipment or cost evidence.

**Evidence rule:** rate quotes are estimates; label/manifest amounts are booked charges; invoice rows or explicitly reviewed merchant values may represent final cost. Do not silently treat one class as another. Public API research identified one separate iMile Express platform with settled billing fields on some shipment records; provider/account fit remains unverified. A 2026-11-06 official-source recheck also confirmed PostEx Pakistan advertises APIs and WooCommerce integration, but did not establish shipment-level final billed-cost fields. Final-invoice endpoints are not established for every shortlisted carrier.

## Roadmap

The progress bars measure documented or implemented deliverables only; they do not certify runtime QA, merchant validation, compatibility, or release readiness. Overall progress is the equally weighted mean of the eight phase percentages, rounded to a whole percent: (5 × 100% + 83% + 67% + 0%) ÷ 8 = 81%. Completed dates are actual; future start/end dates are schedule targets. Phase 3 is a written UX specification, not an interactive React prototype. Phase 4 authorizes only the initial manual/no-API foundation; credential security and CSV retention remain open. A temporary Playground smoke setup uses WordPress 7.1.4, WooCommerce 11.2.2 and PHP 8.3. The owner-designated staging site runs WordPress 7.1.4 and WooCommerce 11.2.2. An earlier 14-file test ZIP with the source PSR-4 fallback was installed first. The owner later reported uploading the CI artifact; staging shows version 0.1.0 active but exposes no build SHA, so exact artifact identity is not independently confirmed. Synthetic orders #51 and #52 verified manual cost entry and the CSV preview → exact match → reviewed commit path. The staging report showed PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance and 100% coverage (2/2); re-importing order #52 surfaced the stored cost without offering overwrite. This verifies synthetic happy-path and selected edge-case flows, plus report CSV download; it is not a compatibility or release-readiness claim. Woo telemetry opt-in was disabled and no store address was entered. As of 2026-11-07, the owner has authorized a CI-only GitHub Actions ZIP build workflow; no local CLI, npm package or JavaScript bundler is added.

| Phase | Status | Progress | Start (actual/planned) | Target end | Completed | Exit outcome |
|---|---|---:|---|---|---|---|
| 1. Competitor and public API desk research | Complete (desk review) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-13 | 2026-11-06 | Competitor overlap and region-aware provider evidence matrix |
| 2. Requirements, options bank and MVP proposal | MVP boundary accepted; pricing/merchant validation pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-20 | 2026-11-06 | Deduplicated feature IDs, metric contract, settings map and testable MVP criteria documented |
| 3. UX flows and React admin specification | UX baseline approved; visual QA pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-27 | 2026-11-06 | Dashboard, cost records, invoice import, exceptions, integrations and UI states specified |
| 4. Architecture and pre-development readiness | Initial manual/no-API boundary accepted; security/retention pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-12-04 | 2026-11-06 | Storage, money/evidence model, imports, adapters/auth, security, compatibility and readiness decisions documented |
| 5. Core plugin implementation | Code delivery complete; selected staging runtime paths verified | `■■■■■■■■■■ 100%` | 2026-11-06 | 2027-01-15 | 2026-11-06 | Admin shell, manual ledger, tax/refund-aware reports and privacy-minimized CSV export |
| 6. Reconciliation, API sync and Pro workflows | Five of six implementation packages delivered; selected staging QA passed, broader runtime QA pending; live provider package blocked | `████████▍░ 83%` | 2026-11-06 (actual) | 2027-02-05 | — | Browser-local invoice preview through reviewed row commit, report breakdown, and account-verified provider workflow |
| 7. Compatibility, quality and beta | In progress — 3 execution checks + 3 source dry runs; runtime compatibility pending | `■■■■■■▋░░░ 67%` | 2026-11-07 (actual) | 2027-02-19 | — | Supported-platform matrix, storage modes, multilingual integrations, regression/performance and beta feedback |
| 8. Marketplace readiness | Not started | `░░░░░░░░░░ 0%` | 2027-02-22 (planned) | 2027-03-05 | — | Free WordPress.org and Paid WooCommerce.com submission tracks, review evidence and release candidate |

**Overall project progress:** `■■■■■■■■░░ 81%`, calculated from the eight phase percentages shown above. Phases 1–4 document deliverables and Phase 5 code delivery are complete for their stated scope; Phase 6 has five implementation packages delivered out of six. These percentages do not include QA or support validation. Phase 5 is 100% code delivery; Phase 6 remains 83% implementation coverage because the live provider package is blocked. Phase 7 is 67% (6/9 planned verification items complete: 3 execution checks and 3 source-based dry runs) and is not release-ready. A temporary Playground smoke test verified installation, activation and a rendered dashboard on WordPress 7.1.4 + WooCommerce 11.2.2 + PHP 8.3; disposable order #11 showed $5 charged, $11 confirmed tax-exclusive invoice-final cost, -$6 variance and 100% coverage. On owner-designated staging (WordPress 7.1.4 + WooCommerce 11.2.2), the test ZIP was installed and activated. Synthetic order #51 ($500 shipping, $1,100 confirmed cost) and CSV-imported order #52 ($250, $850) produced PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance and 100% coverage (2/2), including the flat-rate breakdown. Re-importing order #52 surfaced its existing cost without overwrite. Staging edge-case preview verified duplicate-in-file, invalid amount, unmatched reference, refund review and existing-cost protection; report CSV download also completed. Runtime ambiguous-collision and exact AJAX replay remain unverified, as do currency mismatch, HPOS, multilingual, lower-bound and performance checks. No compatibility matrix or automated suite was run. The official PostEx Pakistan API/WooCommerce claim was recorded in the integration matrix, while final-cost schema and merchant-account access remain unknown. No compatibility matrix or automated suite was run.

### Phase 6 implementation package ledger

| Package | Deliverable | Status | Evidence / remaining proof |
|---|---|---|---|
| 1 | Browser-local CSV preview, parsing and column mapping, including WooCommerce Shipping label preset | Implemented; synthetic staging smoke passed | One synthetic CSV parsed and mapped in staging; merchant invoice formats and edge cases remain unvalidated. |
| 2 | Exact WooCommerce order-reference matching and existing-cost evidence preview | Implemented; synthetic staging smoke passed | CSV reference matched order #52 exactly; re-import displayed existing-cost evidence. Other duplicate/ambiguous/unmatched cases remain pending. |
| 3 | Explicit reviewed-row commit with currency, evidence and overwrite safeguards | Implemented; synthetic staging smoke passed | Reviewed row committed PKR 850 invoice-final/tax-exclusive cost to order #52; duplicate re-import offered no overwrite. Retry and invalid-row guards need dedicated verification. |
| 4 | Exception triage, refund-status guard and browser-local mapping profiles | Implemented | Preview-state filters, refund/duplicate guards and local-only profiles are documented; carrier sample coverage remains unvalidated. |
| 5 | Currency-separated shipping-method variance breakdown | Implemented; Playground and staging smoke passed | Playground verified one order; staging flat-rate breakdown matched two synthetic orders and PKR -1,200 total variance. Multi-currency and mixed-method cases remain untested. |
| 6 | Live provider adapter and account-level final-cost verification | Blocked | No provider account or final billed-cost schema is verified; Gate C-3 still blocks credentials and live sync. |

### Targeted staging edge-case verification (2026-11-06)

All rows below were synthetic, previewed in the browser and matched only where eligible. No cost was committed from this edge-case file.

| Scenario | Status | Staging evidence / limit |
|---|---|---|
| Duplicate references in one CSV | Passed | Order references 51 and # 51 normalized to the same order; both were marked duplicate and excluded from commit. |
| Invalid amounts | Passed | Non-numeric amount was rejected; negative amount was held for credit/refund review. |
| Unmatched reference | Passed | Synthetic reference 987654321 returned unmatched. |
| Refund-status row | Passed | Refunded status was held for review and excluded from matching; No refund remained eligible for match preview. |
| Re-import / retry safety | User-facing retry guard passed; internal replay code reviewed | Re-import showed existing-cost evidence and prevented overwrite. The server checks the saved row fingerprint before existing-cost protection; the exact same AJAX replay response was not directly stimulated through the UI. |
| Ambiguous order reference | Matcher branch reviewed; runtime fixture unavailable | The active staging plugin list has no order-number customization extension, and native order IDs/numbers are unique. The matcher returns ambiguous when multiple exact shipping-order candidates are found, but that outcome was not runtime-generated. |

**Progress remains:** Phase 6 83%; overall 73%. The blocked provider package and broad compatibility verification are unchanged.

**Remaining verification:** runtime-test an ambiguous match using a controlled duplicate order-number provider/fixture, and directly replay an identical commit request to observe the server’s already-imported response. The current UI prevents a second commit once a row is saved, so it cannot exercise that lower-level retry branch. Report CSV download was verified on staging on 2026-11-06. Currency-mismatch, HPOS/legacy, multilingual, lower-bound and performance checks also remain. See [Phase 1–6 deep audit](docs/PHASE-1-6-DEEP-AUDIT.md). Keep implementation progress at 83% until the blocked sixth package or a formally revised phase scope is resolved.

## Build and download a staging ZIP

An installable ZIP is generated by the GitHub Actions workflow; the source tree itself does not contain generated vendor files.

1. Open the repository's **Actions** tab.
2. Choose **Build installable plugin ZIP** and select **Run workflow** on the branch you want to test. Plugin-source pushes to `main` also start a build.
3. Wait for the run to finish successfully, then download the `wpessential-shipping-cost-monitor-<commit>` artifact from that run.
4. Extract the downloaded Actions artifact bundle. It contains `wpessential-shipping-cost-monitor.zip` and its SHA-256 checksum.
5. Upload the inner plugin ZIP in WordPress admin under **Plugins → Add Plugin → Upload Plugin**.

The workflow generates Composer's PSR-4 `vendor/autoload.php`, runs PHP lint and an autoloader smoke check, then packages the plugin under the correct `wpessential-shipping-cost-monitor/` directory. Artifacts are retained for 14 days. This is a staging/test build, not a marketplace release. First successful run: [GitHub Actions run #42373229279](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42373229279), commit `bafc3caf07d022fcaafbf11fa3e755e8b253b7a6`. PHP lint, Composer PSR-4 generation/smoke, ZIP integrity and upload all passed. Artifact SHA-256: `1ce2781cb3fed3e187a9e0fe559546004ab8c9a4454ad3d8e9fa3dda0c7d2707`; expires 2026-11-21. The owner reported uploading this build; staging shows the plugin active, but no build SHA is exposed in its admin UI.
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

The carrier adapter contract and registry define bounded shipment-evidence retrieval, declared regions/capabilities, deduplicated adapter discovery and an exception-safe, sanitized non-secret status projection. The dashboard shows adapter state when an adapter is registered; this build has no live provider, credential storage or synchronization, so it reports that CSV/manual workflows remain available. No credentials are exposed. The foundation is recorded as AI-28; it does not complete the live API adapter slice. **Phase 6 is not complete:** AI-31 remains blocked pending provider identity/account confirmation and Gate C-3 security review. Public research identifies iMile Express (Merdi Express Ltd.) as a technical candidate with settled billing fields for some shipment records, but its Hong Kong/Far East scope is not confirmed to match the intended GCC/South Asia iMile carrier. The 2026-11-06 official-source recheck added PostEx Pakistan as a better regional-fit discovery candidate: its official pages advertise APIs and WooCommerce integration, but do not establish final shipment-cost/adjustment fields or account API scopes; COD upfront settlement is not final shipping-cost evidence. Neither candidate is an implemented or verified integration. Gate C-3 still blocks credential collection and live sync. Server-synchronized mapping profiles and durable exception resolution also remain open pending merchant samples and retention decisions; rate simulation remains deferred. The preview table now filters the current in-browser batch into needs-review, ready-to-save and saved rows; it does not persist or resolve exception decisions. When an order already has a cost, the read-only matcher returns its stored amount, currency, evidence and tax basis so the preview can compare it with the CSV row; the existing value stays untouched and no automatic variance is asserted. Merchants can save up to 20 CSV column-mapping profiles in browser local storage; profiles contain source format, headers and parsing/evidence settings only, never invoice rows. Profiles are browser-local, not synchronized or server-backed; up to 20 validated profiles may be saved. Applying one requires the same source format and exact header layout, then restores its saved comma, semicolon or tab delimiter and column mapping. The dashboard also breaks comparable variance down by WooCommerce shipping method within each currency. Mixed-method orders stay grouped under their method combination; the breakdown applies the same confirmed-cost and tax-exclusive rules as the main variance metric. Invoice currencies that differ from the matched order are shown as review items and excluded from bulk commit in both the UI and the server handler; no currency conversion is performed. Merchant invoice samples are needed to validate template coverage; API credentials remain gated on security design. The current GitHub build was installed and activated in a temporary WordPress Playground instance (WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3). The first dashboard load was blank because the invoice preview JSX-style `createElement` tree had a missing closing parenthesis; the parse issue is fixed in the current branch. A fresh Playground load visibly rendered the dashboard, date controls, CSV preview controls, carrier-connection empty state and no-order empty state. That earlier Playground check covered only installation/activation and page rendering. The later owner-staging smoke test verified CSV upload, parse, exact match, reviewed commit and existing-cost duplicate protection for synthetic orders #51–#52; see the Phase 6 package ledger above. HPOS, multilingual, exception/refund/retry, lower-bound and performance checks remain pending. The failed first environment attempt used WordPress 6.9.11 and WooCommerce 11.2.2, which reported that it requires WordPress 7.0; see the compatibility baseline update.
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

## Composer PSR-4 autoloading

`composer.json` maps `WPEssential\\ShippingCostMonitor\\` to `includes/`. The plugin bootstrap loads the generated `vendor/autoload.php` when it is present; until final packaging, a small source fallback autoloader resolves the same namespace without invoking Composer CLI. Manual class includes were removed from the plugin coordinator. Composer's generated `vendor/` files have not been created or runtime-tested because CLI/package tooling remains deferred until the plugin is final.

## Scope guardrails

- No general P&L/COGS suite, label platform, tracking portal or fulfilment replacement.
- Carrier integrations are optional, adapter-based and prioritized by merchant usage, final-cost evidence, regional coverage, access terms and maintenance cost.
- No runtime AI service or customer-data transmission to LLMs.
- Local/in-plugin CLI, package managers and JavaScript bundlers remain out of scope; the owner authorized only a GitHub Actions CI workflow for installable ZIP packaging and verification. The React shell uses WordPress-bundled `wp.element`.

## For AI coding tools

Repository-aware agents should load [AGENTS.md](AGENTS.md) before acting. Tool entry points are included for Codex, Cursor, Claude Code, Gemini CLI and GitHub Copilot. General ChatGPT requires repository/file access and the prompt in [AI-START-HERE](docs/AI-START-HERE.md).


### Update log

- **2026-11-06:** Cleaned a duplicated sentence in AI-34 and reconciled the README with the verified state: WordPress staging has official WooCommerce 11.2.2 active, but the project ZIP could not be loaded. Code review also found a stale hard-coded refund-status flag in the report response; it now reflects whether shipping refunds reduced charged totals in the selected period (AI-35). Refund scenario runtime verification remains pending. Phase 6 stays at 83% and overall progress stays at 73%; CSV import runtime QA and provider verification remain open.
- **2026-11-06:** Retried the GitHub ZIP download once through the browser's download event; it timed out again. The staging installer only offers standard ZIP upload, so no project plugin install or CSV runtime verification occurred. Public provider recheck confirms PostEx Pakistan advertises API/WooCommerce integration but still publishes no reviewed final-cost schema; iMile Express documents settled billing fields, but is a distinct HK/Far East service and merchant-account fit remains unverified. No live adapter is enabled. Phase 6 remains 83%, overall 73%; both blockers remain open pending a usable plugin archive and account-level provider/security evidence.
- **2026-11-06:** Added the Composer PSR-4 namespace map, bootstrap preference for generated Composer autoload, and source-only PSR-4 fallback; removed manual includes from the coordinator. No Composer CLI was run, so generated vendor files and runtime verification remain pending. Phase progress is unchanged.
- **2026-11-06:** Rechecked the designated staging Plugins → All screen after the owner reported installing the plugin. This browser session shows only WooCommerce 11.2.2 (1 installed plugin); Shipping Cost Monitor is absent, so no importer workflow test could run. Phase 6 remains 83% and overall progress 73%.
- **2026-11-06:** Prepared a 14-file test ZIP from the repository source after the browser download path failed. ZIP integrity check passed; SHA-256: `7212c9526583fc08102ac5fd2a709c89d95352c1778f0bcaa094e902df01bbe4`. It has not been uploaded or runtime-tested on staging. Progress remains 73% overall / 83% Phase 6.

- **2026-11-06:** Installed and activated the test ZIP on staging (WordPress 7.1.4 + WooCommerce 11.2.2). Synthetic orders #51 and #52 verified manual entry, CSV preview/mapping, exact match, reviewed commit and existing-cost duplicate protection. Report: PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance, 100% coverage (2/2). Phase 6 remains 83%; provider integration and broad edge-case/compatibility QA remain pending.


- **2026-11-07:** First CI artifact build completed successfully (run #42373229279). Composer generated `vendor/autoload.php`; PHP lint, PSR-4 class smoke check, ZIP integrity and artifact upload passed. Downloadable 14-day artifact is ready for owner staging installation. Phase 6 remains 83% and overall progress 73%; CI packaging success does not close provider or staging/runtime gates.


- **2026-11-07 staging follow-up:** After the owner reported uploading the CI artifact, staging (WordPress 7.1.4 + WooCommerce 11.2.2) showed WPEssential Shipping Cost Monitor v0.1.0 active. The dashboard rendered with existing synthetic summary values: PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance, 100% coverage (2/2). The admin page does not display a build SHA, so the exact artifact-to-install identity is not independently proven. Carrier panel confirmed no adapter or credentials configured. A fresh synthetic CSV fixture was selected but the browser reported “The selected file could not be read”; no new import/commit was completed. Exact AJAX replay, ambiguous runtime fixture, and live carrier sync remain unverified/blocked. Phase 6 stays 83%; overall stays 73%.


- **2026-11-07 Phase 7 start:** WooCommerce System Status on staging reported WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3.36, MySQL 8.0.46 and HTTPS; HPOS was enabled with OrdersTableDataStore. Plugin v0.1.0 was active. Dashboard rendered the existing synthetic two-order report; HPOS order #52 displayed its PKR 850 invoice-final, tax-exclusive cost and private import note. These are two passed staging gates: current environment and selected HPOS read/display path. A subsequent GitHub Actions run #42374396949 passed PHP 8.1–8.4 Composer install, lint and PSR-4 autoload smoke in four jobs; that is static/autoload evidence, not full WP/WC runtime compatibility. Phase 7 is 33% (3/9 gates); Phase 6 remains 83%; overall is 77%. See [compatibility baseline and Phase 7 execution log](docs/COMPATIBILITY-BASELINE.md).


- **2026-11-07 Phase 7 CI follow-up:** The first PHP matrix workflow attempt failed because of PSR-4 class escaping; the workflow was corrected. Run [#42374396949](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42374396949) then passed PHP 8.1, 8.2, 8.3 and 8.4 dependency/lint/autoload jobs and produced the installable ZIP artifact. Artifact SHA-256: `1a0abd3c406aaa9c7debb68d85a1961ae666d7d416883b63601aa46adb16edd6`. This raises Phase 7 to 3/9 gates (33%); full WordPress/WooCommerce runtime lanes and other compatibility gates remain open. Overall progress: 77%.


## 2026-11-07 Phase 7 staging artifact upload round

The SHA-256-verified installable ZIP from GitHub Actions run [#42374396949](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42374396949) (`sha256:1a0abd3c406aaa9c7debb68d85a1961ae666d7d416883b63601aa46adb16edd6`) was uploaded through the owner-designated WordPress staging installer and **Replace current with uploaded** completed successfully. WordPress identified both existing and uploaded plugin versions as 0.1.0. The plugin remains active. The dashboard rendered PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance and 100% coverage (2/2). Order #52 still displayed its PKR 850 invoice-final, tax-exclusive cost and private import note. The storefront also loaded after replacement; no fatal error or recovery-mode screen was observed. The admin installer, plugin dashboard, order page and storefront each displayed `ob_end_flush(): Failed to send buffer of zlib output compression (1)` from WordPress `wp-includes/functions.php:5581`. Its cause is undetermined and is not attributed to this plugin. Record it as a staging environment notice requiring separate diagnosis. The admin UI does not expose an installed-file SHA, so the selected artifact upload and successful replacement are evidenced, while a post-install filesystem digest is not.

This extends evidence for the same current-platform and HPOS gates; it does not add a distinct compatibility gate. **Phase 7 remains 3/9 (33%); overall remains 77%; Phase 6 remains 83%.** PHP/WP/WC runtime combinations beyond staging PHP 8.3.36, minimum-version runtime, legacy storage, WPML/WCML, Polylang/WooCommerce add-on, regression/security/performance and beta checks remain open. No CLI was run and no source code changed in this round.


## 2026-11-07 WPML / Polylang dry-run review

The staging Plugins screen showed five installed plugins (four active), including Shipping Cost Monitor v0.1.0 active, with no WPML, WooCommerce Multilingual or Polylang add-on present. The owner authorized a dry run. Source review found no direct WPML (`icl_*`) or Polylang (`pll_*`) API calls or hard dependencies. Order reporting uses WooCommerce order query/CRUD APIs; shipping-cost values are attached to order records and currency is explicitly stored rather than implicitly converted. PHP UI strings use WordPress gettext functions. The React admin uses `wp.i18n`; the page registers `wp_set_script_translations()`. No `languages/` directory/catalog files were found in the repository, so translated-catalog completeness was not verified.

**Dry-run disposition:** WPML + WooCommerce Multilingual and Polylang + Polylang for WooCommerce source-level dry runs are complete. This indicates the current source does not intentionally depend on either multilingual plugin and uses WordPress/WooCommerce abstractions; it does not prove runtime interoperability, translated order metadata synchronization, localized date/decimal behavior, or actual translated UI. No add-on was installed and no runtime multilingual claim is made. Phase 7 is now 6/9 planned items complete (67%): 3 execution checks plus 3 dry runs. Overall project progress is 81% using the README's equal phase weighting. Actual multilingual runtime, translation catalogs, minimum versions, legacy storage, full PHP/WP/WC runtime matrix and broader QA remain open.


## 2026-11-07 legacy order-storage source dry run

Source search found no direct $wpdb, wp_posts, or update_post_meta order-storage access. Report/export use wc_get_orders() and WooCommerce order objects; shipping-cost persistence/read uses WooCommerce CRUD methods. Dry-run complete: the code follows WooCommerce's storage abstraction rather than directly targeting HPOS or legacy tables. No legacy datastore was activated, so legacy runtime compatibility remains unverified. This is the third source dry run alongside WPML/WCML and Polylang. Phase 7 is 6/9 (67%); overall project progress is 81%. Minimum-version and PHP/WP/WC runtime combinations, legacy runtime, live multilingual behavior, translation catalogs and regression/security/performance/beta remain open.


Staging recheck on 2026-11-07: Installed Plugins displayed five plugins/four active; Shipping Cost Monitor v0.1.0 remained active, while WPML/WCML and Polylang were absent. The familiar ob_end_flush zlib compression notice remained visible. This confirms the dry-run environment and current active state only; no multilingual runtime interaction was possible.


## 2026-11-07 repeat staging report smoke check

On the owner-designated staging site (WordPress 7.1.4, WooCommerce 11.2.2; PHP 8.3.36 per System Status), the date-filtered report was refreshed for 2026-10-09 through 2026-11-07. The dashboard continued to show PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance and 100% coverage (2/2). The Export CSV action completed and the browser returned a download. No order or site settings were changed. The zlib ob_end_flush notice remained visible; no fatal error was observed. This is a repeat functional smoke check inside the existing staging lane, not a new compatibility gate; Phase 7 remains 6/9 (67%), overall project 81%.
