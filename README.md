# WPEssential Shipping Cost Monitor

> **Current scope decision (2026-11-10):** WPML/WCML and Polylang integration and runtime compatibility are removed from the release requirements. Historical references below are superseded; no support claim is made. WordPress core translation APIs used by the plugin remain.

**A WooCommerce shipping-cost intelligence add-on by WPEssential.**

> **2026-11-11 P7-12 review update:** CSV export now excludes malformed negative recorded cost from comparable variance and leaves its recorded-cost cell blank, aligning with the dashboard eligibility rule ([fix `3320120`](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/commit/33201201ade1fb4ed88b695c0954c1c9e0f9f893)). Static review identified this discrepancy; CI/runtime regression and independent security/privacy review are still outstanding. P7-12 remains Partial; P7-13 and P7-14 remain Pending. See [gate register](docs/PHASE-7-GATES.md).


Find orders and delivery patterns where actual fulfilment spend is eroding margin. The proposed product combines a per-order cost ledger, courier invoice reconciliation and optional carrier API sync where reliable data exists.

> **P7-11 closure (2026-11-11):** On exact build `ddca326`, authenticated HTTP AJAX first commit returned `committed` and byte-identical replay returned `already_imported`; order cost stayed USD 9.25 with one import note. [HTTP replay evidence](docs/P7-11-HTTP-REPLAY.md) complements controlled HPOS ambiguity, re-import, malformed, mismatch, duplicate and staging refund/credit preview checks. P7-11 is **Pass for listed synthetic flows**. Phase 7 now has **8 Pass / 1 Partial / 2 Pending**; no real carrier or production order-number extension coverage is claimed.
> **P7-11 runtime update (2026-11-10):** Current CI ZIP `ddca326` was installed on disposable WP 7.1.4/WC 11.2.2/PHP 8.4.27 HPOS Playground. A controlled WooCommerce order-number/search-result collision between two synthetic shipping orders returned `ambiguous` in preview and direct commit, with no cost write. The follow-up response fix returned `persisted: false`/`committed: false` for the rejected row and true/true for a valid unique commit. [Exact fixture and result](docs/P7-11-AMBIGUITY-RUNTIME-PROBE.md); [CI run #42925897007](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42925897007). HTTP AJAX transport/replay and real third-party order-number behavior remain unverified. Gate count stays 7 Pass / 2 Partial / 2 Pending.
> **P7-11 code update (2026-11-10):** Preview and commit now share exact shipping-order resolution; a direct commit with multiple exact candidates returns `ambiguous` before writing. [Commit `b67db02`](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/commit/b67db0262b2937e15b48e10c4ac1801caa2ee83c) passed [CI build #42924563880](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42924563880) (PHP checks and package). Runtime ambiguous collision still needs a genuinely searchable duplicate-number fixture; P7-11 remains Partial and the gate count is unchanged.
> **P7-11–14 execution update (2026-11-10):** Isolated WordPress Playground (WP 7.1.4, WC 11.2.2, PHP 8.4.27) on exact CI ZIP `bbf95766b084` exercised the invoice handler: first commit `committed`, identical replay `already_imported`, different-batch re-import `existing_cost`, invalid/negative amount, currency/reference mismatch, duplicate order target, anonymous user, bad nonce and 51-row request were rejected. Stored USD 11.00 and one note remained unchanged. [Sanitized runtime evidence](docs/P7-11-12-ISOLATED-RUNTIME-PROBE.md). This is PHP handler execution, not HTTP AJAX or browser transport. Ambiguous-number indexed fixture and full P7-12 independent review remain; P7-13 instrumented scale benchmark and P7-14 consented merchant beta have no qualifying evidence. Gate count stays **7 Pass / 2 Partial / 2 Pending**.
> **Project status:** Phases 1–5 documented/code-delivered for their defined scope; Phase 6 implementation is complete (6/6 packages, 100%); Phase 7 QA/compatibility is active.  
> **Current release/QA status:** Phase 6 includes a read-only ShipStation V2 booked-label adapter, encrypted credential handling, bounded sync preview, and explicit review/match handoff. Merchant API-key runtime connection/sync and independent credential-security review remain pending; label cost is not final invoice cost. Phase 7 has 8 evidence gates passed, 1 partial and 2 pending in the revised 11-gate register. This count is not a readiness percentage. Aggregate project percentage is retired because phase deliverables and QA evidence are not comparable. See [Phase 7 gate register](docs/PHASE-7-GATES.md). No local CLI or JavaScript bundler; CI-only Composer/PHP checks remain authorized.
> **Phase 6 implementation update (2026-11-11):** 6/6 implementation packages delivered. ShipStation V2 uses read-only `GET /v2/labels`; connection secrets are encrypted with AES-256-GCM and fail closed if unique WordPress salts are unavailable; sync is date-bounded and paginated; synced label data enters the existing merchant review/match/explicit commit flow. Costs are classified as booked labels, never final invoices. Commit `50f37db` passed GitHub Actions run [#42891830660](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42891830660): PHP 8.1–8.4 Composer/autoload/lint and installable ZIP packaging/upload passed. Artifact ID `13798567208`, archive digest `sha256:0fe5122020257036688269d782e5090b9614bfd7ef6ee5415a28280cb12416b1` (expires 2026-11-24). CI does not test JavaScript or WordPress runtime; no eligible ShipStation merchant key was available to verify connection/sync on staging. See [Phase 6 deep audit](docs/PHASE-1-6-DEEP-AUDIT.md), [AI backlog AI-31](docs/AI-BACKLOG.md), and [integration matrix](docs/INTEGRATION-MATRIX.md).
> **Last status update:** 2026-11-10 — Phase 7 P7-12 source review found unbounded scalar field lengths in manual/import request paths (defensive resource-boundary gap; no exploit demonstrated). Added server-side byte caps and documented limits; GitHub Actions run #42704528492 passed PHP 8.1–8.4 Composer autoload/lint checks and ZIP packaging/upload for the first bounds change. Follow-up raw-input/nonce checks in commit ebc1df7 passed PHP 8.1–8.4 CI and ZIP build/upload in run #42705243002. Report CSV date inputs are now capped at 10 bytes before sanitization and the shared date validator checks exact length before regex; commit 73562a6 passed PHP 8.1–8.4 CI and ZIP build/upload in run #42705680399; dashboard date handling was also capped before sanitization in follow-up commit a98cf58 passed PHP 8.1–8.4 checks and ZIP build/upload in run #42705989532. Commit 9a7ae7e adds a commit-based BUILD-ID.txt to CI ZIPs and displays its short ID in the admin dashboard; PHP 8.1–8.4 CI and ZIP build/upload passed in run #42707308330. On 2026-11-10 the separate TasteWP site displayed build ID 9a7ae7e35951; its dashboard rendered with 3 synthetic products and 0 eligible orders, so report calculations were not exercised there. Malformed/oversize raw-request runtime verification and independent review remain pending. Phase 7 gate counts remain 7 narrow passes, 2 partial, 5 pending; no gate is being counted as passed by this code change alone. Follow-up source audit found the manual order editor nonce lacked an explicit length cap; commit e1eb345 adds a 128-byte pre-sanitization limit. GitHub Actions run #42823773223 passed PHP 8.1–8.4 Composer/autoload/lint checks and ZIP packaging/upload for commit e1eb345. This newer artifact has not been installed on staging, so runtime verification of the nonce change remains pending. On the wpessential.org staging site, a temporary blank order draft #93 was opened but no item or shipping data was added; it was moved to Trash and the active order list returned to its original four orders. No replay or ambiguity request was sent. A separate synthetic unmatched-reference CSV preview returned `unmatched` for 987654321; no order write occurred. Exact AJAX replay and ambiguous-number fixture remain pending. A separate source audit found unbounded CSV columns/field length in browser parsing; commit 35f0f1a adds a 100-column and 65,536-character cap, with Actions run #42826181980 passing PHP 8.1–8.4 Composer/autoload/lint and ZIP packaging; the workflow does not execute JavaScript parsing tests. Follow-up commit 8b0c1b7 rejects malformed trailing CSV text after quoted fields; Actions run #42826481179 passed PHP 8.1–8.4 Composer/autoload/lint and ZIP packaging. The workflow does not execute JavaScript. An isolated JS runtime check of the exact parser source passed valid CSV, escaped quotes, malformed trailing-text rejection, the 100/101-column boundary, 65,537-character rejection and 5,001-row rejection. This was not staging/browser verification; the later follow-up below supersedes its pending parser-runtime status. Phase 7 counts remain unchanged.
Earlier staging attempt (2026-11-10): The browser file chooser was not opened through the first upload path, so that attempt did not install the build or run a browser CSV test. This was resolved in the follow-up verification below.

**Prior staging verification (fd81d99, 2026-11-10):** On 2026-11-10, CI artifact from `fd81d99cc8e49a0a4c50010f7aa4f440ad157085` (Actions run #42836301431; artifact ID 13772948324; inner ZIP SHA-256 `ebd6bb9e0fd58fd9a4cd54a0038f4bcb29e2f062baff2f42635f7c754f13884c`) was installed/replaced on `wpessential.org/el-test` (WP 7.1.4, WC 11.2.2; existing synthetic PKR report remained PKR 750 charged / PKR 1,950 confirmed / PKR -1,200 variance, 2/2). The loaded asset URL used `admin.js?ver=fd81d99cc8e4`, confirming the build-specific cache key. A synthetic CSV with repeated `Amount` headers rendered distinctly as `Amount (Column 2)` and `Amount (Column 3)`; its one valid row previewed as ready-to-match. A malformed quoted field with trailing text was rejected in-browser with `Unexpected text follows a closing quote.` No matching, commit, order update or staging data mutation was made. The unrelated WordPress zlib `ob_end_flush()` notice persisted. This verifies selected staging UI/parser behavior only; server nonce/capability probes, exact AJAX replay and ambiguous-order fixture remain open. Phase 7 counts remain 7 narrow passes, 2 partial and 5 pending; no gate count was increased.


**Latest development and staging update (2026-11-10):** Commit `e08f91b6af9d0e7d731812c6612733e2bdb19657` prevents invoice commits from writing any rows when multiple submitted rows target the same WooCommerce order; the UI classifies non-committed results as review exceptions and explains duplicate-order batches. Actions run #42837923258 passed PHP 8.1–8.4 Composer/autoload/lint and ZIP packaging (artifact ID 13773738796; artifact archive digest `sha256:599780a23c4a83750b4c6b2fd58e8ed4ca2499cf493d3dc27081c2a635415fbd`; installable ZIP SHA-256 `a232d9d5ca84b5e6f83e51a2840dcf4f60d4a89fa2bd5cff487ab22e96467922`). The artifact was replaced successfully on `wpessential.org/el-test`; dashboard build ID `e08f91b6af9d` rendered and the PKR 750 / PKR 1,950 / PKR -1,200, 2/2 report stayed unchanged. Two synthetic rows referencing order `# 51` and `51` were classified as duplicate in preview and blocked before commit; no order write was requested. This verifies staging install and UI duplicate detection, not direct runtime invocation of the new server-side duplicate guard. Exact replay, ambiguous-order runtime fixture, raw request probes and independent review remain pending.
> **AI handoff:** [AI audit and execution plan](docs/AI-AUDIT-AND-EXECUTION-PLAN.md) is the contributor routing and current-state clarification map; [Phase 7 gate register](docs/PHASE-7-GATES.md) remains the only QA gate authority.
> **Repository:** [wpessential-shipping-cost-monitor](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor)

## Product direction

Record what customers paid for shipping and what merchants paid to fulfil shipments, then surface margin loss and mismatched charges. Connect optional carriers/platforms where APIs support useful shipment or cost evidence.

**Evidence rule:** rate quotes are estimates; label/manifest amounts are booked charges; invoice rows or explicitly reviewed merchant values may represent final cost. Do not silently treat one class as another. Public API research identified one separate iMile Express platform with settled billing fields on some shipment records; provider/account fit remains unverified. A 2026-11-06 official-source recheck also confirmed PostEx Pakistan advertises APIs and WooCommerce integration, but did not establish shipment-level final billed-cost fields. Final-invoice endpoints are not established for every shortlisted carrier.

## Roadmap

The phase bars represent each phase's own scope only; they do not certify runtime QA, merchant validation, compatibility, or release readiness. No aggregate project percentage is reported because unlike phases and evidence gates are not meaningfully comparable. Phase 7's fixed gate statuses are maintained in docs/PHASE-7-GATES.md. Completed dates are actual; future start/end dates are schedule targets. Phase 3 is a written UX specification, not an interactive React prototype. Phase 4 authorizes only the initial manual/no-API foundation; credential security and CSV retention remain open. A temporary Playground smoke setup uses WordPress 7.1.4, WooCommerce 11.2.2 and PHP 8.3. The owner-designated staging site runs WordPress 7.1.4 and WooCommerce 11.2.2. Historical note: an earlier 14-file test ZIP with the source PSR-4 fallback was installed on a separate staging site. On the separate TasteWP test site checked 2026-11-10, the CI artifact built from commit 9a7ae7e35951384eba21213cf390baa247e6765e is active and the dashboard displays build ID 9a7ae7e35951; dashboard shell rendered, with 3 imported synthetic products and 0 eligible orders, so report calculations were not exercised there. The TasteWP PHP/database fingerprint is unknown. On the separate wpessential.org staging site, synthetic orders #51 and #52 verified manual cost entry and the CSV preview → exact match → reviewed commit path. Its report showed PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance and 100% coverage (2/2); re-importing order #52 surfaced the stored cost without offering overwrite. This verifies selected synthetic flows and CSV export only; it is not a compatibility or release-readiness claim. Woo telemetry opt-in was disabled and no store address was entered. As of 2026-11-07, the owner has authorized a CI-only GitHub Actions ZIP build workflow; no local CLI, npm package or JavaScript bundler is added.

| Phase | Status | Progress | Start (actual/planned) | Target end | Completed | Exit outcome |
|---|---|---:|---|---|---|---|
| 1. Competitor and public API desk research | Complete (desk review) | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-13 | 2026-11-06 | Competitor overlap and region-aware provider evidence matrix |
| 2. Requirements, options bank and MVP proposal | MVP boundary accepted; pricing/merchant validation pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-20 | 2026-11-06 | Deduplicated feature IDs, metric contract, settings map and testable MVP criteria documented |
| 3. UX flows and React admin specification | UX baseline approved; visual QA pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-11-27 | 2026-11-06 | Dashboard, cost records, invoice import, exceptions, integrations and UI states specified |
| 4. Architecture and pre-development readiness | Initial manual/no-API boundary accepted; security/retention pending | `■■■■■■■■■■ 100%` | 2026-11-06 | 2026-12-04 | 2026-11-06 | Storage, money/evidence model, imports, adapters/auth, security, compatibility and readiness decisions documented |
| 5. Core plugin implementation | Code delivery complete; selected staging runtime paths verified | `■■■■■■■■■■ 100%` | 2026-11-06 | 2027-01-15 | 2026-11-06 | Admin shell, manual ledger, tax/refund-aware reports and privacy-minimized CSV export |
| 6. Reconciliation, API sync and Pro workflows | Implementation complete (6/6 packages); broader runtime QA pending | `■■■■■■■■■■ 100% implementation` | 2026-11-06 (actual) | 2027-02-05 | 2026-11-11 (implementation) | Browser-local invoice review/commit plus read-only ShipStation booked-label sync; merchant account validation is a QA gate |
| 7. Compatibility, quality and beta | In progress — 8 passes, 1 partial, 2 pending; see gate register | `■■■■■■■■░░░` (8/11 passed; not a readiness %) | 2026-11-07 (actual) | 2027-02-19 | — | All mandatory gates in docs/PHASE-7-GATES.md pass or owner explicitly narrows release support |
| 8. Marketplace readiness | Not started | `░░░░░░░░░░ 0%` | 2027-02-22 (planned) | 2027-03-05 | — | Free WordPress.org and Paid WooCommerce.com submission tracks, review evidence and release candidate |

**Overall project percentage retired:** the prior 81% average mixed unlike phase deliverables and QA counts, so it overstated closeness to release. Phases 1–5 remain complete only for their documented desk/spec/code-delivery scopes; Phase 6 implementation is complete (6/6 packages), while provider account runtime and broad QA remain open. Phase 7 is tracked by the 11 active gates in [docs/PHASE-7-GATES.md](docs/PHASE-7-GATES.md): 8 passed, 1 partial and 2 pending. Those eight do not certify broad compatibility or release readiness. Historical staging evidence remains below, with limits described at each check. The staging report currently reflects synthetic orders and is not merchant validation.

### Current Phase 7 tasks (2026-11-10)

| Gate | Status | Evidence completed | Remaining exit evidence |
|---|---|---|---|
| P7-11 | Pass — synthetic scope | Controlled HPOS ambiguity; authenticated HTTP exact replay; re-import, malformed/mismatch, duplicate and refund/credit preview | Real provider and concurrent race remain outside this synthetic gate |
| P7-12 | Partial | Isolated capability, nonce and request-bound rejection; source/static hardening | Independent full security/privacy review and browser/HTTP matrix |
| P7-13 | Pending | Measurement protocol only | Five instrumented runs each at 100/1,000/10,000 eligible orders on a suitable staging copy, correctness and budget |
| P7-14 | Pending | Beta workflow documented | Consented real merchants, sanitized issues/resolutions and owner acceptance |

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

**Current progress:** Phase 6 implementation is 6/6 complete. Provider account runtime verification, security review and broad compatibility checks remain open; overall percentage remains retired.

**Remaining verification:** runtime-test an ambiguous match using a controlled duplicate order-number provider/fixture, and directly replay an identical commit request to observe the server’s already-imported response. The current UI prevents a second commit once a row is saved, so it cannot exercise that lower-level retry branch. Report CSV download was verified on staging on 2026-11-06. Currency-mismatch, HPOS/legacy, multilingual, lower-bound and performance checks also remain. See [Phase 1–6 deep audit](docs/PHASE-1-6-DEEP-AUDIT.md). Keep implementation progress at 83% until the blocked sixth package or a formally revised phase scope is resolved.

## Build and download a staging ZIP

An installable ZIP is generated by the GitHub Actions workflow; the source tree itself does not contain generated vendor files.

1. Open the repository's **Actions** tab.
2. Choose **Build installable plugin ZIP** and select **Run workflow** on the branch you want to test. Plugin-source pushes to `main` also start a build.
3. Wait for the run to finish successfully, then download the `wpessential-shipping-cost-monitor-<commit>` artifact from that run.
4. Extract the downloaded Actions artifact bundle. It contains `wpessential-shipping-cost-monitor.zip` and its SHA-256 checksum.
5. Upload the inner plugin ZIP in WordPress admin under **Plugins → Add Plugin → Upload Plugin**.

The workflow generates Composer's PSR-4 `vendor/autoload.php`, runs PHP lint and an autoloader smoke check, then packages the plugin under the correct `wpessential-shipping-cost-monitor/` directory. Artifacts are retained for 14 days. This is a staging/test build, not a marketplace release. First successful run: [GitHub Actions run #42373229279](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42373229279), commit `bafc3caf07d022fcaafbf11fa3e755e8b253b7a6`. PHP lint, Composer PSR-4 generation/smoke, ZIP integrity and upload all passed. Artifact SHA-256: `1ce2781cb3fed3e187a9e0fe559546004ab8c9a4454ad3d8e9fa3dda0c7d2707`; expires 2026-11-21. The owner reported uploading this build; staging shows the plugin active, but no build SHA is exposed in its admin UI.
## TasteWP staging verification (2026-11-10)

The build produced by GitHub Actions run [#42707308330](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42707308330) was installed through WordPress admin and activated on the owner-provided TasteWP site. Environment observed: WordPress 7.1.4, WooCommerce 11.2.2. The Plugins screen showed **WPEssential Shipping Cost Monitor** active; the dashboard rendered and displayed build ID `9a7ae7e35951`.

Three synthetic, published simple products were imported through WooCommerce's product CSV importer: Demo Canvas Tote (SKU `smg-test-tote`), Demo Ceramic Mug (`smg-test-mug`), and Demo Gift Box (`smg-test-box`). The importer reported 3 imported, 0 failed, 0 updated, and 0 skipped. These are test fixtures only.

The dashboard had no eligible shipping orders and correctly showed no comparable confirmed costs. No orders or customer records were created on this TasteWP site, so this run verifies package installation, activation, build identification, dashboard rendering, and product fixture import only. It does **not** verify shipping-margin calculations, order-cost entry, invoice reconciliation, or P7-12 malformed/oversize request handling. Carrier APIs were not configured. Browser console errors observed during the session originated from the unrelated Backup Migration plugin and browser extension; no WPEssential Shipping Cost Monitor JavaScript error was observed.

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

> Dated entries below preserve what was recorded at that time, including superseded progress arithmetic. Current QA statuses come from docs/PHASE-7-GATES.md; do not treat historical 6/9 or overall percentages as current.

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


## 2026-11-07 staging report range boundary check

On the owner-designated staging site, the report accepted the maximum supported inclusive range of 366 days (2025-11-07 through 2026-11-07). A 367-day inclusive range (2025-11-06 through 2026-11-07) was rejected and the dashboard fell back to its default 30-day range. The report was then restored to 2026-10-09 through 2026-11-07; the existing synthetic totals remained unchanged. CSV export also completed in the same session. No orders or site settings were modified. This is a targeted report-filter smoke check within the existing staging lane, not an additional Phase 7 gate. Phase 7 remains 6/9 (67%); overall project progress remains 81%. The zlib ob_end_flush notice was still visible and remains untriaged.


## 2026-11-07 staging dashboard console smoke check

The existing Shipping Cost Monitor dashboard rendered its restored 2026-10-09–2026-11-07 report and the same synthetic totals (PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance, 2/2 coverage). A browser-console query filtered to the plugin admin script name `admin.js` returned no warning/error entries. The captured console did contain browser-extension errors; these are not attributed to the plugin. This is a narrow browser-session observation, not a full console, JavaScript, browser, or runtime regression test. No order or site setting was changed. No new Phase 7 gate is counted: Phase 7 remains 6/9 (67%), overall progress 81%. The unrelated zlib output-compression notice remains visible in WordPress admin.


## 2026-11-07 staging empty-report state check

On staging, the report was filtered to 2020-01-01 through 2020-01-30. The dashboard displayed “No eligible shipping orders found,” blank summary values and “No eligible orders in this period”; it did not show stale figures from the prior date range. The filter was restored to 2026-10-09 through 2026-11-07 and the original synthetic values returned (PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance, 2/2 coverage). No order or site setting was changed. This is a targeted empty-state smoke check, not a new Phase 7 gate or full QA result. Phase 7 remains 6/9 (67%); overall progress remains 81%. The WordPress zlib output-compression notice remains visible.


## 2026-11-07 source security review and input-shape hardening

Reviewed report export, invoice match/commit AJAX handlers, order-cost save fields, WooCommerce order CRUD boundaries and carrier status projection. The inspected paths use WooCommerce capability checks; export and AJAX actions use nonces; import commit is bounded to 50 rows, revalidates order/shipping/currency, rejects existing-cost overwrite, and uses order CRUD. Report text cells include spreadsheet-formula prefix neutralization. This was a focused source review, not an independent penetration test or complete security audit.

One defensive improvement was committed in `includes/Admin/OrderCostFields.php`: optional posted currency, evidence, tax-basis and note values are now rejected early unless they are strings, before sanitization. This is malformed-input hardening; no exploitable vulnerability or staging failure was established by the review. GitHub Actions run [#42384461173](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42384461173) passed PHP 8.1–8.4 Composer dependency/autoload and lint jobs, plus the installable ZIP build. Commit: `31fa4961e8343268516be67b2c49f39d88dddc41`. Artifact SHA-256: `f61b9abdf801188996dc377422ff63a9d444bc0dd0fa7169f06ad3f5ec969e64`.

The new source has not been installed on staging, and no malformed order-save request was sent because that path would require submitting an order edit. Therefore runtime behavior of this guard remains unverified. No local CLI was used. Phase 7 remains 6/9 (67%), overall progress 81%; full security review, runtime matrix, regression, performance and beta remain open.


## 2026-11-07 staging artifact handoff status

The hardened commit's GitHub Actions run [#42384461173](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42384461173) succeeded and produced artifact `wpessential-shipping-cost-monitor-31fa4961e8343268516be67b2c49f39d88dddc41` (artifact ID `13578379599`; SHA-256 `f61b9abdf801188996dc377422ff63a9d444bc0dd0fa7169f06ad3f5ec969e64`). The downloaded Actions artifact is an outer ZIP containing the installable plugin ZIP. The current browser security policy rejected a local `data:` URL used to extract the inner package, so no alternate browser route was attempted and no upload/install was made on staging. Runtime proof for the new input guard remains pending until the inner plugin ZIP is uploaded to staging. This environment restriction is not a staging-site failure. Phase 7 remains 6/9 (67%); overall progress 81%. To resume: download the artifact from the linked Actions run, extract the outer archive, upload its inner `wpessential-shipping-cost-monitor.zip` via WordPress Plugins → Add Plugin → Upload Plugin, and tell me when done.


## 2026-11-08 bounded performance source review

Reviewed `includes/Admin/Summary.php`, `includes/Admin/CsvExport.php`, and `includes/Admin/Page.php` at the current repository revision. Both report paths use WooCommerce `wc_get_orders()` with 100-order pages, date/status bounds, and `paginate`; dashboard assets are enqueued only on the plugin screen. The report loops through order objects and shipping items and keeps aggregated totals in memory. This is a bounded source review only: no benchmark, high-volume staging test, query-count trace, or runtime comparison was run. Potential follow-up is to measure representative large date ranges and assess object-query cost before changing pagination or aggregation. Staging recheck on 2026-11-08 still shows v0.1.0 active, so the hardened Actions artifact remains uninstalled. Phase 7 remains 6/9 (67%), overall progress 81%; performance validation is still open.


## 2026-11-08 performance gate measurement plan

The pending performance check now has a reproducible evidence outline; this plan is not a benchmark result and does not close a gate. Run only on a staging copy with disposable fixtures and record exact plugin commit, WP/WooCommerce/PHP/database versions, HPOS state, order count, date range, and whether the request is cold or repeat.

| Workflow | Fixture sizes | Capture for each of 5 runs | Correctness check | Status |
|---|---:|---|---|---|
| Dashboard summary load | 100, 1,000, 10,000 eligible shipping orders | Server request duration, peak PHP memory, database query count, and slow queries | Totals and coverage match an independent fixture aggregate | Pending staging benchmark |
| CSV export | 100, 1,000, 10,000 eligible shipping orders | Export duration, peak PHP memory, query count, response completion, and output row count | Export row count and representative values match fixture data | Pending staging benchmark |

Keep report date windows explicit and wide enough to include exactly the fixtures. Capture cold and repeat requests separately. Retain sanitized timing/query evidence and CSV row-count evidence; do not include customer personal data. Establish pass/fail latency and memory budgets only after capturing the current staging baseline and reviewing it, rather than treating an unmeasured target as a pass. No benchmark was run in this update; Phase 7 remains 6/9 (67%) and overall progress 81%.


## 2026-11-09 staging recheck

Read-only browser verification on the owner-designated staging site found WPEssential Shipping Cost Monitor v0.1.0 active. The dashboard rendered the same synthetic snapshot: PKR 750 shipping charged, PKR 1,950 confirmed cost, PKR -1,200 variance, and 2/2 eligible orders covered. The carrier adapter empty state remained visible (no provider credentials/adapters enabled). WordPress 7.1.4 was shown in admin; WooCommerce was previously observed as 11.2.2 on this staging lane. The WordPress ob_end_flush() zlib output-compression notice was visible twice in the admin response; no plugin fatal screen appeared. No order or setting was changed. The hardened Actions artifact remains uninstalled, so no new-code runtime or performance test occurred. Phase 7 remains 6/9 (67%), overall project 81%.


## 2026-11-10 plan and prompt structural repair

Added a canonical evidence-first execution protocol to AGENTS.md and routed AI-START-HERE.md, Cursor and Copilot through it. It requires agents to finish an unblocked acceptance-sized task in-session, avoid repeating unchanged blocked checks, distinguish implementation/static/runtime evidence, and keep documentation updates from counting as QA progress. Added [docs/PHASE-7-GATES.md](docs/PHASE-7-GATES.md), replacing the misleading historical 6/9 readiness score with 14 explicit gates and narrow acceptance evidence. Fixed a duplicate backlog ID: AI-37 remains the artifact-build task and the performance-baseline task is AI-39; AI-40 tracks this prompt/rules repair. The former aggregate 81% was retired because it averaged unlike phase deliverables and QA checks. No code or runtime gate was changed by this documentation work.


## 2026-11-10 P7-12 bounded-input hardening

Source review found that manual cost save and invoice match/commit enforced field shapes and row-count caps but did not consistently cap scalar lengths before sanitization/conversion. Added server-side bounds for row numbers, order IDs, references, amount strings, currency/evidence/tax keys, batch hashes and manual notes. The limits are documented in PRODUCT-SPEC.md; the manual note field now displays its 2,000-byte maximum. Follow-up review found raw strings could be trimmed or sanitized before those limits, and row identifiers could reach regex processing first. Commit ebc1df7 moves caps ahead of trimming/sanitization and adds a 128-byte nonce cap. This is defensive hardening, not a proven exploit or staging incident. The first bounds change passed run #42704528492; follow-up commit ebc1df7 passed PHP 8.1–8.4 checks and ZIP build/upload in run #42705243002. Report date inputs now enforce a 10-byte maximum before sanitization, and the shared date validator rejects other lengths before regex evaluation (commit 73562a6; run #42705680399 passed PHP 8.1–8.4 checks and ZIP build/upload). A later source trace found the dashboard path also sanitized date query values before the bound; commit a98cf58 applies the same 10-byte pre-sanitization cap there; run #42705989532 passed PHP 8.1–8.4 checks and ZIP build/upload. CSV export source review confirmed customer personal fields are excluded and spreadsheet-formula prefixes are escaped for exported text. Invoice CSV parsing is browser-local (5 MB file cap); mapping profiles save headers/settings only in localStorage. Matching sends mapped references; commit sends selected order/cost fields, not raw CSV rows or customer profile fields. The artifact is installed on the separate TasteWP site; runtime malformed/oversize request tests remain pending. P7-12 remains partial and P7-11 remains partial until runtime cases pass.


## 2026-11-10 Phase 7 corrected React build verified on staging

The Phase 7 staging pass exposed a React runtime exception in the new ShipStation carrier card (`useState is not defined`). Fixed in commit `bbf95766b0848dd17c23b483f77f0771503017d8`; GitHub Actions run [#42906678164](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42906678164) passed PHP 8.1–8.4 Composer/autoload/lint and installable ZIP build/upload (artifact ID `13804917827`, outer archive SHA-256 `c32ff77f71d758d938ecbf6c9c7940fafe3cca75ec7d4bc3486c89d4ce09dd05`). The installable package was uploaded to `wpessential.org/el-test`; WordPress reported successful replacement. The dashboard displayed build ID `bbf95766b084`; the carrier card, CSV controls and existing report rendered. Synthetic report remained PKR 750 charged, PKR 1,950 confirmed cost, PKR -1,200 variance, coverage 2/2. No ShipStation credential/API call or order mutation was made. The site-wide zlib output-compression notices remain. This closes the observed component-render bug and extends P7-02 selected-flow evidence; it does not close a new Phase 7 gate. P7-11/P7-12 are partial; P7-08/09/10/13/14 remain pending. Canonical evidence and exact outstanding inputs are in [Phase 7 gate register](docs/PHASE-7-GATES.md). Current count remains 7 narrow passes, 2 partial, 5 pending; Phase 7 is not complete.


## 2026-11-10 P7-08 and P7-09 staging evidence

On staging build bbf95766b084, manual shipping-cost persistence was checked in HPOS and legacy datastore modes on synthetic order #51 (PKR 1,100 changed to 1,101 and then restored). Invoice CSV matching/commit succeeded for synthetic order #103 (PKR 85) in legacy mode and #104 (PKR 95) in HPOS mode. Both had PKR 100 shipping. Report after both showed PKR 950 charged, PKR 2,130 confirmed cost, PKR -1,180 variance and 4/4 coverage; CSV download completed in each mode. HPOS was restored with compatibility mode off; test order #51 baseline cost was restored. Synthetic orders #103/#104 remain on staging as QA fixtures. A partial-refund attempt on #103 did not create a refund: WooCommerce's manual refund button remained PKR 0 after PKR 10 was entered, so the preview was canceled. Refund-adjusted report/export remains unverified.

P7-08 is partial: one runtime lane is WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 / MySQL 8.0.46. Baseline minimum WP 7.0 / PHP 8.1 and PHP 8.4 runtime lanes remain untested; CI matrix checks are not runtime integration tests. P7-09 is partial until refund adjustment passes in both data stores. Phase 7 gate counts are now 7 narrow passes, 4 partial, 3 pending. See [canonical gate register](docs/PHASE-7-GATES.md) for evidence and next actions.


## 2026-11-11 P7-08 minimum and PHP 8.4 runtime update

The exact installable package built from commit `bbf95766b0848dd17c23b483f77f0771503017d8` (ZIP SHA-256 `a01f715c09b872be4d734da64e3f18c56e5ec97b2506af76d09ed4703a2ab5fe`) was tested in temporary WordPress Playground sites. On the minimum lane, WordPress 7.0.8 / WooCommerce 11.2.2 / PHP 8.1.34, the plugin installed and activated, the manual shipping cost persisted on a synthetic shipping order, and the report calculated USD 100 charged, USD 85 confirmed cost, USD 15 variance, 1/1 coverage. On a separate latest WordPress lane, WordPress 7.1.4 / WooCommerce 11.2.2 / PHP 8.4.27, installation and activation succeeded and the React dashboard rendered with the expected empty-state controls and build ID. The existing real staging lane (WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 / MySQL 8.0.46) has already exercised selected report, manual-save, CSV commit and export paths.

P7-08 remains **Partial**: PHP 8.4 lane has dashboard/activation evidence but not yet an order/report workflow; the Playground environments use PHP.wasm and do not establish MySQL/MariaDB compatibility. The exact minimum-lane report flow is now verified. No new gate is counted as passed. Phase 7 remains 7 narrow passes, 4 partial and 3 pending.


## 2026-11-10 P7-09 refund-adjusted storage matrix

On the owner-designated el-test staging site (build `bbf95766b084`), synthetic shipping-only order #103 received a PKR 10 manual shipping-line refund (#105) with HPOS selected. The dashboard changed from PKR 950 charged / PKR 2,130 confirmed cost / PKR -1,180 variance to PKR 940 / PKR 2,130 / PKR -1,190. After order synchronization and switching to legacy posts storage, synthetic order #104 received a PKR 5 manual shipping-line refund (#106). The dashboard then showed PKR 935 charged / PKR 2,130 confirmed cost / PKR -1,195 variance, 4/4 coverage. CSV exports were downloaded in legacy and restored HPOS modes; both contained #103 with refund 10, net shipping 90, cost 85, variance 5, and #104 with refund 5, net shipping 95, cost 95, variance 0. HPOS was restored and compatibility sync disabled. The two synthetic refunds remain on staging as QA fixtures. This completes P7-09's stated manual save, invoice commit, refund-adjusted report and export matrix on both datastores. Phase 7 now has 8 narrow passes, 3 partial, 3 pending.


## 2026-11-10 P7-08 runtime matrix closure and scope revision

Exact CI package `bbf95766b084` (inner ZIP SHA-256 `a01f715c09b872be4d734da64e3f18c56e5ec97b2506af76d09ed4703a2ab5fe`) was installed in a fresh WordPress Playground PHP 8.4 lane. WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.4.27 (PHP.wasm): synthetic processing order #12 saved USD 100 flat-rate shipping and USD 85 invoice-final/tax-exclusive merchant cost. The dashboard reported USD 100 charged, USD 85 cost, USD 15 variance, 1/1 coverage. Earlier minimum lane WP 7.0.8 / WC 11.2.2 / PHP 8.1.34 passed the same order/report math. Real el-test staging WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 / MySQL 8.0.46 passed install replacement and selected order, report, refund, import and export workflows. P7-08 is Pass for this exact tested matrix. Playground uses PHP.wasm and does not establish MySQL/MariaDB on PHP 8.1 or 8.4; MariaDB remains untested and is not claimed as verified. WPML/WCML and Polylang compatibility were removed from release scope by owner decision. Former P7-05, P7-06 and P7-10 are retired, not counted as passes. Current Phase 7: 7 Pass, 2 Partial, 2 Pending of 11 active gates.
