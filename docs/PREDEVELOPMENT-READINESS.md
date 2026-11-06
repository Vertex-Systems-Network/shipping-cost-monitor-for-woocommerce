# Pre-development readiness — Phase 4

**Status:** Documentation package drafted; human approval gates remain open.  
**Updated:** 2026-11-06  
**Implementation / CLI / build tooling:** Not started, as required by the project guardrail.

## Phase 4 exit checklist

| Workstream | Deliverable | Status | What remains |
|---|---|---|---|
| Data model and storage boundary | Proposed WooCommerce order CRUD + plugin-owned supporting records; evidence classes and lifecycle in [Architecture decisions](ARCHITECTURE-DECISIONS.md) | Draft complete | Gate C approval, volume assumptions, exact schema/indexes |
| API adapter and auth | Capability-declared provider adapters, scopes, regions, sync and failure rules | Draft complete | Merchant account proof, provider terms/scopes, per-provider final-cost evidence |
| Credential protection | Plaintext prohibited; OAuth preferred; encryption/key lifecycle must be reviewed and fail closed | Decision required | Security review must choose and approve an implementable key strategy |
| Import/reconciliation | Staging → validate → preview → exact match/review → idempotent commit | Draft complete | Validate against anonymized merchant invoice formats |
| Security/privacy | Endpoint, CSV, webhook, logging and data-minimization controls specified | Draft complete | Threat-model review, retention/export/delete/uninstall decisions |
| PHP/WP/WC/database | Dated baseline proposal in [COMPATIBILITY-BASELINE.md](COMPATIBILITY-BASELINE.md) | Proposed; no plugin claim | Gate C approves target; recheck release versions and pass the matrix |
| HPOS | CRUD-first design; feature detection; compatibility declaration only after tests | Proposed | Verify HPOS + legacy order storage before declaring support |
| WPML/Polylang | Optional integration targets; current add-on requirements and test lanes proposed in COMPATIBILITY-BASELINE.md | Proposed | Decide formal support target; verify combinations; no dependency claim |
| Performance | Bounded server-side pagination, indexed queries, Action Scheduler proposal | Draft complete | Confirm compatibility range; benchmark representative volume |
| React/admin UX | UX spec exists; business rules remain server-side | Review draft | Gate B approval; toolchain and implementation gates |
| Human gates | A product, B UX, C architecture, D toolchain, E release | Open | Owner reviews and explicit decisions |

## Minimum gates before implementation

1. **Gate A — Product:** approve MVP, API priority and Free/Pro boundary in PRODUCT-SPEC.md.
2. **Gate B — UX:** approve UX-FLOWS.md before React implementation.
3. **Gate C — Architecture:** decide the open items in ARCHITECTURE-DECISIONS.md, especially schema, credential security, retention and compatibility matrix.
4. **Gate D — Toolchain:** explicitly authorize tooling. CLI/build/package tooling stays absent until then.
5. Before any release, **Gate E — Release** must cover compatibility, API account evidence, privacy, support/docs and Marketplace requirements.

## Current truthful status

Phase 4 documentation work is complete as a proposed review package, including a dated public-source compatibility matrix. No provider account was connected, credentials were not collected, no code or tests were run, and no PHP/WP/WC/HPOS/WPML/Polylang compatibility has been verified. The version baseline is a recommendation, not an implemented plugin requirement. Therefore Phase 4 is not human-approved and does not authorize plugin implementation.