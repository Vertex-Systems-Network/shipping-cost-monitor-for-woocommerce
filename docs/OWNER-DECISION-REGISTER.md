# Owner decision register

**Status:** Gates A and B approved; Gate C approved for the initial no-API foundation with credential handling deferred; Gate D approved for a no-CLI/no-build React path. This status records the owner's instruction to start on 2026-11-06.  
**Updated:** 2026-11-06  
**Purpose:** Resolve the gates that block implementation without treating draft documents as approval.

The owner said “start” after reviewing the gate register. For the initial scope, the proposed defaults below are accepted where they directly match prior instructions; unresolved or evidence-dependent items stay deferred.

## Gate A — product scope

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| A-1 | MVP feature boundary | Free manual cost ledger/basic reporting/CSV export; Pro invoice CSV mapping, reconciliation and loss reports. | Scope accepted to start; pricing and market validation remain open. | Accepted for development |
| A-2 | Provider priority | No provider API in the initial build; prioritize manual entry and generic CSV after sample validation. | Accepted; credentials and provider sync are out of initial scope pending account/security evidence. | Accepted; API deferred |
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
| C-3 | Credential security | No credential collection or API connections in the initial implementation. Revisit authenticated encryption/key lifecycle before adding integrations. | No plaintext storage; API work remains excluded until security review. | Deferred; blocks integrations only |
| C-4 | Data retention | No uploads/raw payloads in the bootstrap milestone. Final retention/export/delete/uninstall policy is required before CSV import and Marketplace release. | The first shell has no customer data persistence. | Deferred to data/import milestone |
| C-5 | Compatibility target | PHP 8.1 minimum / 8.3 recommended; WP 6.9+; WooCommerce 11.2+ development baseline; revise at release. | Accepted as declared development baseline only; compatibility testing and support claims remain pending. | Accepted as proposal |

## Gate D — toolchain

| ID | Decision needed | Recommended default | Evidence / impact | State |
|---|---|---|---|---|
| D-1 | Development tooling authorization | Start a no-build implementation using WordPress-bundled React (`wp.element`) and plain JavaScript; PHP owns server behavior. No npm, package manager, bundler or build pipeline. | Accepted under the explicit start instruction; stop for a new approval if compilation/tooling becomes necessary. | Accepted; constrained path |
| D-2 | CLI policy | CLI remains absent until the plugin is final and the owner explicitly changes this instruction. | Preserves the standing no-CLI requirement. | Accepted; prohibited for now |

## Remaining decisions

- Provide anonymized merchant invoice samples before CSV parsing/matching is finalized (A-3).
- Complete credential-security and retention reviews before adding API credentials or CSV upload persistence (C-3/C-4).
- Recheck compatibility versions and run the matrix before making support claims (C-5).
- Keep CLI, package managers, bundlers and build pipelines out until the plugin is final (D-2).
