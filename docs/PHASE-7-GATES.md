# Phase 7 quality and release gates

> **Current scope decision (2026-11-10):** WPML/WCML and Polylang integration and runtime compatibility are removed from the release requirements. Historical references below are superseded; no support claim is made. WordPress core translation APIs used by the plugin remain.

**Authority:** This is the canonical Phase 7 gate register. README shows a summary only. Use [AI audit and execution plan](AI-AUDIT-AND-EXECUTION-PLAN.md) for current cross-document evidence, environment distinctions and ordered next actions.  
**Last reviewed:** 2026-11-11  
**Current count (2026-11-11):** 11 active gates — 8 Pass (P7-01, 02, 03, 04, 07, 08, 09, 11), 2 Partial (P7-12, P7-13), 1 Pending (P7-14). The gate table is authoritative. Dated notes further down quote older counts (for example "7 passes, 2 partial, 5 pending", "P7-11 remains partial" or "P7-13 Pending"); those are history and are superseded by this line and the table.  
**Scope:** Compatibility, security, regression, performance, beta evidence for WPEssential Shipping Cost Monitor.  
**Interpretation:** Gate rows are not equal-sized. The prior “6/9 = 67%” mixed source dry-runs with runtime checks and is retired as a release-readiness percentage. Historical evidence is retained below, but Phase 7 closes only when every mandatory gate is passed or explicitly removed from supported scope by the owner.

## Gate register

| Gate | Requirement and exit evidence | State | Evidence / missing input |
|---|---|---|---|
| P7-01 Environment fingerprint | Record WP, WC, PHP, database, HTTPS and active order datastore on the target staging lane. | Pass — one lane | Staging System Status: WP 7.1.4, WC 11.2.2, PHP 8.3.36, MySQL 8.0.46, HTTPS; HPOS selected. Not a support matrix. |
| P7-02 Core dashboard and CSV smoke | Install/activate; report and export render; compare known synthetic data and confirm no fatal/admin JS failure. | Pass — selected flows | On the separate wpessential.org staging site, synthetic orders #51/#52 showed PKR 750 shipping charged, PKR 1,950 confirmed cost, PKR -1,200 variance, 2/2 coverage; CSV download observed. Separately, on 2026-11-10 the TasteWP site (WP 7.1.4/WC 11.2.2) displayed build ID `9a7ae7e35951` and its dashboard rendered; it had 3 imported synthetic products and 0 eligible orders, so no report calculation was exercised there. CI run #42707308330 passed PHP 8.1–8.4 checks and artifact packaging. The TasteWP PHP/database fingerprint is unknown. |
| P7-03 CI static/package lane | CI Composer PSR-4 install, PHP lint, autoload smoke, package ZIP creation and artifact upload pass. | Pass — CI only | Actions run #42906678164 passed PHP 8.1–8.4 dependency/lint jobs and installable ZIP packaging for commit bbf95766b084. This is not WordPress runtime coverage.
| P7-04 HPOS read/display | Read an HPOS order and display its stored shipping-cost evidence correctly. | Pass — selected flow | HPOS OrdersTableDataStore was active; order #52 cost/evidence appeared in report. Manual-save/import/refund writes are not covered. |
| P7-07 Legacy storage source boundary | Source review confirms order reads/writes use WooCommerce CRUD/query abstractions. | Pass — source only | No direct order-table access found. Legacy datastore runtime remains untested. |
| P7-08 Supported-version runtime matrix | Install/upgrade and core report/order workflows pass on declared minimum WP/WC/PHP and latest stable release matrix; publish exact tested versions. | Pass — exact tested matrix | Fresh PHP 8.4 Playground: WP 7.1.4 / WC 11.2.2 / PHP 8.4.27 (PHP.wasm), exact CI ZIP bbf95766b084, synthetic order #12 Processing with USD 100 shipping and USD 85 invoice-final tax-exclusive cost; dashboard USD 15 variance, 1/1 coverage. Prior minimum Playground: WP 7.0.8 / WC 11.2.2 / PHP 8.1.34, same USD 100/85/15 workflow. Real el-test staging: WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 / MySQL 8.0.46, install replacement plus selected HPOS/legacy order/report/import/refund/export flows. Exact runtime combinations only; Playground PHP.wasm does not prove MySQL/MariaDB on PHP 8.1/8.4, and MariaDB is untested. |
| P7-09 HPOS and legacy write-path matrix | Verify manual cost save, invoice commit, refund-adjusted report and export against HPOS and legacy storage; compare records/totals. | Pass — synthetic HPOS and legacy flows | Existing manual-save and invoice-commit checks passed on both stores. New el-test run on build bbf95766b084: HPOS order #103 manual shipping refund #105 PKR 10 reduced report net shipping 950→940 and variance -1180→-1190, cost 2130 unchanged; legacy order #104 manual shipping refund #106 PKR 5 reduced net shipping to 935 and variance to -1195, coverage 4/4. CSV exported in legacy and restored HPOS modes; both files contained #103 refund 10 / net 90 / cost 85 / variance 5 and #104 refund 5 / net 95 / cost 95 / variance 0. HPOS restored, compatibility sync off. Synthetic refunds remain as fixtures; merchant data not validated. |
| P7-11 Reconciliation edge/retry regression | Runtime tests for ambiguous order numbers, exact same-request replay response, safe re-import, malformed values, mismatch currency, refunds/credits and no-overwrite behavior. Save sanitized request/result evidence. | Pass — synthetic HPOS/selected staging cases | On build `ddca326`, authenticated HTTP AJAX first commit returned `committed`, byte-identical replay returned `already_imported` with false aggregate write flags; cost USD 9.25 and one import note remained ([HTTP evidence](P7-11-HTTP-REPLAY.md)). A controlled HPOS order-number/search collision returned `ambiguous` in preview and direct handler commit without writing ([ambiguity evidence](P7-11-AMBIGUITY-RUNTIME-PROBE.md)). Earlier isolated handler probes covered different-batch re-import/no-overwrite, invalid/negative amounts, currency/reference mismatch and duplicate targets ([sanitized results](P7-11-12-ISOLATED-RUNTIME-PROBE.md)); el-test browser preview covered refund-status/negative-credit exclusion and unmatched/duplicate references. These are synthetic exact flows, not a production order-number provider, concurrent race or carrier account validation. |
| P7-12 Security and privacy review | Independent review of capability/nonce boundaries, import size/row bounds, validation, formula injection, personal-data minimization, retention, failure/retry behavior; fix findings and attach evidence. | Partial — source hardening and static CI passed; runtime/independent review pending | New field byte caps reject oversized manual/import values. Follow-up source review found the manual order-editor nonce lacked an explicit cap; commit `e1eb345` adds a 128-byte pre-sanitization limit; GitHub Actions run #42823773223 passed PHP 8.1–8.4 Composer/autoload/lint checks and ZIP packaging/upload. Runtime evidence remains pending because the artifact is not installed on staging. Follow-up commit ebc1df7 moves raw-field, row-number and nonce limits before trimming/sanitization/regex work; the initial bounds commit passed run #42704528492 and follow-up ebc1df7 passed run #42705243002 (PHP 8.1–8.4 lint/autoload and ZIP build/upload). This addresses defensive resource bounds, not a demonstrated exploit. Report date inputs also reject oversized raw values before sanitization/regex (commit 73562a6; PHP 8.1–8.4 lint/autoload and ZIP build/upload passed in run #42705680399). CSV export source review confirmed customer personal fields are excluded and spreadsheet-formula prefixes are escaped for text cells. Import CSV parsing is browser-local with a 5 MB cap; localStorage stores mapping headers/settings, and match/commit requests send mapped fields rather than raw CSV rows/customer profile fields. Dashboard date query inputs now receive the same 10-byte pre-sanitization cap in Page::enqueue_assets (commit a98cf58; PHP 8.1–8.4 lint/autoload and ZIP build/upload passed in run #42705989532). Commit `e08f91b` adds a server-side preflight that rejects all rows targeting a duplicated order ID in one commit request, and the browser now classifies non-committed outcomes as review exceptions. PHP 8.1–8.4 CI passed in run #42837923258. On staging, duplicate references were withheld by the preview matcher before commit, so the new endpoint guard was not directly exercised. An isolated PHP 8.4.27 WordPress handler probe on build `bbf95766b084` rejected anonymous user, invalid nonce, 51-row request, malformed/negative amount and duplicate targets; [sanitized results](P7-11-12-ISOLATED-RUNTIME-PROBE.md). Full independent review, HTTP/browser transport and export/parser runtime security matrix remain open. A 2026-11-10 staging attempt to select a synthetic CSV reached the filename-selected state but the browser reported “The selected file could not be read”; no preview, AJAX commit or order write occurred. The exact-replay case remains pending. An ambiguous duplicate order-number fixture is still unavailable. A source audit found the browser CSV parser did not cap columns per row or individual field length; commit `35f0f1a` adds 100-column and 65,536-character limits while retaining the 5 MB file/5,000-row caps. Commit `8b0c1b7` also rejects malformed trailing data after quoted CSV fields. Earlier parser bounds passed Actions run #42826181980; the latest commit is queued in run #42826481179. The PHP/package workflow does not execute JavaScript. An isolated JS runtime check of the exact committed parser source passed valid CSV, escaped quotes, malformed trailing-text rejection, the 100/101-column boundary, 65,537-character rejection and 5,001-row rejection. This is not a browser/WordPress staging test; capability, nonce and staging malformed-request probes remain pending. |
| P7-13 Performance baseline | Execute `docs/AI-BACKLOG.md` item AI-39 on a staging copy at 100/1,000/10,000 eligible orders; five runs per size/workflow; capture elapsed time, peak PHP memory, DB query count/slow queries, CSV row count and correctness. Agree budgets after measuring baseline. | Partial — 100-order Playground summary lane only | Five summary runs on 100 synthetic orders measured 0.714 s cold, 0.043–0.057 s repeat, 114/2 query deltas, 81,788,928 peak PHP bytes; expected USD 1,198 / 1 covered matched. Five separate PHP CSV handler executions measured 0.637–0.821 s, 118 queries and exactly 100 data rows each. [Exact evidence](P7-13-PLAYGROUND-100-ORDER-BASELINE.md). 1,000/10,000, slow queries, independent HTTP timings and real database staging remain. |
| P7-14 Beta feedback | Run the approved beta workflow with real merchants; record consented, sanitized feedback, reproducible issues, resolutions and owner acceptance. | Pending | No beta feedback evidence. Merchant invoice samples and retention decision remain needed for some workflows. |

## Historical count reconciliation

The old 9-item denominator counted three staging/CI execution observations and three source dry-runs together, while leaving other work bundled under vague “matrix / QA / performance / beta” language. That made the percentage look like release readiness and failed to tell the next contributor exactly what to do. The prior count is superseded. The seven passes above are explicitly narrow evidence packages; two are source-only dry-runs and none closes the corresponding runtime gate.

## 2026-11-11 targeted P7-12 source finding

A focused source review found that `CsvExport::export()` accepted any numeric recorded cost when calculating comparable variance and exported it as a recorded cost. `Summary::build()` rejects negative cost, so malformed legacy/extension-inserted `-5` could appear as a comparable CSV variance while the dashboard excluded it. Commit [`3320120`](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/commit/33201201ade1fb4ed88b695c0954c1c9e0f9f893) applies the same nonnegative condition to CSV comparison and blanks the recorded cost cell for invalid negative values. The original order metadata is not modified. [CI run #42936293499](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42936293499) completed successfully for commit `3320120` (PHP checks and package); this is source/static remediation only: runtime CSV regression, full independent security/privacy review and HTTP/browser matrix remain open. P7-12 stays Partial; P7-13 has no measured benchmark and stays Pending. Reviewer must test a controlled negative-cost metadata fixture and validate that CSV row is not marked Comparable.

## Current blocking handoff

- **Staging state (2026-11-10):** build bbf95766b084 is installed on el-test; original HPOS datastore and compatibility-mode-off setting were restored after the legacy lane. Synthetic orders #103/#104 remain as QA fixtures, each with PKR 100 shipping; costs committed were PKR 85 via legacy and PKR 95 via HPOS.
- **Contributor action:** P7-08 and P7-09 are closed for their exact tested scopes. Continue P7-12 independent review, P7-13 benchmarks, and P7-14 real merchant beta. P7-11 closes for its listed synthetic regression flows including HTTP replay and controlled HPOS ambiguity. Finish P7-12 independent review and browser/security matrix. P7-13 needs instrumented 100/1,000/10,000-order staging copy; P7-14 needs consented participating merchants and owner acceptance.
- **Stop repeating:** do not re-open the same staging version/dashboard smoke as a new gate unless the installed build, dataset, environment, or test path changes.
- **No CLI rule:** no local/plugin CLI, npm or JS bundler. CI-only Composer/PHP checks are authorized; browser/Playground/staging checks remain valid. Do not let the no-local-CLI boundary block work that can be completed through these routes.

## Phase 7 close rule

Close Phase 7 only after all 11 active gates are passed with evidence or the owner records a narrower supported/release scope for the explicitly deferred lane. Any unresolved blocker must identify the responsible party and exact requested input. A percentage or completed source dry-run is not a substitute for a passed release gate.


## 2026-11-10 corrected-build staging verification

The prior React runtime failure in the ShipStation carrier card was fixed in commit `bbf95766b0848dd17c23b483f77f0771503017d8` by binding the WordPress-provided `wp.element.useState` hook in the component. GitHub Actions run [#42906678164](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42906678164) passed PHP 8.1–8.4 Composer/autoload/lint jobs and installable ZIP packaging/upload (artifact ID `13804917827`; outer artifact SHA-256 `c32ff77f71d758d938ecbf6c9c7940fafe3cca75ec7d4bc3486c89d4ce09dd05`). The installable ZIP was uploaded and WordPress reported “Plugin updated successfully” on the owner-designated `wpessential.org/el-test` staging site. The dashboard displayed build ID `bbf95766b084`, the React carrier card rendered with ShipStation V2 “Not connected”, the invoice CSV controls rendered, and existing synthetic summary values remained PKR 750 charged / PKR 1,950 confirmed cost / PKR -1,200 variance / 2 of 2 eligible orders. No carrier credential or API request was used and no order data was changed. The site-wide zlib `ob_end_flush()` notices persisted. This resolves the observed React render failure and confirms selected dashboard/carrier-card runtime smoke only; it does not close a new gate or prove the full browser console is clean.

**Current Phase 7 disposition:** P7-02 remains Pass — selected flows; P7-03 remains Pass — CI only. The evidence-gate count remains 7 narrow passes, 2 partial and 5 pending. P7-08 through P7-10 and P7-13 through P7-14 still require additional runtime matrices, add-on environments, measured workload fixtures or participating merchants. P7-11 and P7-12 remain partial until replay/ambiguous fixtures and runtime security probes/independent review are evidenced. These dependencies cannot be truthfully inferred from this staging smoke. The newest installed staging build is `bbf95766b084` (not the previously recorded `fd81d99`).


## 2026-11-11 P7-08 Playground runtime evidence

The installable ZIP built from commit `bbf95766b0848dd17c23b483f77f0771503017d8` (SHA-256 `a01f715c09b872be4d734da64e3f18c56e5ec97b2506af76d09ed4703a2ab5fe`) was installed and activated in two temporary Playground environments:

- Minimum lane: WordPress 7.0.8 / WooCommerce 11.2.2 / PHP 8.1.34. A synthetic shipping order saved merchant-paid cost USD 85 against USD 100 charged; dashboard showed USD 15 variance and 1/1 coverage.
- Latest PHP lane: WordPress 7.1.4 / WooCommerce 11.2.2 / PHP 8.4.27. Plugin activation and React dashboard rendering passed; the dashboard correctly showed the no-eligible-orders empty state. Order creation and report math were not tested on this lane.
- Both sites report PHP.wasm / Emscripten. They add WordPress/PHP runtime evidence but do not establish production database compatibility.
- Existing real staging evidence remains WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 / MySQL 8.0.46, with selected HPOS and legacy manual-save/import/report/export flows.

P7-08 remains Partial until an order/report workflow is run on PHP 8.4 and the declared database coverage is evidenced. Gate counts do not change: 7 narrow passes, 4 partial, 3 pending.


## 2026-11-10 P7-09 completion evidence

Synthetic refund #105 on HPOS order #103 was PKR 10 against the shipping line. Report: net shipping PKR 940, confirmed cost PKR 2,130, variance PKR -1,190. After synchronizing orders and selecting legacy posts storage, synthetic refund #106 on order #104 was PKR 5 against shipping. Report: net shipping PKR 935, confirmed cost PKR 2,130, variance PKR -1,195, 4/4 coverage. Both legacy and restored HPOS CSV exports showed #103 refund 10 / net 90 / cost 85 / variance 5, and #104 refund 5 / net 95 / cost 95 / variance 0. HPOS was restored with compatibility mode off. Earlier manual save and invoice commit evidence covers both datastores. P7-09 is Pass for the stated synthetic matrix. Current gate count: 8 Pass, 3 Partial, 3 Pending.


## 2026-11-10 scope and runtime matrix decision

Owner removed WPML/WCML and Polylang integration/support from the release requirements. Former P7-05 and P7-06 source dry-runs and P7-10 multilingual runtime gate are retired from the active register, not converted into passes. The 11 active gates contain 7 Pass, 2 Partial (P7-11, P7-12), and 2 Pending (P7-13, P7-14).

P7-08 passed on exact tested combinations: minimum Playground WP 7.0.8 / WC 11.2.2 / PHP 8.1.34; latest Playground WP 7.1.4 / WC 11.2.2 / PHP 8.4.27, with synthetic order #12 shipping USD 100, confirmed cost USD 85 and dashboard variance USD 15, 1/1 coverage; real staging WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 / MySQL 8.0.46 with install replacement and selected order/report workflows. Playground is PHP.wasm; no MariaDB or production MySQL on PHP 8.1/8.4 is claimed. Historical counts and pending statements above predate this decision.

## 2026-11-11 Playground P7-12 negative-cost regression

The CI package from commit `3320120` was installed in isolated WordPress Playground (WP 7.1.4, WC 11.2.2, PHP 8.3.36). A synthetic processing shipping order with USD 10 charged and malformed recorded cost `-5` was excluded from confirmed/comparable dashboard totals. Runtime CSV export left both Recorded cost and Comparable variance blank and reported `No confirmed cost`. [Fixture, exact CSV and limits](P7-12-PLAYGROUND-NEGATIVE-COST-CSV.md). This closes only the targeted CSV discrepancy check. P7-12 remains **Partial** pending independent security/privacy review and broader browser/HTTP probes; P7-13 and P7-14 remain Pending.

## 2026-11-11 P7-12 Playground import handler probes

On the installed `3320120` CI package in isolated WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 Playground, direct PHP handler calls rejected anonymous access, invalid and 129-character nonces, a 51-row batch, a 65-character amount, and a negative amount. The synthetic target order retained its pre-existing `-5` cost and blank import fingerprint. [Exact cases and evidence limits](P7-12-PLAYGROUND-IMPORT-BOUNDARY-PROBES.md). These are handler-runtime probes, not HTTP AJAX/browser transport. P7-12 remains **Partial** pending the broader matrix and independent review; gate counts do not change.

## 2026-11-11 P7-12 Playground HTTP export check

On the installed `3320120` package, the dashboard-generated CSV export URL returned HTTP 200 with a CSV attachment response. Synthetic order #13's malformed `-5` recorded cost and comparable variance were blank, with `No confirmed cost`; the dashboard showed 0/1 coverage. Replacing its nonce with an invalid value returned HTTP 403. [Response and limitations](P7-12-PLAYGROUND-HTTP-EXPORT.md). The browser click did not expose a download event, so this is HTTP route evidence, not browser download UX. P7-12 remains **Partial** pending independent review and remaining browser/import transport matrix.

## 2026-11-11 P7-12 authenticated import HTTP checks

On the installed `3320120` Playground build, actual authenticated `admin-ajax.php` POSTs rejected invalid and 129-character nonces with HTTP 403, rejected a 51-row batch with HTTP 400, and classified a 65-character numeric amount as `invalid` with `persisted:false`/`committed:false`. The synthetic order's pre-existing `-5` cost and empty fingerprint remained unchanged. [Exact response matrix and limits](P7-12-PLAYGROUND-HTTP-IMPORT-BOUNDARIES.md). This adds HTTP transport evidence, while independent review and full browser upload/preview matrix remain open. P7-12 stays **Partial**.

## 2026-11-11 P7-12 browser CSV import flow

The installed `3320120` package on isolated Playground completed file selection, browser parsing/mapping, match, confirmation, AJAX save, WooCommerce CRUD persistence and report refresh for a synthetic invoice row. Existing malformed cost on order #13 was shown as a comparison and withheld from overwrite; fresh order #14 committed USD 8.25 invoice-final tax-exclusive against USD 12 shipping. The refreshed dashboard showed USD 3.75 comparable variance and 1/2 coverage. [Full flow and limits](P7-12-PLAYGROUND-BROWSER-CSV-FLOW.md). This closes the selected browser CSV workflow gap, but P7-12 remains **Partial** until independent security/privacy review and remaining matrix are complete.

## 2026-11-11 shipping-method table correction

A mixed comparable/noncomparable fixture revealed that the method row displayed one comparable order beside the USD 22 charged total for two orders. Commit [`3b8b5c9`](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/commit/3b8b5c9162ca78a6e2fe99cf2b03cc3da49f4fc4) uses the total method order count for that column. A direct edit to the isolated Playground test copy rendered **2 / USD 22.00 / USD 8.25 / USD 3.75** after refresh. [Regression evidence](P7-12-METHOD-COUNT-REGRESSION.md). A CI artifact of this later commit has not been installed; the source edit does not close P7-12 independent review.


## 2026-11-11 release-preparation source review (P7-12 evidence, not closure)

A source review of every PHP entry point confirmed each AJAX and admin-post handler checks `manage_woocommerce` and a bounded nonce before reading input, and that order writes also check `edit_shop_order`. No direct database access, unescaped output or unbounded input was found. This review was done by an AI contributor, so it does not satisfy the independent-review requirement.

An isolated PHP 8.3.6 stub harness (no WordPress; WordPress functions replaced by minimal stand-ins) exercised `ShipStationAdapter`: a too-short key and an HTTP 401 probe saved nothing; a valid key was stored only as an AES-256-GCM envelope with no plaintext; a tampered ciphertext returned `reauth_required`; default salts were rejected; a 32-day range and an invalid calendar date were rejected; voided, return and negative-cost labels were dropped; requests went only to the fixed `https://api.shipstation.com/v2/labels` host. `uninstall.php` removed the stored envelope, and the bootstrap registered an admin notice instead of failing when `vendor/autoload.php` was absent. PHP lint and a JavaScript syntax check passed on the working tree. These are static and stub results: no WordPress runtime, no real ShipStation account and no CI run are claimed.

Changes in the same change set that need runtime confirmation (backlog AI-45): HPOS compatibility declaration, autoloader guard, `uninstall.php`, dashboard layout and style changes, and the packaged `readme.txt`, `changelog.txt` and `LICENSE`.


## 2026-11-11 Playground runtime check of `main` at `a872b191de0e` (AI-45 / AI-46)

**Environment:** playground.wordpress.net, WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3 (PHP.wasm, SQLite), Plugin Check 2.2.0. **Build:** repository source of `main` at `a872b191de0e` installed through a Blueprint `git:directory` resource, with a hand-written PSR-4 `vendor/autoload.php` stand-in and a `BUILD-ID.txt`. This is **not** the CI ZIP: the Composer autoloader and the package contents were not exercised here (CI run #43052869273 covers those statically).

**Fixture:** two synthetic Processing orders created through WooCommerce CRUD, each with one Flat rate shipping line: USD 12 with a recorded cost of USD 8.25 (invoice-final, tax-exclusive), and USD 10 with no cost.

**Observed:**
- Plugin activated; dashboard rendered with build ID `a872b191de0e`.
- Summary: USD 22.00 charged, USD 8.25 confirmed cost, USD 3.75 variance (positive style), 50% coverage (1 / 2). Matches the fixture.
- Method table: Flat rate (USD), 2 orders, USD 22.00 charged, USD 8.25 confirmed cost.
- New layout confirmed at an 800px-wide viewport: report cards precede the invoice importer; method and carrier cards have inner spacing.
- The Plugins screen showed no HPOS-incompatibility notice for the plugin. The WooCommerce Features screen itself was not opened, and whether HPOS was the active datastore on this site was not recorded.
- Plugin Check (General, Plugin Repo, Security; errors and warnings): "1 error and 90 warnings".
  - Error: `fclose()` on the `php://output` CSV stream in `CsvExport.php`.
  - Warnings from installing the raw repository (`.cursor`, `.github`, `AGENTS.md`, `CLAUDE.md`, `GEMINI.md`): these files are not in the CI ZIP.
  - Warnings: `Domain Path` pointing at a missing `languages` folder; discouraged `load_plugin_textdomain()`.
  - Remaining warnings seen were `NonceVerification.Missing` and `InputNotSanitized` in the AJAX handlers and date inputs. In the lines inspected, the nonce is verified in a helper (`authorize()`) or before use, and input is length-checked before sanitizing, which the sniffs do not follow. Not every one of the 90 rows was read.

**Fixed in the follow-up change:** the stream calls carry a justified `phpcs:ignore`; the `Domain Path` header and `load_plugin_textdomain()` call were removed (WordPress loads translations for the slug); the method table no longer forces horizontal scrolling at this width; "1 orders" now uses a plural-aware string.

**Not tested:** uninstall on a real site, the ShipStation connection, the invoice import flow on this build, a narrow phone viewport, and a Plugin Check re-run after the fixes. P7-12 stays Partial.


## 2026-11-11 Plugin Check warning triage (P7-12 evidence, not closure)

The Plugin Check run above reported nonce and sanitization findings that were not all read in the browser. They were reproduced statically on `main` at `2deece9` with PHP_CodeSniffer 3.13.2 and WordPress Coding Standards 3.1.0 (security, alternative-function, I18n and database sniffs): 34 errors and 7 warnings in 7 files.

Each flagged line was read. None was an exploitable defect:
- `CarrierSyncController`: nonce and capability are verified in `authorize()` before any input is read; the sniff does not follow the helper call.
- `InvoiceImporter`, `ReferenceMatcher`, `CsvExport`: the flagged reads are the nonce check itself, or input read after the nonce check, length-capped before sanitizing (the bounded-input hardening recorded earlier for P7-12).
- `OrderCostFields`: raw values are type- and length-checked and the handler returns early; nothing is saved until the nonce and `edit_shop_order` are verified.
- `Page`: read-only report date filter on a capability-checked screen.
- `file_get_contents()` reads the plugin's own 41-byte `BUILD-ID.txt`.
- `base64_encode/decode` store the AES-256-GCM credential envelope; not obfuscation.

Each line now carries a `phpcs:ignore` (or a `phpcs:disable`/`enable` pair for the base64 envelope) with its reason. Code behaviour is unchanged. After the change the same sniffs report no findings, and the ShipStation/uninstall stub harness still passes all cases. Remaining WordPress-Extra findings are file names (PSR-4 class files) and WooCommerce capabilities unknown to WPCS; both are intentional. Plugin Check itself has not been re-run on this change.


## 2026-11-11 Plugin Check re-run after triage (P7-12 evidence, not closure)

**Environment:** fresh non-persistent playground.wordpress.net site (`storage=none`), WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3 (PHP.wasm), Plugin Check 2.2.0. **Source:** `main` at `93c63a4` through a Blueprint `git:directory` resource with a hand-written autoloader stand-in (not the CI ZIP). An earlier attempt reused an autosaved site with old code and was discarded.

**Result (General, Plugin Repo, Security; errors and warnings):** "Checks complete. 8 warnings found." No errors.
- 5 warnings: `.cursor`, `.github`, `AGENTS.md`, `CLAUDE.md`, `GEMINI.md` in the plugin root. These are repository files; the CI package copies only `assets`, `includes`, `vendor` and the release files.
- 3 warnings `trademarked_term`: the plugin name and slug contained the restricted term "wp". Addressed by decision F-5 (rename to Shipping Cost Monitor for WooCommerce / `shipping-cost-monitor-for-woocommerce`) in the follow-up change; the text domain, menu slug, main file and package folder were renamed with it.

After the rename, PHP lint, the JavaScript syntax check, the WPCS security/I18n sniffs with `text_domain=shipping-cost-monitor-for-woocommerce` and the ShipStation/uninstall stub harness all pass. Plugin Check has not yet been run on the renamed package.


## 2026-11-11 Plugin Check on the renamed plugin (`main` at `2aee2e461f8d`)

**Environment:** fresh non-persistent playground.wordpress.net site, WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3 (PHP.wasm), Plugin Check 2.2.0. Source installed into `shipping-cost-monitor-for-woocommerce/` through a Blueprint `git:directory` resource with a hand-written autoloader stand-in (not the CI ZIP).

- Activation succeeded; the dashboard at `page=shipping-cost-monitor-for-woocommerce` showed build ID `2aee2e461f8d`. One synthetic Processing order (USD 12 Flat rate, USD 8.25 invoice-final tax-exclusive cost) gave USD 12.00 charged, USD 8.25 confirmed cost, USD 3.75 variance, 100% coverage (1 / 1); the note read "1 order with…" (singular) and the method table fit without horizontal scrolling.
- Plugin Check (General, Plugin Repo, Security; errors and warnings): **"Checks complete. 5 warnings found."** No errors. All five are repository-only files (`.cursor`, `.github`, `AGENTS.md`, `CLAUDE.md`, `GEMINI.md`) that the CI package does not contain. The `trademarked_term` warnings are gone.

**Still not covered:** Plugin Check on the CI ZIP itself (which would also exercise the Composer autoloader), uninstall on a WordPress site, the legacy-build pause notice in a real WordPress install (verified only in a PHP stub), invoice import on this build, phone-width layout, and the ShipStation connection. P7-12 stays Partial pending independent review.

## 2026-11-11 Free/Pro split and 1.0.0 runtime check (AI-47)

**Environment:** fresh non-persistent playground.wordpress.net site, WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3 (PHP.wasm, SQLite, legacy order storage in this site). **Source:** branch `claude/v1-free-pro-docs` at `fa34146`, free plugin from the repository root and Pro from `pro/` through Blueprint `git:directory` resources, each with a hand-written PSR-4 autoloader stand-in (not the CI ZIPs).

**Fixture:** six Processing orders created through WooCommerce CRUD (USD): Flat rate 12.00 / cost 8.25 invoice-final exclusive; Flat rate 10.00 / 11.40 invoice-final exclusive; Flat rate 15.00 / no cost; Express 25.00 / 19.90 merchant-confirmed exclusive; Express 25.00 / 27.10 invoice-final exclusive; Local pickup 5.00 / 4.00 booked label, tax unknown.

**Free only (Pro inactive):** dashboard showed USD 92.00 charged, USD 66.65 confirmed cost, USD 5.35 variance (green), 66.7% coverage (4 / 6); method table Flat rate 3 / 37.00 / 19.65 / 2.35 and Express 2 / 50.00 / 47.00 / 3.00; the booked-label cost was excluded. All match hand calculation. No build ID shown; buttons use one style; content fills the width with the standard 20px right margin. The Pro add-on card replaced the importer.

**Free + Pro:** Pro activated with "Plugin activated."; the Plugins screen showed the free plugin as "Required by" Pro. `window.wpeScmPro` defined, invoice panel and carrier card rendered, the ShipStation key field showed both help links, no console errors. Dashboard data included Pro nonces and `carrier_adapters` (ShipStation, disconnected). Authenticated AJAX from the dashboard page: match preview returned `duplicate` for `# 12` and `12`, `existing_cost` with the stored USD 8.25 for order 10, `unmatched` for 99999; commit to order 12 returned `committed`, identical replay `already_imported` (`persisted:false`); invalid nonce HTTP 403; ShipStation sync without a key HTTP 400 "Connect a ShipStation API key before syncing.". A browser-injected four-row CSV auto-mapped Order / Charge / Currency, and matching produced existing-cost comparisons, `unmatched` and "Credit/refund needs review" for the negative row.

**Found and fixed during this run:** the Pro review column showed raw codes (`unmatched`, `duplicate`, `committed`, `merchant_confirmed`); they now use translated labels. The CI autoload smoke test had been exiting with status 0 before checking any class because class files exit without `ABSPATH`; it now defines `ABSPATH` and asserts its output.

**Not covered:** the two CI ZIPs themselves, Plugin Check on the split packages, the label fix in a browser (PHP lint and `node --check` only), HPOS datastore on this site, and Pro licensing/updates (not built).


## 2026-11-11 Real CI artifacts installed and checked (AI-45 / AI-47)

**Build:** CI run 43078644836 on branch `claude/docs-link-woo-billing` at `e078de7`, artifacts `shipping-cost-monitor-for-woocommerce-e078de7…` and `shipping-cost-monitor-pro-for-woocommerce-e078de7…`, installed into a fresh non-persistent Playground (WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3, Plugin Check 2.2.0) through Blueprint `url` resources pointing at the artifacts' short-lived download links. This is the first runtime check of the **CI packages themselves**, including the Composer autoloaders.

**Artifact layout:** before this change each artifact ZIP wrapped the plugin ZIP and a checksum, so uploading the downloaded file in WordPress failed with "Plugin installation failed." (owner report). Artifacts now upload the plugin folder; each downloaded file installed as-is into `shipping-cost-monitor-for-woocommerce/` and `shipping-cost-monitor-pro-for-woocommerce/`.

**Observed:** both plugins active; the free plugin shows "Required by" Pro. Plugins screen row actions: Free — Deactivate | Check this plugin | **Documentation** | **Dashboard**; Pro — Deactivate | Check this plugin | **Documentation**, all pointing to the renamed repository. Dashboard with three synthetic USD orders (12.00 / 8.25 invoice-final; 15.00 / no cost; 25.00 / 27.10 invoice-final): 52.00 charged, 35.35 confirmed cost, 1.65 variance, 66.7% coverage, matching hand calculation; Documentation button in the header; Pro panel and ShipStation help links present; `admin.js` then `pro.js` loaded with HTTP 200.

**Plugin Check on the packages:**
- Free: "1 warning" — `missing_composer_json_file`. Fixed in the follow-up commit by packaging `composer.json`.
- Pro: "2 errors and 2 warnings" — `missing_composer_json_file` (fixed the same way); `plugin_updater_detected` for `Update URI`, `no_plugin_readme`, and `plugin_header_requires_plugins_not_in_directory`. The last three are WordPress.org-directory rules; Pro is distributed on WooCommerce.com (F-8), which requires the `Update URI` behaviour and `changelog.txt`, and the dependency warning clears once the free plugin is published on WordPress.org.

**Not covered:** uninstall on this site, ShipStation with a real key, invoice import through the UI on the packaged build, WooCommerce QIT.

### 2026-11-12 — Built-in documentation tab (F-10)

**Environment:** WordPress Playground, PHP 8.3, latest WordPress, WooCommerce from WordPress.org; Free and Pro installed from the CI artifacts of run 43104088508 (commit 1205b6c on `claude/in-plugin-docs`).

**Checked:**
- The Plugins screen shows **Documentation** and **Dashboard** beside Deactivate for Free. Documentation opens `admin.php?page=shipping-cost-monitor-for-woocommerce&tab=docs` (Contents) in the same tab.
- The chapter list works, and chapter 4 renders with its screenshots from `assets/docs/images/`.
- The in-guide link "six example orders" opens chapter 2 at the matching section. The first build (run 43103695394) produced `chapter02-key-ideas` without `=`; fixed in 1205b6c and rechecked.
- The dashboard still loads with Free + Pro active (Pro invoice panel present). Its header shows the Documentation button.

**Not covered:** Pro's own Documentation row link, the narrow (mobile) layout, translations.
