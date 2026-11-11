# Repository instructions for AI contributors

This file is the canonical project brief for repository-aware coding agents. Read it before making changes. The current cross-document audit and ordered handoff are in `docs/AI-AUDIT-AND-EXECUTION-PLAN.md`; use that alongside the canonical sources below.

## Required reading order

1. `README.md` — current product direction, roadmap and boundaries.
2. `docs/AI-BACKLOG.md` — task IDs, gates and handoff format.
3. For the assigned task, read the relevant source of truth:
   - `docs/PRODUCT-SPEC.md` for feature IDs, option bank, packaging and platform requirements.
   - `docs/MARKET-RESEARCH.md` for competitor/provider evidence and dated findings.
   - `docs/ARCHITECTURE.md` for systems, data flow, APIs, privacy and security boundaries.

If a task is ambiguous, report the conflict or assumption. This repository is AI-operated: under Gate F of `docs/OWNER-DECISION-REGISTER.md` (owner statement, 2026-11-11) AI contributors make product and engineering decisions themselves, record each one with its reasoning in that register before acting on it, and do not wait for human approval. Do not make an unrecorded decision, and do not treat a decision as evidence: testing and review claims still need real evidence. Only external-account actions, secrets and real merchant data require the owner.

## Project rules

- Product: **Shipping Cost Monitor for WooCommerce** by WPEssential (decision F-5). Do not put "WP" or a trademark at the start of the product name, slug or text domain.
- Use the feature IDs in `docs/PRODUCT-SPEC.md`; check them before adding options, settings or workflows. Avoid duplicate capabilities under different names/screens.
- The owner authorized the initial implementation on 2026-11-06; see `docs/OWNER-DECISION-REGISTER.md` for the accepted scope and limits. Phase 5 may proceed within that scope.
- On 2026-11-07, the owner explicitly authorized a CI-only GitHub Actions workflow that builds a downloadable installable ZIP, generates the Composer PSR-4 vendor autoloader, runs PHP lint and checks PSR-4 loading. This is a narrow exception to the earlier no-build boundary. Do not add local CLI setup, an in-plugin CLI, npm, a JS bundler or unrelated build tooling. The approved React path remains plain JavaScript using WordPress-bundled `wp.element`.
- Carrier credentials: a read-only ShipStation V2 adapter with encrypted key storage is implemented under owner decisions A-2 and C-3 (2026-11-11). Independent security review and a merchant-account runtime test are still pending, so do not describe it as reviewed or merchant-verified, and do not add another provider or credential type without a recorded owner decision. CSV import persistence waits for anonymized merchant invoice samples and the retention decision.
- Do not claim compatibility, tests, security review or Marketplace acceptance without evidence.
- Use WooCommerce CRUD APIs. HPOS compatibility is declared under decision F-1 on the evidence of gates P7-04 and P7-09; keep order access on CRUD so that stays true.
- Keep customer-paid shipping, estimates, label/manifest amounts, final billed charges, credits/refunds and merchant-confirmed costs distinct. Never call a quote “actual cost.”
- Carrier connections are optional adapters. Do not claim API access, supported regions or final-invoice data without provider-specific evidence.
- No runtime AI service or customer-data transmission to an LLM is currently in scope.
- Do not claim work, compatibility, tests, security review or Marketplace acceptance that has not been evidenced.
- The GitHub repository is `Vertex-Systems-Network/shipping-cost-monitor-for-woocommerce` (F-9). The free plugin's slug, text domain, package folder and main file are `shipping-cost-monitor-for-woocommerce` (F-5). The Pro add-on `shipping-cost-monitor-pro-for-woocommerce` (F-7) lives in its own repository, `Vertex-Systems-Network/shipping-cost-monitor-pro-for-woocommerce` (F-11). This repository must never contain Pro code or a `pro/` folder; CI rejects it. Pro works only with the free plugin installed. Pro is sold on WooCommerce.com, which handles billing and updates (F-8); do not add licensing code.
- Keep status/progress in README aligned with actual evidence and mark target dates as proposed until approved.

## Keeping Free and Pro in step (F-11)

- Pro may use only the Free contract listed in the Pro repository's `README.md` (actions `wpe_scm_init`, `wpe_scm_dashboard_enqueued`; filters `wpe_scm_dashboard_data`, `wpe_scm_carrier_adapters`; constant `WPE_SCM_FILE`; script handle `wpe-scm-admin`; JS globals `wpeScmDashboard` and `wpeScmPro`). `.github/scripts/check-contract.sh`, identical in both repositories, fails CI when they drift.
- **Every change, in either repository, must be checked against the other.** When a Free change alters or removes anything Pro uses, or Pro needs something new from Free, change both: use a branch with the **same name** in each repository and open both PRs. CI builds each branch against the same-named branch of the other repository, so the pair is tested together. Merge Free first.
- When a change affects documented behaviour, update the built-in documentation (`assets/docs/`) and the GitHub wiki in the same work.
- Keep `.github/workflows/build-installable-zip.yml` and `.github/scripts/check-contract.sh` identical in both repositories, apart from the `paths:` trigger list. CI always builds both plugins; the Pro ZIP is uploaded only from the private Pro repository (F-12), never from this public one.

## Work handoff

Report: task/feature IDs, changed files, decisions, assumptions, evidence, unverified items and proposed status updates. Do not mark a phase complete unless its exit outcome is met.


## Execution and evidence discipline

- Start each work session by confirming the exact repository and default branch, then reading `docs/AI-START-HERE.md`, `docs/AI-AUDIT-AND-EXECUTION-PLAN.md`, current README status, the task entry in `docs/AI-BACKLOG.md`, and its referenced source-of-truth document. Verify the repository's current state before relying on an older handoff.
- Select the highest-priority unblocked task with a concrete acceptance check. Do the implementation or verification in the same session where possible; do not end after a plan, repeated read-only check, or status-only README edit when useful unblocked work remains.
- Complete one whole backlog item per session when feasible. If an item is blocked, record the exact failed dependency, who/what can unblock it, and its smallest next action; do not repeat the same blocked check unless a new artifact, version, fixture, permission, or evidence has appeared. Move to the next independent P0/P1 task.
- A documentation-only change is complete only when documentation/prompt repair is the assigned task. It does not count as implementation, runtime QA, or release progress.
- Keep environment evidence separate by site, build ID, fixture and flow; never transfer order/report evidence from wpessential.org staging to TasteWP. Never deliver project test ZIPs/files in chat or retain them in the Library; if artifact testing is authorized and possible, install privately and remove temporary copies.
- Keep states separate: **implemented** (code exists), **static-verified**, **runtime-verified** (identify exact environment and flow), **blocked** (state external dependency), and **deferred** (owner-approved out of scope). Source dry-runs never count as runtime compatibility.
- Progress bars may represent only a fixed, listed checklist. Never call an unweighted count a release-readiness percentage. Do not compute an overall project percentage by averaging unlike phases. Keep the gate register as the authority for QA closure; README is a concise view that links to it.
- Update README after a meaningful completed task or state change, not after every repeated observation. Repeated smoke checks of the same flow add evidence but do not increment completion.
- Respect approved tooling boundaries: no local or in-plugin CLI, npm or JS bundler. Use the authorized CI workflow, browser/Playground, or approved staging environment. A tooling restriction is not a reason to stop unrelated unblocked work.
- Do not ask the owner to repeat authorization already recorded. Ask only for a specifically identified external dependency that cannot be supplied or created within existing authorization.

## Completion report contract

Every handoff must include: task ID and exact outcome; files/commit changed; verification performed with exact environment/result; what remains unverified; blocker owner/action if blocked; next highest-priority unblocked task. Never describe an attempted check as a pass. Close a phase only when every mandatory exit gate in its register passes or the owner explicitly narrows the supported scope.
