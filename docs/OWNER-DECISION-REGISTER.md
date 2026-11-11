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

## Remaining decisions

- Provide anonymized merchant invoice samples before CSV parsing/matching is finalized (A-3).
- Complete the independent credential-security review of the implemented ShipStation key storage before release, and the retention review before adding CSV upload persistence (C-3/C-4). No further provider credentials without a new decision.
- Decide Free/Pro packaging: the repository currently builds one plugin containing invoice import and the ShipStation adapter, while A-1 describes those as Pro. See the submission readiness checklist.
- Decide the first public version number (currently 0.1.0).
- Recheck compatibility versions and run the matrix before making support claims (C-5).
- Keep local/in-plugin CLI, npm and JS bundlers out of scope. Use only the explicitly authorized GitHub Actions artifact workflow (D-2/D-3).
