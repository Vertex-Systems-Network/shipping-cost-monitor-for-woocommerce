# AI-assisted development backlog and working protocol

This backlog is for ChatGPT, Cursor, Claude, Gemini, and human contributors. It is a shared project plan, not authorization for an agent to invent scope or skip review.

## Rules for every contributor

1. Read README.md and the relevant documents in docs/ before proposing or changing code.
2. Treat `docs/PRODUCT-SPEC.md` as the single feature bank. Search by feature ID before adding an option; extend an existing ID or explain why a new capability is distinct. Never create duplicate settings under different screens.
3. Treat `docs/ARCHITECTURE.md` as the current system boundary. Raise an architecture decision when implementation conflicts with it; do not silently fork the design.
4. Do not add or run a CLI, package manager, build pipeline or plugin code before the pre-development exit review explicitly changes this constraint.
5. Do not claim an item is complete because a draft or code snippet exists. Update status only with evidence and a human-readable summary.
6. Do not invent compatibility claims, Marketplace approval, competitor exclusivity or test results.
7. Use focused changes; explain files changed, decisions, risks, and unverified assumptions in each handoff.

## Backlog

| ID | Work item | Status | Exit evidence |
|---|---|---|---|
| AI-01 | Recheck competitor feature matrix from primary listings/docs | In progress | Sources, checked date, overlaps and product gaps recorded |
| AI-02 | Validate merchant workflow and CSV invoice examples (with permission-safe sample data) | Planned | At least three representative anonymized schemas and edge cases |
| AI-03 | Approve Free/Pro boundary and MVP acceptance criteria | Planned | Product owner sign-off on SCM feature IDs |
| AI-04 | Define terms: charged shipping, actual cost, variance, coverage, refunds and currencies | Planned | Metric glossary with worked examples |
| AI-05 | Decide canonical storage for snapshots, components, imported rows and audit history | Planned | Schema decision record with query/performance rationale |
| AI-06 | Map React admin screens, navigation, empty/loading/error/success states | Planned | Screen flow and reviewed wireframes/prototype |
| AI-07 | Platform compatibility baseline drafted | Drafted; release versions need verification | PHP/WP/WC/database baseline documented; HPOS, WPML and Polylang release matrix verified and recorded |
| AI-08 | Define security/privacy review and import threat model | Planned | Capability matrix, CSV rules, retention and external-service disclosure |
| AI-09 | Define reconciliation matching rules and human review controls | Planned | Examples for duplicates, splits, returns, credits and ambiguity |
| AI-10 | Define report query, pagination, caching and background-job strategy | Planned | Performance budget and volume test scenarios |
| AI-11 | Write release and WooCommerce Marketplace readiness checklist | Planned | Metadata, documentation, license, support and compatibility evidence |
| AI-12 | Pre-development exit review | Planned | Product, UX, architecture, requirements and toolchain decision approved |

## Agent task template

When assigning work, provide:

- **Task ID(s):** exact backlog or SCM feature IDs.
- **Goal:** one bounded outcome.
- **Allowed files/scope:** explicit paths or document/code area.
- **Constraints:** compatibility, security, UI, performance, privacy and no-CLI rule.
- **Evidence required:** sources, screenshots, decisions or implementation evidence.
- **Completion report:** changed files, feature IDs covered, decisions, assumptions, unresolved items, and status proposal.

## Human approval gates

- **Gate A — Product:** MVP and Free/Pro boundary approved.
- **Gate B — UX:** React admin information architecture and flows approved.
- **Gate C — Architecture:** data model, imports, security and compatibility decisions approved.
- **Gate D — Toolchain:** only after Gates A–C, decide build tooling; no CLI/tooling is assumed before this gate.
- **Gate E — Release:** compatibility evidence, documentation, privacy and Marketplace checks reviewed.
