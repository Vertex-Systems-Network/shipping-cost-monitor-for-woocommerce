# AI-assisted development backlog and working protocol

This backlog is for ChatGPT, Cursor, Claude, Gemini, and human contributors. It is a shared project plan, not authorization for an agent to invent scope or skip review.

## Rules for every contributor

1. Read README.md and the task-relevant documents in docs/ before proposing or changing code.
2. Treat `docs/PRODUCT-SPEC.md` as the single feature bank; check feature IDs before adding options.
3. Treat `docs/ARCHITECTURE.md` and `docs/ARCHITECTURE-DECISIONS.md` as the system boundary and proposed decisions; raise conflicts instead of silently overriding them.
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
| AI-05 | Decide storage for cost snapshots, charge rows and audit history | Complete (proposed ADR; Gate C pending) | Hybrid WooCommerce CRUD/order metadata and plugin-owned evidence/batch records documented in ARCHITECTURE-DECISIONS.md; exact schema needs volume review |
| AI-06 | Map React admin screens, navigation and all screen states | Complete (UX specification draft) | UX-FLOWS.md defines hierarchy, primary flows, shared filters, actions, empty/loading/error/partial/success states; owner Gate B review pending |
| AI-07 | Platform compatibility baseline | Drafted; release versions need verification | PHP/WP/WC/database baseline drafted; decide supported matrix and verify during release QA |
| AI-08 | Security/privacy and import/API threat model | Complete (proposed controls; review pending) | Credential, endpoint, CSV, webhook, logging and data-minimization controls plus open security decisions in ARCHITECTURE-DECISIONS.md |
| AI-09 | Define reconciliation matching and review controls | Complete (proposed MVP rules) | Exact-ID precedence, exception states, ambiguity/manual review and audit behavior in PRODUCT-SPEC.md |
| AI-10 | Define reporting/performance/background jobs | Complete (architecture proposal; benchmark pending) | Bounded pagination, server aggregates, indexed query direction and Action Scheduler proposal in ARCHITECTURE-DECISIONS.md |
| AI-11 | Marketplace readiness checklist | Planned | Metadata, license, docs, support and compatibility evidence |
| AI-12 | Pre-development exit review | Draft checklist; gates open | PREDEVELOPMENT-READINESS.md maps deliverables and remaining A–E approvals |
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
- **Gate B — UX:** review and approve UX flows before visual implementation.
- **Gate C — Architecture:** approve schema, API adapter/auth, imports, security and compatibility decisions.
- **Gate D — Toolchain:** only after Gates A–C; decide build tooling. CLI remains prohibited until explicitly approved.
- **Gate E — Release:** verify compatibility, provider API terms/access, privacy, docs and Marketplace requirements.

## Current handoff

Phases 1–4 have research/specification deliverables. Phase 4 is a proposed architecture/readiness package, not an approval. Product, UX and architecture gates remain open; implementation remains prohibited until the required human gates and separate toolchain gate authorize it.
