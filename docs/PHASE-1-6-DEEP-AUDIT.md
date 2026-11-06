# Phase 1–6 deep audit

**Audit date:** 2026-11-06  
**Scope:** Documentation, canonical feature bank, source-level workflow review, previously recorded Playground/staging evidence, and one new staging report-export check.  
**Result:** Phases 1–5 satisfy only their explicitly documented desk/spec/code-delivery exit scopes. Phase 6 is not complete (83% implementation coverage). Phase 7/8 gates remain open.

## Phase status

| Phase | Audited status | What is evidenced | Remaining work / boundary |
|---|---|---|---|
| 1. Market and provider research | Complete as dated desk research | Competitor overlap, public API evidence, regional candidates and uncertainty are in MARKET-RESEARCH.md and INTEGRATION-MATRIX.md. | No claim of zero competitors, merchant demand validation, provider-account access, or final-cost schema for every carrier. Refresh sources before release. |
| 2. Requirements and options bank | Proposal baseline complete; owner/customer validation open | PRODUCT-SPEC.md maintains feature IDs, metrics, settings ownership and Free/Pro proposal. | Pricing, merchant validation, anonymized invoice formats, and Marketplace packaging mechanics are undecided. Current matching is exact WooCommerce order ID/order number; tracking/AWB/provider shipment ID are not implemented. |
| 3. UX / React specification | Written UX baseline complete; implementation/visual QA partial | UX-FLOWS.md defines admin screens and states; current admin UI uses WordPress-bundled React via wp.element and rendered on staging. | No separate polished visual prototype/review evidence. Persistent exception resolution, server-synced profiles, zone/category loss reports, alerts, and rate simulation are not implemented. |
| 4. Architecture / readiness | Initial manual/no-API boundary accepted; security and retention gates open | WooCommerce CRUD/order metadata, money/evidence distinction, bounded browser CSV flow, adapter interface and threat-control proposals are documented. | Credential storage/API calls remain gated by C-3. CSV rows are browser-local and not durably retained; merchant samples/retention policy are missing. This is not a completed security review. |
| 5. Core implementation | Code delivery complete; staging core paths verified selectively | Manual order values and reports use WooCommerce CRUD; Playground/staging report numbers were observed. On 2026-11-06, staging report CSV export produced a download. | PHP lint and compatibility matrix not run. Export contents were not independently checked. Refund allocation, currency edge cases, HPOS, multilingual and performance remain unverified. |
| 6. Reconciliation / integrations | 83% implementation coverage; not phase-complete | Staging synthetic CSV preview/commit path; duplicate-in-file, non-numeric invalid, negative review, unmatched reference, refund review, existing-cost non-overwrite and report CSV download evidenced. Source audit confirms permission/nonce gates, server-side order/shipping/currency revalidation, fingerprint check and WooCommerce CRUD persistence. | Exact same AJAX replay response not exercised. Runtime ambiguous collision unavailable because staging has no duplicate order-number fixture. Currency mismatch, mapping-profile reuse, provider runtime behavior, merchant invoice formats and broad QA need evidence. Live provider package blocked on verified merchant account/schema/security gate. |
| 7. Compatibility, quality and beta | Not started | None claimed by this audit. | HPOS/legacy, WPML, Polylang, supported PHP/WP/WC/database lanes, lower-bound period, regression/security/performance and beta feedback. |
| 8. Marketplace readiness | Not started | Strategy targets WordPress.org Free and WooCommerce.com Paid; mechanics/requirements are documented. | Packaging, vendor/account prerequisites, QIT, listing assets, policy review, support, and approvals are outstanding. |

## Phase 6 audit checklist

| Check | Status | Evidence |
|---|---|---|
| Synthetic exact-reference import happy path | Pass | Previously recorded: staging order #52 received reviewed PKR 850 cost; report reflected PKR 750 charged, PKR 1,950 cost, PKR -1,200 variance, 2/2 coverage. |
| Duplicate references inside one CSV (51, # 51) | Pass | Both normalized rows were duplicate and excluded from commit. |
| Invalid / negative amount | Pass | Non-numeric value invalid; negative value held for review. |
| Unmatched reference | Pass | Synthetic 987654321 was unmatched. |
| Refund-status row | Pass | Refunded held/excluded; No refund remained eligible. |
| Existing-cost re-import protection | Pass | Existing value displayed; overwrite not offered; no edge CSV rows committed. |
| Same exact AJAX commit replay | Pending | Fingerprint/idempotency branch reviewed in source; exact response not invoked at runtime. UI disables recommit after save. |
| Ambiguous exact order reference | Pending | Branch reviewed; staging has no duplicate order-number fixture. No runtime pass claimed. |
| Report CSV download | Pass | Staging Export CSV control delivered a download on 2026-11-06. File contents/edge values were not independently audited. |
| Live carrier final-cost sync | Blocked | No approved provider/account, account-level final-cost schema or C-3 credential-security approval. |
| HPOS, WPML/Polylang, currency mismatch and performance | Pending | No matrix/test evidence. |

## Source audit findings and disposition

- **Evidence model:** source distinguishes invoice-final/merchant-confirmed from booked-label evidence, and variance comparability requires matching currency and tax-exclusive cost. Preserve this rule; a label quote or booked amount must not be represented as final invoice cost.
- **Import boundary:** raw CSV is parsed in-browser. Match requests submit bounded mapped references; commit requests submit normalized selected row fields. Server checks include capability, nonce, shipping order, editable order, reference identity, currency and existing cost before WooCommerce CRUD updates.
- **Idempotency limitation:** a batch/row fingerprint is stored against the order; re-import UI proves existing-cost non-overwrite but not the exact already_imported response for a replayed AJAX request. Track separately.
- **Reference limitation:** exact order ID/order number matching is supported; tracking/AWB/provider shipment-ID match is not. Product-spec language must not imply those identifiers are currently supported.
- **Reporting limitation:** shipping-method breakdown exists, but zone/category loss, threshold alerts, period comparison, PDF output and rate simulation are deferred features, not defects against the accepted initial boundary.
- **Input hardening follow-up:** source accepts is_numeric() amounts, which admits scientific notation. Consider tightening to a documented decimal grammar and strict positive row/order ID validation before wider merchant release. This audit did not change behavior because runtime verification and PHP lint tools are unavailable here.
- **Composer:** composer.json has PSR-4 mapping; staging ZIP used the source fallback and omitted generated vendor files. Composer-generated autoload packaging remains pending under the no-CLI-until-final rule.

## Validation limits

- Staging is WordPress 7.1.4 + WooCommerce 11.2.2. This proves only the observed flows on that site/configuration.
- Existing test data is synthetic. No merchant invoice has been validated.
- PHP and Composer executables were unavailable in this environment; PHP lint was not run. No CLI/build toolchain was invoked.
- No automated test suite, full security review, full compatibility matrix, provider account verification or Marketplace submission was performed.

## Next gated work

1. Keep Phase 6 at 83%; complete provider package only after merchant/provider identity, account-specific final billed-cost evidence, terms and C-3 credential review.
2. Request anonymized representative invoices before defining aggregation, tracking-reference matching or durable exception resolution.
3. Exercise exact AJAX replay and ambiguous matching in a controlled staging fixture; record exact response/evidence.
4. Run PHP lint and supported-platform checks only when the approved final toolchain/environment is available; do not state those as passed before execution.
5. Start Phase 7 quality lanes, then Phase 8 submission preparation; neither is complete.