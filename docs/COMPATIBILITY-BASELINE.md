# Compatibility baseline — proposal

**Research snapshot:** 2026-11-07  
**Status:** Proposed implementation baseline; Phase 7 execution started 2026-11-07. Staging verified one current-platform + HPOS lane on WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3.36 and MySQL 8.0.46. Official release listings checked 2026-11-07 show WordPress 7.1.4 and WooCommerce 11.2.2 as current stable; the staging pair matches that observed current lane. This is one tested environment, not a compatibility matrix or broad support claim.  
**Scope:** WPEssential Shipping Cost Monitor core plus optional WPML and Polylang multilingual-store coexistence.

## Recommended baseline for the first release

| Layer | Proposed baseline | Evidence / implementation note |
|---|---|---|
| PHP | **8.1 minimum; 8.3+ recommended.** Test 8.1–8.4; add 8.5 only when WooCommerce and the chosen dependencies officially support it. | WooCommerce plans to require PHP 8.1 from 11.6; WooCommerce recommends 8.3+. Selecting 8.1 now avoids shipping on a runtime already scheduled to leave the supported baseline. |
| WordPress | **7.0 minimum** for the current WooCommerce 11.2.2 development target; then test the newest stable WordPress at release time. | The WooCommerce 11.2.2 activation check reported WordPress 7.0 as its minimum; the official WooCommerce plugin readme on its current repository also declares 7.0. WooCommerce 10.8's earlier 6.9 floor does not apply to this later target. Recheck at release.
| WooCommerce | **11.2.2 current stable at snapshot; target 11.2+ for initial development.** Add latest stable and next beta to pre-release verification when available. | 11.3 is beta and scheduled for the week of 2026-11-09; do not declare it supported until stable and tested. WooCommerce documents per-version minimum WordPress/PHP requirements. |
| Database | MySQL **8.0+** or MariaDB **10.6+** recommended; test both if infrastructure permits. | Align with WooCommerce server recommendations. Plugin tables must use WordPress database APIs/upgrade conventions. |
| Memory / transport | WordPress memory limit **256 MB+** recommended; HTTPS required for provider connections. | Align with current WooCommerce server recommendations. This is not a guarantee of capacity for large imports. |
| WPML | Optional, no runtime dependency. Target current WPML Multilingual CMS + WooCommerce Multilingual at release; language/currency data must remain separate from cost math. | WPML lists WP 6.0+, PHP 7.4–8.5 as its own minimum range; its WooCommerce add-on is independently versioned and tested. The plugin must test the actual supported combination, not infer compatibility from WPML's minimum. |
| Polylang | Optional, no runtime dependency. For multilingual WooCommerce, test Polylang/Polylang Pro **with Polylang for WooCommerce**. | Polylang's WooCommerce add-on lists WordPress 6.5+, WooCommerce 9.2+, Polylang 3.7+ as its current minimum. Polylang core alone is not a multilingual WooCommerce setup. |
| Admin UI | React for bespoke workflows; register pages through WooCommerce-supported extension admin APIs. Keep business rules and persistence on the PHP server. | Prefer stable page-registration APIs. Do not depend on experimental React Settings UI until WooCommerce marks it stable and the project explicitly chooses it. |

These are project recommendations, not claims that the plugin currently supports those versions. At release, raise the minimum if the chosen WooCommerce stable line requires it. Avoid advertising broader compatibility than the tested matrix.

## Verification matrix required before support claims

| Test lane | WordPress | WooCommerce | PHP | Database | Multilingual |
|---|---|---|---|---|---|
| Minimum supported | 7.0 | WooCommerce 11.2.2 development target | 8.1 | MySQL 8.0 | None |
| Recommended baseline | Latest stable at release (7.1.4 observed in Playground) | 11.2.2 stable at snapshot | 8.3 | MariaDB 10.6 | WPML current |
| PHP upper lane | Latest stable WP | Latest stable WC | 8.4; 8.5 after official stack support | One supported DB | Polylang + Polylang for WooCommerce current |
| Forward check | Latest stable WP | Next WooCommerce beta before release, then stable | New PHP minor after official support | Both DB families where available | Run integrations independently and together only if supported |

Every lane must cover install/upgrade/deactivation, manual cost record, CSV import/export, REST/admin permissions, background job retry, multilingual admin strings, locale-specific date/decimal parsing, and order read/write. Test both HPOS and legacy order storage before declaring HPOS compatibility. Any failed lane narrows the advertised matrix; document the exact tested versions.

## Plugin engineering constraints

- Use WordPress and WooCommerce APIs; never make assumptions from database table names for orders.
- Register plugin translations and use translatable UI strings; do not store translated display strings in numeric/business records.
- Store amounts with explicit currency and basis; locale parsing happens at import boundary, and all numeric values are normalized before matching/reporting.
- Do not convert currencies implicitly. WPML multi-currency display settings do not authorize cross-currency sum or conversion in the plugin's reports.
- Keep WPML/Polylang integrations optional and feature-detected; missing multilingual plugins must not break the core plugin.
- Use the WordPress/WooCommerce React page pathway for the app-like admin screens; server-side capability checks, validation and persistence remain authoritative.
- Avoid dependency on WooCommerce's experimental settings React renderer. The stable PHP settings API remains the safer fallback for simple configuration until the new path is stable.
- State the actual `Requires PHP`, `Requires at least` and tested-up-to metadata only after the release matrix has passed.

## Decisions still needed at Gate C / release planning

1. Confirm whether PHP 8.1 is the chosen minimum or adopt 8.3 to align with current production recommendations.
2. Reconfirm the newest stable WordPress/WooCommerce pair immediately before implementation and Marketplace submission.
3. Confirm support for both MySQL and MariaDB in the project test environment.
4. Decide whether WPML and Polylang are formal release support targets or best-effort compatibility targets, and test the WooCommerce-specific add-ons.
5. Set an explicit policy for the latest beta lane; beta testing never equals a stable compatibility claim.

## Phase 7 execution log — 2026-11-07

| Gate | Result | Evidence / boundary |
|---|---|---|
| Current-platform staging lane | Pass (single environment) | WooCommerce System Status showed WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3.36, MySQL 8.0.46 and HTTPS. Plugin v0.1.0 was active; dashboard rendered the synthetic 2-order report. No other version lane is implied. |
| HPOS order read + stored cost display | Pass (selected flow) | HPOS was enabled and OrdersTableDataStore was active. Order #52 showed its imported PKR 850 invoice-final, tax-exclusive cost and private import note. Dashboard report read the same synthetic order set. This does not cover manual save, refunds, or all HPOS order states. |
| Minimum PHP 8.1 / WordPress 7.0 lane | Pending | Staging runs PHP 8.3.36 and WordPress 7.1.4; minimum lane not available. |
| PHP 8.1–8.4 lint/autoload matrix | Pass (CI run #42374396949) | Composer install, PHP lint and PSR-4 class smoke passed on PHP 8.1, 8.2, 8.3 and 8.4. This is static/autoload verification, not full WordPress/WooCommerce runtime coverage. |
| PHP runtime under WordPress/WooCommerce | Pending | The plugin was runtime-observed only on PHP 8.3.36 + WP 7.1.4 + WC 11.2.2. PHP 8.1/8.2/8.4 runtime combinations remain untested. |
| Legacy order storage | Source dry run complete; runtime pending | Repository search found order reporting/export via wc_get_orders() and no direct $wpdb, wp_posts, or update_post_meta order-storage paths. The source follows WooCommerce CRUD abstractions, which are intended to support both storage backends. Legacy datastore was not activated or runtime-tested. |
| WPML + WooCommerce Multilingual | Dry run complete; runtime pending | Source review found no direct WPML dependency/API calls. Core order queries use WooCommerce APIs and cost records are attached to order IDs. PHP strings use WordPress translation functions; React strings use `wp.i18n` and `wp_set_script_translations()` is registered. No `languages/` catalog directory is present in the repository. No WPML/WCML runtime interaction was exercised. |
| Polylang + Polylang for WooCommerce | Dry run complete; runtime pending | Source review found no direct Polylang API calls or hard dependency. Core order queries use WooCommerce APIs; currency values remain explicit and are not implicitly converted. PHP/React localization hooks are present, but no `languages/` catalog directory exists. No Polylang/WooCommerce add-on runtime interaction was exercised. |
| Regression, security, performance and beta review | Partial source review; full gate pending | Focused source review covered capability/nonce checks, bounded import, WooCommerce CRUD, overwrite protection and CSV formula guard; malformed optional manual-save fields now fail early and CI passed. No penetration test, runtime malformed-input test, automated suite, benchmark or beta feedback was run. |

**Phase 7 progress:** 6 of 9 planned verification items complete (67%): 3 execution checks passed (current staging lane, selected HPOS read/display, PHP 8.1–8.4 lint/autoload CI) and 3 source-based dry runs completed (WPML/WCML, Polylang/WooCommerce add-on, legacy order-storage API boundary). The three dry runs establish only that source has no hard multilingual dependency and uses WooCommerce storage abstractions; they do not establish runtime interoperability, translated UI coverage, or a legacy datastore support claim. Full WP/WC runtime lanes, minimum versions, legacy storage, live multilingual checks, translation-catalog coverage, and regression/security/performance/beta remain open.

## Research sources

- [WordPress server requirements](https://wordpress.org/about/requirements/)
- [WooCommerce server recommendations](https://woocommerce.com/document/server-requirements/)
- [WooCommerce PHP and WordPress version table](https://woocommerce.com/document/update-php-wordpress/)
- [WooCommerce 11.2 release list](https://developer.woocommerce.com/releases/)
- [WooCommerce 11.3 beta schedule](https://developer.woocommerce.com/2026/10/26/woocommerce-11-3-pre-release/)
- [WooCommerce PHP 8.1 requirement announcement](https://developer.woocommerce.com/2026/09/29/php-8-1-requirement/)
- [WPML minimum requirements](https://wpml.org/home/minimum-requirements)
- [WPML WooCommerce add-on changelog](https://wpml.org/version/wpml-multilingual-multicurrency-for-woocommerce/)
- [Polylang for WooCommerce changelog](https://polylang.pro/polylang-for-woocommerce-changelog/)
- [Polylang FAQ: WooCommerce multilingual setup](https://polylang.pro/documentation/support/faq/)
- [WooCommerce extension admin pages](https://developer.woocommerce.com/docs/extensions/settings-and-config/working-with-woocommerce-admin-pages)
- [WooCommerce Settings UI stability note](https://developer.woocommerce.com/docs/extensions/settings-and-config/settings-ui/)

Research is a dated public-doc review. It does not establish account-specific integrations, plugin interoperability, PHP runtime behavior or release compatibility.


CI follow-up: an initial matrix run failed because the PSR-4 smoke command escaped the class name incorrectly. The workflow was corrected; run [#42374396949](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42374396949) then passed all four PHP matrix jobs and the ZIP build. Artifact digest: `sha256:1a0abd3c406aaa9c7debb68d85a1961ae666d7d416883b63601aa46adb16edd6`. The passed matrix checks dependency install, syntax and autoload only.


## Phase 7 staging artifact replacement — 2026-11-07

The checksum-verified ZIP from Actions run [#42374396949](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42374396949), digest `sha256:1a0abd3c406aaa9c7debb68d85a1961ae666d7d416883b63601aa46adb16edd6`, was selected in the owner-designated WordPress installer. The installer showed current and uploaded version 0.1.0; **Replace current with uploaded** completed with “Plugin updated successfully.” Installed Plugins still showed Shipping Cost Monitor active at 0.1.0. The dashboard rendered its report (PKR 750 charged, PKR 1,950 confirmed, PKR -1,200 variance, coverage 2/2), and order #52 showed PKR 850 invoice-final/tax-exclusive with its private import note. The storefront loaded after update. These observations add exact upload/replacement evidence to the existing current-platform lane; they do not add a separate compatibility gate, so Phase 7 remains 3/9 (33%). No fatal error or recovery-mode screen was observed.

**Observed staging notice:** admin installer, dashboard, order page and storefront showed `ob_end_flush(): Failed to send buffer of zlib output compression (1)` at `wp-includes/functions.php:5581`. Cause is undetermined; do not attribute it to the plugin without diagnosis. Track it as an environment notice to investigate before release-quality claims.

The WordPress admin does not display the installed plugin file digest. The selected CI artifact, its checksum, and successful overwrite are known from the upload flow, but a post-install filesystem hash was not obtained. Runtime observations remain limited to WP 7.1.4 + WC 11.2.2 + PHP 8.3.36 + MySQL 8.0.46 with HPOS enabled. Minimum versions, PHP runtime matrix, legacy datastore, WPML/WCML, Polylang for WooCommerce, and regression/security/performance/beta lanes remain pending.


## 2026-11-07 WPML / Polylang dry-run review

The staging Plugins screen showed five installed plugins (four active), including Shipping Cost Monitor v0.1.0 active, with no WPML, WooCommerce Multilingual or Polylang add-on present. The owner authorized a dry run. Source review found no direct WPML (`icl_*`) or Polylang (`pll_*`) API calls or hard dependencies. Order reporting uses WooCommerce order query/CRUD APIs; shipping-cost values are attached to order records and currency is explicitly stored rather than implicitly converted. PHP UI strings use WordPress gettext functions. The React admin uses `wp.i18n`; the page registers `wp_set_script_translations()`. No `languages/` directory/catalog files were found in the repository, so translated-catalog completeness was not verified.

**Dry-run disposition:** WPML + WooCommerce Multilingual and Polylang + Polylang for WooCommerce source-level dry runs are complete. This indicates the current source does not intentionally depend on either multilingual plugin and uses WordPress/WooCommerce abstractions; it does not prove runtime interoperability, translated order metadata synchronization, localized date/decimal behavior, or actual translated UI. No add-on was installed and no runtime multilingual claim is made. Phase 7 is now 6/9 planned items complete (67%): 3 execution checks plus 3 dry runs. Overall project progress is 81% using the README's equal phase weighting. Actual multilingual runtime, translation catalogs, minimum versions, legacy storage, full PHP/WP/WC runtime matrix and broader QA remain open.


## 2026-11-07 legacy order-storage source dry run

Repository search reviewed order data access for $wpdb, wp_posts, update_post_meta, and wc_get_orders. No direct $wpdb/posts-table order reads or direct update_post_meta order writes were found. Report and export paths use wc_get_orders() and order objects; cost values are accessed through WooCommerce order CRUD methods. Dry-run disposition: source-level legacy-storage API boundary review complete. No datastore setting was switched and no legacy mode runtime was exercised. This does not establish full legacy compatibility. Phase 7 is 6/9 planned items complete (67%): 3 execution checks + 3 dry runs (WPML/WCML, Polylang/WooCommerce add-on, legacy storage). Full PHP/WP/WC runtime matrix, minimum versions, live multilingual, legacy runtime, translation catalogs and regression/security/performance/beta checks remain open.


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


## 2026-11-08 source-level performance review

Reviewed Summary, CSV export, and admin asset loading. Dashboard summary and CSV use paginated WooCommerce order queries at 100 orders per page, filtered by status and date; frontend-facing admin assets are restricted to this plugin screen. This confirms bounded pagination in source, not performance at scale. No benchmark, SQL/query-count trace, or large-order staging run was performed, so the performance gate remains open. Staging still reports v0.1.0 active and does not contain the hardened artifact.


## 2026-11-08 proposed performance measurement protocol

Performance remains unverified. A staging-copy benchmark should compare dashboard summary and CSV export using 100, 1,000, and 10,000 eligible shipping orders. For each size, use a date filter covering only the fixtures, run five requests with cold and repeat runs noted separately, and record commit, WP/WooCommerce/PHP/database versions, HPOS state, elapsed server time, peak PHP memory, database query count, slow queries, and CSV row count. Validate summary aggregates and CSV rows against an independent fixture tally, retain evidence without customer personal data, and establish latency/memory budgets only after the staging baseline is measured and reviewed. Do not infer scale readiness from 100-order query pagination alone. This is a test plan, not execution evidence; no performance gate is closed.
