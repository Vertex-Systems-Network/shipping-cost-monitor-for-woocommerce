# Owner decision register

**Status:** Recommendations prepared for review; no owner decisions recorded.  
**Updated:** 2026-11-06  
**Purpose:** Resolve the gates that block implementation without treating draft documents as approval.

Choose **Accept recommendation**, **Change**, or **Defer** for each row. A change should name the replacement decision. Deferral leaves that gate open.

## Gate A — product scope

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| A-1 | MVP feature boundary | Accept the Free/Pro proposal in PRODUCT-SPEC.md: Free manual cost ledger/basic reporting/CSV export; Pro invoice CSV mapping, reconciliation and loss reports. | Reuses the deduplicated feature bank; keeps manual + CSV useful without any carrier connection. Pricing is not approved by this choice. | Open |
| A-2 | Provider priority | No provider API in MVP until merchant usage and account-specific cost evidence are verified; prioritize generic invoice CSV. | Public documentation does not prove merchant account scopes, region availability or invoice-final access. | Open |
| A-3 | Merchant validation | Collect three anonymized invoice formats and confirm primary merchant workflow before freezing import design. | Reveals currency, decimal, identifier, surcharge, credit and split-shipment cases. | Open |

## Gate B — UX

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| B-1 | Admin navigation and flows | Accept UX-FLOWS.md as the review baseline, including dashboard, cost records, import wizard, exceptions, integrations, reports and settings. | Approving the document allows visual implementation planning only after Gate D. | Open |
| B-2 | React scope | React for the app-like workflow screens; server-side PHP remains authoritative for permissions, validation, matching and persistence. | Keeps UX choice aligned to WooCommerce extension guidance without locking an experimental settings UI. | Open |

## Gate C — architecture, privacy and compatibility

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| C-1 | Order-linked storage | WooCommerce CRUD/order metadata for selected per-order cost; plugin tables for import/evidence/audit records when cross-order query needs justify them. | Avoids direct order-table assumptions and permits indexed reconciliation queries; exact schema follows sample volume. | Open |
| C-2 | Money and comparisons | Store source amount, ISO currency and tax/basis explicitly; use decimal-safe arithmetic; never combine currencies implicitly. | Prevents false margin totals; agrees with metric contract. | Open |
| C-3 | Credential security | Do not accept credentials until a security review approves authenticated encryption, key lifecycle, rotation, revoke/disconnect and fail-closed behavior. | No plaintext fallback. This remains a release blocker for API integrations. | Open |
| C-4 | Data retention | Delete temporary uploads/raw payloads promptly after normalization; retain minimal normalized evidence/audit data; decide export/delete/uninstall behavior before coding. | Merchant record retention and privacy obligations need an explicit policy. | Open |
| C-5 | Compatibility target | Use COMPATIBILITY-BASELINE.md as a proposal; choose PHP 8.1 minimum / 8.3 recommended, WP 6.9+, WooCommerce 11.2+ as development baseline, then revise to latest stable before release. | No tested compatibility is claimed. HPOS and legacy storage plus WPML/Polylang add-ons need verification. | Open |

## Gate D — toolchain

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| D-1 | Development tooling authorization | Defer code/toolchain setup until A–C are resolved; then approve a minimal React/WooCommerce-compatible toolchain as a separate decision. | Repository rule prohibits plugin code, CLI, package manager, build system and tests before this gate. | Open |
| D-2 | CLI policy | Keep CLI absent until explicit owner approval; record any later exception here before use. | Preserves the standing project constraint that CLI is not added before the plugin is final. | Open |

## How to record a decision

Copy the ID and reply in this format:

- `A-1 Accept`
- `C-5 Change: PHP 8.3 minimum`
- `D-1 Defer`

No decision is inferred from this recommendation sheet or from the fact that planning documents exist. After decisions are supplied, update README.md, the relevant source-of-truth document and AI-BACKLOG.md before implementation begins.
