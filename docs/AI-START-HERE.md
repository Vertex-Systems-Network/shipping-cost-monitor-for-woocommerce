# AI start here

## What to give the AI

AI tools only know the project files they can access and actually load. Repository-aware coding tools can read the root instruction files automatically when their harness supports them. A general chat session may not automatically read a GitHub repository from its URL alone; connect the repository or provide the relevant files and use the prompt below.

## Copy/paste prompt

> Work on the GitHub repository `Vertex-Systems-Network/wpessential-shipping-cost-monitor`. First read `AGENTS.md`, `README.md`, and the task-relevant files in `docs/`. Follow the feature IDs in `docs/PRODUCT-SPEC.md` and the task gates in `docs/AI-BACKLOG.md`. This project is still in documentation-only pre-development: do not add plugin code, CLI, package manager, build pipeline, or tests until the documented human approval gate. For this task: [describe one bounded task]. Before editing, summarize the applicable requirements and files you will change. Afterward, report changes, evidence, assumptions, unresolved issues, and proposed status updates.

## Tool entry points

- **Codex / ChatGPT Codex:** `AGENTS.md`
- **Cursor:** `.cursor/rules/project.mdc` (points contributors to `AGENTS.md`)
- **Claude Code:** `CLAUDE.md` imports `AGENTS.md`
- **Gemini CLI:** `GEMINI.md` imports `AGENTS.md`
- **GitHub Copilot:** `.github/copilot-instructions.md`

These files route each supported agent to the same source of truth instead of keeping separate, drifting copies of the product plan.
