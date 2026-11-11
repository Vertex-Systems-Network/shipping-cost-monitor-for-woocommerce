# Owner decision register

**Status:** Gates A and B approved; A-2 and C-3 are superseded by the 2026-11-11 read-only ShipStation implementation scope recorded below; Gate D approves no-build React plus the owner-authorized CI-only artifact workflow.  
**Updated:** 2026-11-11  
**Purpose:** Resolve the gates that block implementation without treating draft documents as approval.

The owner said “start” after reviewing the gate register. For the initial scope, the proposed defaults below are accepted where they directly match prior instructions; unresolved or evidence-dependent items stay deferred.

## Gate A — product scope

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| A-1 | MVP feature boundary | Free manual cost ledger/basic reporting/CSV export; Pro invoice CSV mapping, reconciliation and loss reports. | Scope accepted to start; pricing and market validation remain open. | Accepted for development |
| A-2 | Provider priority | Add a read-only ShipStation V2 label-cost adapter as booked-label evidence; keep invoice CSV as final-cost source unless provider account evidence proves otherwise. | 2026-11-11 execution directive supersedes the original no-API MVP boundary. Adapter implementation does not claim account-level runtime verification or final invoice data. | Implemented; account validation open |
| A-3 | Merchant validation | Gather anonymized invoice examples before finalizing CSV parsing/matching. | Needed before import implementation; does not block the admin/bootstrap foundation. | Deferred to import phase |

## Gate B — UX

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| B-1 | Admin navigation and flows | UX-FLOWS.md is the approved navigation/workflow baseline. | Accepted for development. | Accepted |
| B-2 | React scope | React via WordPress-bundled `wp.element`; PHP remains authoritative for permissions, validation, matching and persistence. | Accepted; plain JavaScript source avoids a build pipeline. | Accepted |

## Gate C — architecture, privacy and compatibility

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| C-1 | Order-linked storage | WooCommerce CRUD/order metadata for selected per-order cost; defer plugin tables until CSV/evidence volume justifies them. | Accepted as the initial implementation boundary; exact metadata contract still follows the feature spec. | Accepted for initial build |
| C-2 | Money and comparisons | Preserve source amount, ISO currency and tax/basis; decimal-safe arithmetic; no implicit cross-currency totals. | Accepted from the metric contract. | Accepted |
| C-3 | Credential security | ShipStation key is accepted only after a read-only auth probe, encrypted with AES-256-GCM, and stored as ciphertext in a non-autoload option. Derive the encryption key from unique AUTH_KEY + SECURE_AUTH_KEY; fail closed on missing/default salts; salt rotation requires reconnect. Disconnect deletes the stored envelope. | Implemented defensively in code; independent security review, merchant-account runtime test, and key rotation/recovery runbook review remain release gates. No plaintext fallback. | Implementation complete; review pending |
| C-4 | Data retention | No uploads/raw payloads in the bootstrap milestone. Final retention/export/delete/uninstall policy is required before CSV import and Marketplace release. | The first shell has no customer data persistence. | Deferred to data/import milestone |
| C-5 | Compatibility target | PHP 8.1 minimum / 8.3 recommended; WP 7.0+; WooCommerce 11.2+ development baseline; revise at release. | Updated after WooCommerce 11.2.2 reported WP 7.0 minimum and an official WooCommerce readme declared 7.0; support claims and matrix testing remain pending. | Accepted as proposal |

## Gate D — toolchain

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| D-1 | React development path | Use WordPress-bundled React (`wp.element`) and plain JavaScript; PHP owns server behavior. No npm or JS bundler. | Preserves the approved no-build UI path. | Accepted |
| D-2 | Local/in-plugin CLI | Do not add a local CLI prerequisite, CLI distributed inside the plugin, npm or JS build chain. | Product development should remain usable from the repository source; package compilation is isolated to GitHub Actions. | Accepted; remains prohibited |
| D-3 | Installable ZIP artifact workflow | Add a manually downloadable GitHub Actions workflow that runs Composer PSR-4 generation, PHP lint, autoloader smoke and ZIP packaging. | Explicit owner request on 2026-11-07: “artifact build system add krro take build kr k me download krron phir testing pr upload kr don”. CI-generated artifacts support staging installs; they do not constitute a Marketplace release. | Authorized; initial artifact build passed 2026-11-07 (run #42373229279). Phase 7 follow-up added PHP 8.1–8.4 CI lint/autoload matrix, which passed in run #42374396949; this remains CI-only, with no local or in-plugin CLI. |

## Gate F — delegated decisions (AI-operated repository)

On 2026-11-11 the owner stated that no human works in this repository and that every decision is to be made by the AI contributors. From that date, AI contributors decide product and engineering questions, record each decision here with its reasoning, and do not wait for owner approval. Three kinds of item still need the owner in person, because an AI contributor cannot supply them: actions inside external accounts (WordPress.org, WooCommerce.com, ShipStation), secrets and API keys, and real merchants or their data.

| ID | Decision | Reasoning | State |
|---|---|---|---|
| F-1 | Declare HPOS (`custom_order_tables`) compatibility. | Gates P7-04 and P7-09 passed manual save, invoice commit, refund-adjusted report and export on HPOS and legacy storage; all order access is WooCommerce CRUD. WooCommerce.com requires the declaration. | Decided 2026-11-11 |
| F-2 | Ship one free plugin containing every current feature. No Pro split in this release. | The code is one plugin with no licensing layer; WordPress.org requires everything in a hosted plugin to be free and functional. A paid add-on can be designed later from features not yet built (alerts, zone/category analysis, simulation). This narrows A-1 for the first release; the WooCommerce.com paid track is deferred until a Pro add-on exists. | Superseded by F-7 (2026-11-11) |
| F-3 | Keep version 0.1.0 until Phase 7 closes; the first public release is 1.0.0. | The independent security review (P7-12), the full benchmark (P7-13) and beta (P7-14) are open; a 1.0.0 label before they close would overstate readiness. | Superseded by F-6 (2026-11-11) |
| F-5 | Distribute as **Shipping Cost Monitor for WooCommerce**, slug and text domain `shipping-cost-monitor-for-woocommerce`, main file `shipping-cost-monitor-for-woocommerce.php`, author WPEssential. The GitHub repository name and the `wpe_scm_` code prefix are unchanged. | Plugin Check 2.2.0 flagged the old name and slug with `trademarked_term` because they contain "wp"; WordPress.org does not accept new plugins whose name or slug uses "WP", and trademarks such as WooCommerce may only appear after "for". The slug returned "Plugin not found" from the WordPress.org plugins API on 2026-11-11, so it appeared free; it is reserved only when the submission is accepted. | Decided 2026-11-11 |
| F-6 | Both plugins start at version **1.0.0**. | Owner instruction on 2026-11-11 ("starting version 1.0"). Supersedes F-3. Open Phase 7 gates (P7-12 independent review, P7-13 full benchmark, P7-14 beta) are not closed by the version number and stay listed in the README status. | Decided 2026-11-11 |
| F-7 | Ship two plugins built from this repository: **Shipping Cost Monitor for WooCommerce** (free, WordPress.org) with the order cost record, dashboard, method breakdown and CSV export; and **Shipping Cost Monitor Pro for WooCommerce** (paid add-on, `pro/`), which requires the free plugin and adds invoice CSV import, exact matching and the ShipStation adapter. CI builds both ZIPs. | Owner instruction on 2026-11-11 to generate Free and Pro artifacts. Supersedes F-2. This is the A-1 boundary. The free package must contain no Pro code (WordPress.org guideline on locked or trial code), which CI enforces. Pro uses its own namespace `WPEssential\ShippingCostMonitorPro`, text domain `shipping-cost-monitor-pro-for-woocommerce` and uninstall routine; it hooks into the free plugin through `wpe_scm_init`, `wpe_scm_dashboard_data`, `wpe_scm_dashboard_enqueued` and `wpe_scm_carrier_adapters`. Pro licensing and update delivery are not built yet. | Decided 2026-11-11 |
| F-8 | Sell the Pro add-on on the **WooCommerce.com Marketplace** and use WooCommerce.com's billing, subscriptions and update delivery. Pro contains **no licensing or update code of its own**. Its `Update URI` points to its WooCommerce.com product page so WordPress never offers a WordPress.org update for it. WooCommerce.com's deployment adds the `Woo: <product_id>:<hash>` header to the distributed package; it is not added to the source by hand. Each release checks that the deployed package carries it. | Owner instruction on 2026-11-11. WooCommerce.com links a purchased subscription to a store and delivers updates through the WooCommerce.com Update Manager, which withholds the update package when the subscription has expired, so a custom license check would duplicate it. Vendor application, product listing, pricing and QIT runs are WooCommerce.com account actions for the owner. | Decided 2026-11-11 |
| F-9 | The GitHub repository is renamed to `Vertex-Systems-Network/shipping-cost-monitor-for-woocommerce`, matching the free plugin slug. | Owner renamed it on 2026-11-11. GitHub redirects the old URL, so dated evidence links stay valid; current instructions and in-plugin links use the new name. | Recorded 2026-11-11 |
| F-10 | The user guide lives **inside the free plugin**, not in the repository: a Documentation tab on the Shipping Cost Monitor page (`admin.php?page=shipping-cost-monitor-for-woocommerce&tab=docs`) renders bundled HTML chapters from `assets/docs/` with screenshots in `assets/docs/images/`. `docs/user-guide/` is removed. The Plugins-screen Documentation links (Free and Pro) and the dashboard button open this tab. | Owner instruction on 2026-11-12. Merchants read the guide where they use the plugin, it works offline and matches the installed version, and no external link can break. Chapters are static HTML chosen from a fixed list and passed through `wp_kses_post`; the page needs `manage_woocommerce`. Documentation text is excluded from the CI "no Pro code in Free" grep because the guide describes Pro features. | Decided 2026-11-12 |
| F-11 | The Pro add-on moves to its own repository, `Vertex-Systems-Network/shipping-cost-monitor-pro-for-woocommerce`; `pro/` is removed from this repository (supersedes the single-repository part of F-7). Free never contains Pro code; Pro works only with Free installed (`Requires Plugins`). Both repositories run the same workflow: it checks out both (same-named branch in the other repository if it exists, else `main`), runs a Free/Pro contract check (`.github/scripts/check-contract.sh`), lints both and uploads **both** installable ZIPs. A change in either repository that affects the contract or documented behaviour is made in both, on same-named branches, Free merged first. User documentation is also published on this repository's GitHub wiki. | Owner instruction on 2026-11-12 (Pro repository created by the owner; Free must not contain Pro; changes in one must be checked and mirrored in the other; building either must build both; wiki pages for the documentation). Same-named branches let CI test a matched pair before either merges, without a cross-repository token. | Decided 2026-11-12 |
| F-4 | Uninstall deletes the stored carrier credential and keeps order cost metadata. | Cost records are merchant accounting data attached to orders; deleting them on uninstall would be silent data loss. The readme states this. | Decided 2026-11-11 |

## Remaining decisions

- Provide anonymized merchant invoice samples before CSV parsing/matching is finalized (A-3).
- Complete the independent credential-security review of the implemented ShipStation key storage before release, and the retention review before adding CSV upload persistence (C-3/C-4). No further provider credentials without a new decision.
- Free/Pro packaging and version are decided in F-7 and F-6.
- Pro distribution is decided in F-8 (WooCommerce.com). Owner actions: WooCommerce.com vendor application, product listing and price, then confirm the deployed package carries the `Woo:` header that WooCommerce.com adds.
- Recheck compatibility versions and run the matrix before making support claims (C-5).
- Keep local/in-plugin CLI, npm and JS bundlers out of scope. Use only the explicitly authorized GitHub Actions artifact workflow (D-2/D-3).
