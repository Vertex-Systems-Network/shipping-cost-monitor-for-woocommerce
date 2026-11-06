# AI-assisted development backlog and working protocol

This backlog is for ChatGPT, Cursor, Claude, Gemini, and human contributors. It is a shared project plan, not authorization for an agent to invent scope or skip review.

## Rules for every contributor

1. Read README.md and the task-relevant documents in docs/ before proposing or changing code.
2. Treat `docs/PRODUCT-SPEC.md` as the single feature bank; check feature IDs before adding options.
3. Treat `docs/ARCHITECTURE.md` as the current system boundary; raise decisions when implementation conflicts.
4. Do not add plugin code, CLI, package manager, build pipeline or run tests before the human toolchain gate.
5. Mark work complete only with evidence. Distinguish public desk research from account-level API verification and merchant validation.
6. Do not claim compatibility, Marketplace acceptance, provider access, final invoice data or test results without evidence.
7. Report changed files, decisions, assumptions, evidence and unresolved items.

## Backlog

| ID | Work item | Status | Exit evidence |
|---|---|---|---|
| AI-01 | Competitor feature matrix from primary listings/docs | Complete (desk review) | Overlaps and plausible differentiation recorded in MARKET-RESEARCH.md; no exclusivity claim |
| AI-02 | Validate merchant workflow and invoice CSV examples | Needs merchant inputs | Three anonymized invoice schemas and edge cases; request files only with sensitive data removed |
| AI-03 | Approve Free/Pro boundary and MVP acceptance criteria | Decision pack ready; owner approval pending | Decision-ready MVP, acceptance criteria and settings map in PRODUCT-SPEC.md |
| AI-04 | Define metrics: charged, actual/confirmed cost, variance, coverage, refunds, currency | Complete (proposed metric contract) | Metric glossary, comparable-cost rules and examples/guardrails in PRODUCT-SPEC.md |
| AI-05 | Decide storage for cost snapshots, charge rows and audit history | Planned | Schema decision record with volume/query/retention rationale |
| AI-06 | Map React admin screens and all states | Planned | Screen flows/wireframes for dashboard, imports, exceptions, connectors and settings |
| AI-07 | Platform compatibility baseline | Drafted; release versions need verification | PHP/WP/WC/database baseline drafted; verify current and previous supported versions during release QA |
| AI-08 | Security/privacy and import/API threat model | Planned | Capabilities, credential strategy, provider scopes, CSV protection, retention and disclosures |
| AI-09 | Define reconciliation matching and review controls | Complete (proposed MVP rules) | Exact-ID precedence, exception states, ambiguity/manual review and audit behavior in PRODUCT-SPEC.md |
| AI-10 | Define reporting/performance/background jobs | Planned | Query plan, pagination/caching and data-volume scenarios |
| AI-11 | Marketplace readiness checklist | Planned | Metadata, license, docs, support and compatibility evidence |
| AI-12 | Pre-development exit review | Planned | Product, UX, architecture, compatibility and toolchain gates reviewed |
| AI-13 | Carrier/platform public API capability matrix | Complete (public-doc desk review) | Region-aware evidence matrix in INTEGRATION-MATRIX.md; actual invoice endpoints/account permissions remain unverified |
| AI-14 | Verify candidate APIs with merchant accounts and account-specific docs | Not started | Top providers selected from beta usage; scopes, invoice-final fields, terms, geography and sync limits confirmed |

## Agent task template

- **Task ID(s):** exact backlog/feature IDs.
- **Goal:** one bounded outcome.
- **Allowed files/scope:** explicit files.
- **Constraints:** compatibility, security, UI, performance, privacy and no-CLI rule.
- **Evidence required:** sources or decision record.
- **Completion report:** files changed, decisions, assumptions, unresolved items and status proposal.

## Human approval gates

- **Gate A — Product:** review and approve MVP, provider priority and Free/Pro packaging. No customer usage or owner approval is claimed until supplied.
- **Gate B — UX:** approve React admin flows.
- **Gate C — Architecture:** approve schema, API adapter/auth, imports, security and compatibility decisions.
- **Gate D — Toolchain:** only after Gates A–C; decide build tooling. CLI remains prohibited until explicitly approved.
- **Gate E — Release:** verify compatibility, provider API terms/access, privacy, docs and Marketplace requirements.

## Current handoff

Phase 2 requirements/options work is ready for owner review. Implementation remains prohibited until the relevant human gates and the separate toolchain gate authorize it.
