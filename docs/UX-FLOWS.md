# UX flows and React admin interface specification

**Status:** UX specification drafted for review  
**Scope:** Proposed MVP from `docs/PRODUCT-SPEC.md`  
**Not included:** interactive React code, plugin code, or toolchain setup. Those remain behind the documented human/toolchain gates.

## UX goals

1. Help a merchant see whether shipping cost data is complete before drawing conclusions.
2. Make actual/confirmed cost visually distinct from quote or booked label cost.
3. Make invoice import and review safer than editing many WooCommerce orders by hand.
4. Keep one shared report filter model and one canonical settings home per option.
5. Make the most common task—finding and resolving shipping cost loss—clear in a few steps.
6. Preserve the WooCommerce admin feel, with clean hierarchy, restrained color, readable tables, useful whitespace and predictable controls.

## Navigation and page hierarchy

Add a **WooCommerce → Shipping Cost Monitor** entry, with these screens:

| Screen | Primary job | Access/availability |
|---|---|---|
| Dashboard | Understand coverage, shipping variance and items needing action | Free summary; Pro adds detailed breakdowns |
| Cost records | Find orders, inspect evidence and record/correct an order cost | Free manual record; Pro adds advanced filters |
| Reconcile invoices | Import courier CSV and resolve source-row exceptions | Pro; Free users see a concise upgrade explanation and can still export their own records |
| Reports | Analyze loss by shipping method/zone and export filtered results | Free basic summary/export; Pro detailed breakdowns |
| Integrations | Connect supported shipping platforms/carriers and view sync health | Optional; list only adapters actually implemented and reviewed |
| Settings | Manage retention, diagnostics and plugin behavior | Grouped sections; never repeat controls owned elsewhere |

Use WooCommerce's admin shell/header and breadcrumbs. Avoid adding duplicate top-level menus for order costs, reports and imports.

## Shared page frame

Every main screen uses the same frame:

1. Page title, one sentence explaining what the screen answers, and a primary action.
2. Shared filter bar: date range, order status, currency, plus optional method/zone filters where relevant.
3. Context line showing timezone, selected currency group and whether displayed amounts are confirmed or provisional.
4. Page content.
5. Persistent help link to metric definitions and source/evidence labels.

The filter bar uses one shared state model. Dashboard, reports and cost-record list must not develop independent date/status/currency settings with conflicting defaults. When a filter is changed, announce that results are updating and retain the visible filter values.

## Screen specifications

### 1. Dashboard

**Primary action:** “Import courier invoice” (Pro) or “Add shipping cost” (Free).

**Content order:**

- Summary cards:
  - Shipping charged to customers
  - Confirmed fulfilment cost
  - Shipping variance
  - Confirmed cost coverage
- Coverage explanation below cards, e.g. “18 of 23 eligible orders have confirmed costs; 3 are booked-label amounts; 2 have no cost record.”
- Pro panels: variance trend and highest-loss shipping methods/zones. Place “Zone unavailable” records in a visible group rather than guessing.
- Action list: unmatched invoice rows, amount mismatches, missing confirmed costs and sync failures.
- Recent cost activity table with order number, date, method, charged amount, confirmed cost, variance, evidence label and next action.

**Card behavior:** Clicking a metric applies its corresponding filter in Cost records/Reports. Negative variance must include “shipping deficit” text/icon; do not rely on red/green color alone.

### 2. Cost records

**Primary action:** “Add cost” opens a focused order search-and-record dialog. Rows lead to the WooCommerce order edit screen or a side detail panel.

**Table columns:** order, order date, destination/zone when available, shipping method, customer-paid shipping, confirmed cost, provisional amount, variance, evidence status, updated.

- Server-side pagination, sorting and search.
- Filters reuse the shared filter bar.
- Missing values show “No cost recorded”; estimates show “Estimate”; label purchase values show “Booked”.
- Do not display blank/unknown values as zero.
- Bulk actions are excluded from MVP unless a specific safe workflow is approved.

**Manual entry dialog:**

1. Search/select one order.
2. Show its shipping line, currency, method and current evidence.
3. Enter cost, source/evidence class, date and optional note.
4. Preview variance and confirm save.
5. Success message links back to the updated order and records an audit event.

### 3. Reconcile invoices

Use a short, recoverable wizard with progress steps:

1. **Upload:** accepted file types, row/file limits, privacy note and link to a sample format.
2. **Map columns:** order/tracking/AWB/provider shipment ID, charge amount, currency, charge date and optional charge type.
3. **Set parsing rules:** date format, decimal/thousands separators, timezone, currency and tax treatment.
4. **Preview:** parsed rows, matched count, duplicates, invalid rows, currency issues and estimated import size.
5. **Review matches:** exact match identifier and confidence basis. Ambiguous rows wait for user review.
6. **Commit:** explicit summary of records to be added/updated; no action occurs before confirmation.
7. **Result:** imported, skipped, duplicate and failed row counts with downloadable error CSV and “Open exception queue”.

Allow returning to earlier steps without losing the uploaded file during the current wizard session. If a step fails, preserve the prior valid mapping and show a recovery action.

### 4. Exception queue

**Tabs/statuses:** Unmatched, Duplicate, Ambiguous, Amount mismatch, Credit/Refund, Invalid. Matched rows are visible in the import batch result/history, not mixed into unresolved work.

**List columns:** source row ID, courier/source, charge date, tracking/AWB/order reference, amount/currency, current status, reason.

Selecting a row opens a review panel with:

- Sanitized source values and original row number.
- Candidate WooCommerce order(s), if any, with the exact matching basis.
- Existing order cost evidence and previous import history.
- Actions appropriate to the state: link to order, mark as credit/refund, ignore with reason, or correct mapping and retry.
- A clear “This will change the selected order cost” summary before saving.

Never provide a one-click “auto-fix all” control in MVP.

### 5. Integrations and sync

Show only adapters actually available in the installed build; do not imply a carrier is supported because it appears in the research shortlist.

**Connection flow:**

1. Select provider.
2. Explain supported regions, required account, permissions, data sent/received, evidence classes supported and known limitations.
3. Connect using the provider's reviewed authentication method.
4. Test connection; show provider-confirmed success/failure.
5. Select sync mode and allowed date/status scope only if supported.
6. Show final setup summary and first-sync action.

**Connection card:** provider/account label, Connected/Attention/Disconnected state, data types synced, last success, last attempt, rows processed, unresolved rows and next scheduled sync if configured.

If a provider returns only quotes/booked amounts, label the data accordingly and explain it will not count as confirmed final cost. On token expiry, stop retries requiring auth and ask the merchant to reconnect.

### 6. Reports

Reuse the shared filter bar. Show:

- Shipping charged, confirmed costs, confirmed variance and coverage by currency.
- Breakdown by shipping method; zone only where a valid historical zone is known.
- Optional chart paired with a readable table.
- Export button exports the currently filtered dataset and states the evidence classes included.

No second copy of date/status/currency controls. No cross-currency grand total without an explicit future FX design.

### 7. Settings

Use grouped settings sections matching the canonical homes in `docs/PRODUCT-SPEC.md`:

- Data & privacy: retention, opt-in diagnostics and uninstall behavior.
- Report defaults: default period/status only if needed; report-level selections remain in shared filters.
- License: Free/Pro entitlement and license state.
- Help & status: version, compatibility notices and links to documentation.

Provider credentials belong only on their provider connection screen. Import mapping belongs only to import profiles. Alert thresholds belong only to the later Alerts screen.

## State and feedback matrix

| Screen/action | Loading | Empty | Error/partial | Success |
|---|---|---|---|---|
| Dashboard | Keep page frame and filters; skeleton cards; announce update | Explain no eligible orders or no cost data; offer “Add cost”/“Import invoice” | Keep last known values visibly marked stale; show retry and affected data window | Cards update; show coverage and last refreshed time |
| Cost records | Table skeleton; preserve filters | “No matching orders” with clear filter reset; separate from “No eligible orders yet” | Row-level load failure message; do not replace values with zero | Inline save confirmation and updated evidence label |
| Invoice wizard | Step-specific progress and non-blocking cancel where safe | Before file selection, show supported formats/sample | Preserve mapping; state failed rows/step; safe retry/back | Preview/commit summaries with exact counts |
| Exception queue | Load rows within selected status | State-specific empty message (e.g. “No unmatched rows”) | Preserve unresolved rows and identify failed action | Update row state, audit confirmation and remaining count |
| Integration setup | Provider-specific connection progress | Disconnected explanation and connect action | Explain auth/rate limit/provider error; redact secrets; retry or reconnect | Provider-confirmed connection, scopes and last-sync status |
| Scheduled/manual sync | Show running job and progress/counts | No sync history yet: explain first sync | Partial sync is clearly partial; retain cursor/errors and safe retry | Completion summary: records, matched, unresolved, skipped |
| Reports/export | Charts/tables skeleton and export disabled while query is invalid | Explain missing data or no orders in selected period | Keep filters, show query/export failure and retry | Show generated time and export confirmation |

## Accessibility and internationalization

- Keyboard operable menus, dialogs, tables, tabs and wizard controls; visible focus state.
- Use semantic headings, labels and form descriptions; announce async progress/results to assistive technology.
- Status uses text/icon plus color; charts have table equivalents.
- Respect WordPress locale, text direction and translated plugin strings, including RTL layouts.
- Use store timezone and currency symbols/code with explicit currency grouping; do not hardcode English status text into APIs.
- Keep numeric records independent of display locale; formatting must never alter stored values.

## Responsive and visual behavior

- Admin-first desktop layout with clear single-column fallbacks on narrow screens; tables may scroll horizontally with key identifiers/variance kept visible.
- Use a restrained WooCommerce-compatible palette; reserve strong red/amber for actionable loss/errors and green for confirmed success, always with text labels.
- Use consistent spacing, typography and control sizes; prioritize legibility over decorative charts.
- Keep charts secondary to numbers, coverage and next actions.

## UX review checklist

This draft is ready for human review when the product owner can answer “yes” to:

- Can a merchant tell booked estimate from confirmed final cost without opening documentation?
- Can the merchant see coverage and currency before interpreting totals?
- Can they import an invoice, understand each match and recover from partial errors?
- Is every setting in exactly one screen/home?
- Are empty, loading, error, partial and success states specified for every primary flow?
- Does the design avoid altering checkout/shipping rates or silently changing order data?
