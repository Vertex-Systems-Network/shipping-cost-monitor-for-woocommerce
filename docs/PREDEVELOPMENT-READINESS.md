# Pre-development readiness — Phase 4

**Status:** Phase 4 decisions accepted for the initial manual/no-API build; evidence-dependent items remain deferred.  
**Updated:** 2026-11-10  
**Implementation:** Initial plugin/admin shell started under OWNER-DECISION-REGISTER.md. A narrow GitHub Actions artifact workflow is authorized for Composer autoload generation, PHP lint, PSR-4 smoke verification and installable ZIP packaging; local/in-plugin CLI, npm and JS bundling remain prohibited.

## Phase 4 exit checklist

| Workstream | Deliverable | Status | What remains |
|---|---|---|---|
| Data model and storage boundary | Proposed WooCommerce order CRUD + plugin-owned supporting records; evidence classes and lifecycle in [Architecture decisions](ARCHITECTURE-DECISIONS.md) | Draft complete | Gate C approval, volume assumptions, exact schema/indexes |
| API adapter and auth | Capability-declared provider adapters, scopes, regions, sync and failure rules | Draft complete | Merchant account proof, provider terms/scopes, per-provider final-cost evidence |
| Credential protection | Plaintext prohibited; OAuth preferred; encryption/key lifecycle must be reviewed and fail closed | Decision required | Security review must choose and approve an implementable key strategy |
| Import/reconciliation | Staging → validate → preview → exact match/review → idempotent commit | Draft complete | Validate against anonymized merchant invoice formats |
| Security/privacy | Endpoint, CSV, webhook, logging and data-minimization controls specified | Draft complete | Threat-model review, retention/export/delete/uninstall decisions |
| PHP/WP/WC/database | Dated baseline proposal in [COMPATIBILITY-BASELINE.md](COMPATIBILITY-BASELINE.md) | Proposed; no plugin claim | Development baseline accepted as proposal; recheck release versions and pass the matrix |
| HPOS | CRUD-first design; feature detection; compatibility declaration only after tests | Proposed | Verify HPOS + legacy order storage before declaring support |
| WPML/Polylang | Optional integration targets; current add-on requirements and test lanes proposed in COMPATIBILITY-BASELINE.md | Proposed | Decide formal support target; verify combinations; no dependency claim |
| Performance | Bounded server-side pagination, indexed queries, Action Scheduler proposal | Draft complete | Confirm compatibility range; benchmark representative volume |
| React/admin UX | UX spec accepted; business rules remain server-side | Baseline approved | Visual/functional review; implementation uses no-build React |
| Human gates | A/B accepted for MVP and UX; C/D accepted with no-API/no-build constraints; E release remains | In progress | Merchant examples, security/retention decisions, version verification and release evidence |

## Remaining gates and implementation limits

- CSV import/upload persistence waits for anonymized merchant invoices and a defined retention policy.
- Carrier credentials and sync wait for credential encryption/key-management review and account-specific API evidence.
- Compatibility statements wait for the documented matrix to pass, including HPOS and both supported order-storage modes.
- The approved CI-only artifact workflow is the sole build exception. Do not introduce local CLI setup, in-plugin CLI, npm or JS bundlers.
- Before release, **Gate E — Release** must cover compatibility, API account evidence, privacy, support/docs and Marketplace requirements.

## Current truthful status

Phase 4 decisions authorize the initial manual/no-API foundation. The Phase 5 bootstrap/admin shell is now implemented, but no provider account was connected, credentials were not collected, no tests were run, and no PHP/WP/WC/HPOS/WPML/Polylang compatibility has been verified. The version baseline remains a recommendation, not a tested support claim.