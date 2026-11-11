# P7-12 Playground import boundary probes

**Date:** 2026-11-11. **Repository:** `Vertex-Systems-Network/wpessential-shipping-cost-monitor`. **Installed package:** CI artifact `13817888476` from commit `33201201ade1fb4ed88b695c0954c1c9e0f9f893` (same build as [negative-cost regression](P7-12-PLAYGROUND-NEGATIVE-COST-CSV.md)). **Environment:** isolated WordPress Playground, WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3.36, PHP.wasm. WooCommerce and the plugin were active.

Executed the installed `InvoiceImporter::commit_rows()` handler in WordPress bootstrap with synthetic order #13 as the target. Each call supplied a row reference `13`, USD currency, a 64-character synthetic batch hash, `invoice_final` evidence and `exclusive` tax basis; the request variation is below. These calls exercised the PHP handler, not HTTP AJAX transport or browser JavaScript.

| Request variation | Observed handler response | Result |
|---|---|---|
| No logged-in user (`wp_set_current_user(0)`), invalid nonce | `success: false`; permission message | Rejected before nonce/rows |
| Administrator, invalid nonce | `success: false`; expired-request message | Rejected |
| Administrator, 129-character nonce | `success: false`; expired-request message | Rejected |
| Administrator, valid nonce, 51 rows | `success: false`; 1–50 row limit message | Rejected |
| Administrator, valid nonce, 65-character numeric amount | `success: true`, row `invalid`, `persisted: false`, `committed: false` | Rejected without write |
| Administrator, valid nonce, amount `-7` | `success: true`, row `invalid`, `persisted: false`, `committed: false` | Rejected without write |

Post-probe WooCommerce CRUD read of order #13 returned the original malformed cost `-5` and an empty import fingerprint. This confirms the selected rejection paths did not overwrite that order. The note count observed after the probes was two; no before-count was recorded, so no note-count claim is made.

**Disposition:** Narrow Playground PHP runtime checks passed. P7-12 stays **Partial**; HTTP AJAX status/nonce/session transport, browser CSV parser/export matrix, independent security/privacy review and other endpoints remain unverified. This fixture uses Playground's PHP.wasm and does not prove production database behavior.
