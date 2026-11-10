# AI start here

## What to give the AI

AI tools only know the project files they can access and actually load. Repository-aware coding tools can read the root instruction files automatically when their harness supports them. A general chat session may not automatically read a GitHub repository from its URL alone; connect the repository or provide the relevant files and use the prompt below.

## Copy/paste prompt

> Work on the GitHub repository `Vertex-Systems-Network/wpessential-shipping-cost-monitor`. First verify the repository and default branch, then read `AGENTS.md`, `docs/AI-START-HERE.md`, `docs/AI-AUDIT-AND-EXECUTION-PLAN.md`, the current README status, the active task row in `docs/AI-BACKLOG.md`, and all task-specific sources. Follow feature IDs in `docs/PRODUCT-SPEC.md`, architecture boundaries in `docs/ARCHITECTURE.md` and `docs/ARCHITECTURE-DECISIONS.md`, and the evidence gates in `docs/PHASE-7-GATES.md` when relevant. Initial implementation is authorized within `docs/OWNER-DECISION-REGISTER.md`. Use React through WordPress-bundled `wp.element` and plain JavaScript. The authorized GitHub Actions workflow handles CI-only Composer PSR-4 generation, PHP lint, autoload smoke and installable ZIP packaging. Do not add local/in-plugin CLI, npm or JS bundlers. API credentials and durable CSV retention remain deferred unless their recorded gates are satisfied. Choose the highest-priority unblocked task and do its implementation/verification now; do not stop at a plan, repeated staging check or status-only README edit while useful authorized work remains. If blocked, record the exact dependency, owner action and next independent task; do not retry the same blocked check without new evidence. Report only verified evidence and distinguish implemented, static-verified, runtime-verified, blocked and deferred. For this task: [one bounded task or continue highest-priority unblocked backlog item].

## Tool entry points

- **Codex / ChatGPT Codex:** `AGENTS.md`
- **Cursor:** `.cursor/rules/project.mdc` (points contributors to `AGENTS.md`)
- **Claude Code:** `CLAUDE.md` imports `AGENTS.md`
- **Gemini CLI:** `GEMINI.md` imports `AGENTS.md`
- **GitHub Copilot:** `.github/copilot-instructions.md`

These files route each supported agent to the same source of truth instead of keeping separate, drifting copies of the product plan.


## Progress and blocker rules

README updates are a record of meaningful completed work or changed evidence, not a substitute for it. Use fixed task/gate IDs and their exit evidence; source dry-runs are not runtime passes. Do not use the old Phase 7 6/9 score as a readiness percentage; see PHASE-7-GATES.md. Complete one whole unblocked backlog item per session where feasible. If a user action is the only blocker for one item, state the exact requested action once and continue independent tasks rather than repeating the same check.


## Current handoff

Use [AI audit and execution plan](AI-AUDIT-AND-EXECUTION-PLAN.md) for source-of-truth order, the environment-specific evidence snapshot, and the ordered next actions. Use [PHASE-7-GATES.md](PHASE-7-GATES.md) for gate status. Do not treat the TasteWP build/dashboard smoke as report or order-flow evidence; those synthetic report observations belong to the separate wpessential.org staging site.
