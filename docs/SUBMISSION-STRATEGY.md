# Distribution and marketplace submission strategy

> **Current scope decision (2026-11-10):** WPML/WCML and Polylang integration and runtime compatibility are removed from the release requirements. Historical references below are superseded; no support claim is made. WordPress core translation APIs used by the plugin remain.

**Status:** Target channels recorded; vendor/listing mechanics and approval remain unverified.  
**Updated:** 2026-11-06

## Target distribution

| Edition | Primary destination | Target model | Current state |
|---|---|---|---|
| Free | WordPress.org Plugin Directory | Free, useful standalone edition | Planned; needs completed product, directory readme/assets and review |
| Paid / Pro | WooCommerce.com Marketplace | Paid WooCommerce extension | Planned; requires approved vendor account, separate product submission, business/code/UX review |
| Free edition on WooCommerce.com | Confirm with Marketplace partner team | WooCommerce.com's freemium workflow may require separate free and paid submissions that are later merged | Open question; do not assume WordPress.org + WooCommerce.com alone will produce the merged Marketplace page |

The user's target remains **Free on WordPress.org and Paid/Pro on WooCommerce.com**. Marketplace documentation says freemium products require two separate submissions on its review flow, one for each model, later merged into a product page. Before submission, confirm whether the intended WordPress.org Free package can satisfy that Marketplace Free submission requirement, or whether a separate Woo.com Free submission/listing is needed.

## Package and product boundaries

- WordPress.org Free must be a stable, complete, useful plugin in its own right. It must not be a trial, a crippled shell, or functionality disabled solely to force payment.
- Pro must be a distinct paid package submitted through WooCommerce.com's Marketplace flow. Decide whether it is a standalone Pro edition or an add-on requiring Free only after confirming Marketplace expectations and the upgrade/data-migration experience.
- Do not build a download/install mechanism that fetches or executes a paid plugin from an external server inside the WordPress.org Free plugin. Use the approved Marketplace distribution and update flow.
- Use one documented source of truth for common behavior, but produce separate, reviewable Free and Pro release packages. Keep licensing and every bundled third-party asset/library compatible with the destination.
- Keep plugin identity, slugs, versioning, Free/Pro feature IDs, database keys and migration behavior consistent across channels. Final public slugs are not reserved until accepted by each directory/Marketplace.
- Place any upgrade link in a contextual, restrained location and only after the WooCommerce.com product listing exists. Do not add dashboard-wide nags, affiliate tracking or unsolicited outbound telemetry.

## WordPress.org Free submission track

### Before submission

1. Finish SCM-ORD-01/02/SCM-REP-01 and the accepted Free feature boundary; verify Free works without a Pro license, account, carrier connection or external service.
2. Complete security and privacy review. Request no remote connection or telemetry without clear, authorized consent; document any provider data transmission.
3. Confirm GPLv2-or-later compatibility for source, libraries, images and other assets; provide human-readable source and attribution.
4. Prepare the WordPress.org main plugin file metadata, directory-safe slug, readme.txt, screenshots, icon and banner assets. Repository README.md is not a substitute for the public directory's readme.txt.
5. Create a complete installable release ZIP within the directory's size limit, containing production files only. Include no placeholder UI, development-only content or unnecessary build/vendor files.
6. Verify the plugin has a meaningful function, correct dependency behavior, supported-version declarations, accessible admin UI, and no unexpected external calls.
7. Submit with the official WPEssential organization account/email. Receive and address reviewer feedback. After approval, publish maintained stable source/releases using the provided WordPress.org SVN repository and update tags/readme metadata as required.

### Listing / maintenance deliverables

- Clear human-oriented description, installation/configuration instructions, screenshots and support contact.
- Stable tag and tested WordPress/PHP requirements matching actually verified versions.
- Release and security response procedure; current compatibility updates.
- Free/Pro boundary and any Pro link described without trialware, hidden locks or intrusive admin promotion.
- Privacy/data handling, retention, delete/export and third-party service disclosures where relevant.

## WooCommerce.com Paid / Pro submission track

### Before vendor submission

1. Apply for and receive approval for a WooCommerce Marketplace vendor account.
2. Finalize product rationale, competitor differentiation, company history, support plan, price and revenue-share business model.
3. Confirm product-name/trademark compliance and price consistency across every sales environment.
4. Prepare a production ZIP, business details, testing instructions, demo/test account instructions as appropriate, product page copy, screenshots and documentation.
5. Complete full security, critical-flow, accessibility, UX, data lifecycle, upgrade and compatibility review.
6. Confirm support for the two latest major releases of WooCommerce and WordPress at submission time, HPOS compatibility, and publish only the verified platform combinations.
7. Run and pass the current required WooCommerce Quality Insights Toolkit checks: API, E2E, Activation, Security, PHPCompatibility, Malware and Validation.
8. Submit through the approved Marketplace vendor dashboard. Resolve test/reviewer feedback and upload corrected packages through the Marketplace process. Approval and launch are separate from submission.

### Paid product constraints

- If a monthly tiered SaaS billing model is selected, account for the Marketplace SaaS Billing API requirement before launch. Current product proposal is a paid extension/license model, not a SaaS product; confirm the exact billing/entitlement design with the Marketplace before implementation.
- Use the Marketplace-approved payment, licensing, update and support paths. Do not route merchants to external checkout/affiliate destinations from the Woo Marketplace listing or plugin UI.
- If Marketplace freemium pairing is required, prepare a separate Free submission as instructed and request the merged listing only through the Marketplace workflow.

## Shared release gates and sequencing

| Gate | Free WordPress.org | Paid WooCommerce.com |
|---|---|---|
| Functional completeness | Useful, stable Free edition; no placeholders/trial lock | Complete paid product with tested critical workflows |
| Licensing | GPL-compatible source/assets/libraries | Marketplace license/business terms and compatible bundled assets |
| Security/privacy | Directory guideline review, consent for external traffic, data disclosure | Security review, QIT security/malware/API/validation |
| Compatibility | Claim only tested WP/PHP/Woo versions | Latest two major WP/Woo lines, HPOS required, advertised integrations verified |
| Documentation | readme.txt, install/use/support/privacy details | Product page, user docs, testing instructions, support expectations |
| Distribution | Official WordPress.org submission/review/SVN path | Vendor approval, Marketplace submission/review/update flow |
| Cross-edition | No silent data loss; clarify whether Pro is an add-on or standalone | Confirm Free pairing/merge mechanics and safe upgrade path |

**Submission order proposal:** reach a release candidate, complete shared quality/security evidence, confirm vendor/account and freemium mechanics, then submit both channel tracks with aligned names, versions and feature matrix. Do not claim acceptance or schedule an exact launch date before each reviewer approves.

## Submission readiness checklist

**Updated:** 2026-11-11. Nothing has been submitted to either marketplace. "Done in source" means the file or code exists and passed static checks; it does not mean a reviewer has accepted it.

### Shared

| Item | State | Owner / next action |
|---|---|---|
| GPL-compatible license file and headers | Done in source (`LICENSE`, GPL-2.0-or-later) | — |
| Plugin headers: name, URIs, version, requires, text domain | Done in source | Owner confirms `https://wpessential.org` as Plugin/Author URI |
| HPOS compatibility declared | Done in source; runtime confirmation pending | AI-45 |
| Uninstall cleanup | Done in source (credential option removed; order cost metadata kept by design) | Decided F-4 |
| External service disclosure (ShipStation) | Done in `readme.txt` | Recheck ShipStation terms/privacy URLs before submitting |
| Translation template (`languages/*.pot`) | Not done | WordPress.org generates language packs from source; a `.pot` is needed for WooCommerce.com distribution |
| Public version number | Decided F-3: 0.1.0 until Phase 7 closes, then 1.0.0 | — |
| Free/Pro packaging | Decided F-2: one free plugin; WooCommerce.com paid track deferred until a Pro add-on exists | The WooCommerce.com table below applies only when that add-on is built |
| Phase 7 gates | Open (see gate register) | P7-12, P7-13, P7-14 |

### WordPress.org (account `wpessential`)

| Item | State | Owner / next action |
|---|---|---|
| `readme.txt` in directory format | Done in source; meta follows the existing `wpessential` plugin (contributor `wpessential`, 5 tags, same field set). This plugin stays GPLv2-or-later, while that plugin is GPLv3-or-later | Run the official readme validator before upload |
| Stable tag equals plugin version | Enforced in CI | — |
| "Tested up to" | 7.1, matching recorded WP 7.1.4 runs | Update at release |
| Plugin Check (PCP) | Run 2026-11-11 on `main` at `93c63a4` in Playground: 0 errors; 3 trademark warnings fixed by F-5; 5 warnings are repository-only files absent from the CI ZIP | 2026-11-11 re-run on renamed `main` (`2aee2e461f8d`): 0 errors, 5 repository-only warnings. Run once on the CI ZIP before upload | Run on the CI ZIP on a test site; fix errors |
| Plugin name and slug | Done in source (F-5): "Shipping Cost Monitor for WooCommerce" / `shipping-cost-monitor-for-woocommerce`; WordPress.org API returned "Plugin not found" on 2026-11-11 | Reserved only on acceptance; only the account owner can submit |
| Screenshots, banner and icon assets | Not done | Owner/design; captions already listed in `readme.txt` |
| Submission upload and review replies | Not done | Account owner, from the `wpessential` WordPress.org account with 2FA |
| SVN release | Not done | After approval |

### WooCommerce.com Marketplace

| Item | State | Owner / next action |
|---|---|---|
| Vendor account and product application | Not done | Owner |
| `Woo:` plugin header (product ID and hash) | Not possible yet | Assigned by WooCommerce.com after the product is accepted |
| `changelog.txt` in Marketplace format | Done in source | — |
| `WC requires at least` / `WC tested up to` headers | Done in source (11.2 / 11.2) | Update at release |
| QIT test runs (activation, security, PHPStan, PHPCompatibility, Woo API/E2E) | Not run | Requires vendor access to QIT |
| Latest two major WordPress and WooCommerce lines | Partly evidenced (see P7-08) | Extend the matrix before submission |
| Product page copy, docs, support and refund terms, pricing | Not done | Owner |

## Official references checked 2026-11-06

- [WordPress.org Plugin Directory overview](https://developer.wordpress.org/plugins/wordpress-org/)
- [WordPress.org detailed plugin guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [WordPress.org submission and maintenance flow](https://developer.wordpress.org/plugins/wordpress-org/planning-submitting-and-maintaining-plugins/)
- [WordPress.org plugin readme standard](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/)
- [WooCommerce Marketplace product submission requirements](https://developer.woocommerce.com/docs/woo-marketplace/submitting-your-product)
- [WooCommerce Marketplace vendor onboarding](https://developer.woocommerce.com/docs/woo-marketplace/getting-started)

This is a dated planning document. Recheck official requirements, version targets, payment terms and submission flow immediately before release.
