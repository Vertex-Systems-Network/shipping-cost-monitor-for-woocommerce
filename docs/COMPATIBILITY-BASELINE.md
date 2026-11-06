# Compatibility baseline — proposal

**Research snapshot:** 2026-11-06  
**Status:** Proposed implementation baseline. One temporary Playground smoke run installed WordPress 7.1.4, WooCommerce 11.2.2 and PHP 8.3; this is not a compatibility matrix or support claim.  
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
