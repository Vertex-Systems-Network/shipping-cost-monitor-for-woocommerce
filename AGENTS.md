# Repository instructions for AI contributors

This file is the canonical project brief for repository-aware coding agents. Read it before making changes.

## Required reading order

1. `README.md` — current product direction, roadmap and boundaries.
2. `docs/AI-BACKLOG.md` — task IDs, gates and handoff format.
3. For the assigned task, read the relevant source of truth:
   - `docs/PRODUCT-SPEC.md` for feature IDs, option bank, packaging and platform requirements.
   - `docs/MARKET-RESEARCH.md` for competitor/provider evidence and dated findings.
   - `docs/ARCHITECTURE.md` for systems, data flow, APIs, privacy and security boundaries.

If a task is ambiguous, report the conflict or assumption. Do not invent product decisions.

## Project rules

- Product: **WPEssential Shipping Cost Monitor**, a future WPEssential WooCommerce add-on.
- Use the feature IDs in `docs/PRODUCT-SPEC.md`; check them before adding options, settings or workflows. Avoid duplicate capabilities under different names/screens.
- The repository is in **pre-development planning**. Do not add plugin implementation, CLI commands, package managers, build systems or run tests until human approval at the documented toolchain gate. Documentation-only planning is allowed.
- React is planned for the admin interface. Do not select/build a toolchain before the gate.
- Use WooCommerce CRUD APIs; HPOS compatibility must be proven before it is declared.
- Keep customer-paid shipping, estimates, label/manifest amounts, final billed charges, credits/refunds and merchant-confirmed costs distinct. Never call a quote “actual cost.”
- Carrier connections are optional adapters. Do not claim API access, supported regions or final-invoice data without provider-specific evidence.
- No runtime AI service or customer-data transmission to an LLM is currently in scope.
- Do not claim work, compatibility, tests, security review or Marketplace acceptance that has not been evidenced.
- Preserve repository slug `wpessential-shipping-cost-monitor`; display product name is title case as above.
- Keep status/progress in README aligned with actual evidence and mark target dates as proposed until approved.

## Work handoff

Report: task/feature IDs, changed files, decisions, assumptions, evidence, unverified items and proposed status updates. Do not mark a phase complete unless its exit outcome is met.
