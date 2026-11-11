# P7-11 exact HTTP AJAX replay — 2026-11-11

Disposable WordPress Playground site `yctw-vmbm-okav`: WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.4.27 (PHP.wasm), HPOS. Installed and activated CI ZIP for `ddca326f64e02f768224feebb5eeb55102044961` (inner SHA-256 `a050f3612878b724aa40280e1cce8b991f501c6ac4756f4ed1c0681020a6c9e7`). The plugin dashboard exposed the authenticated import nonce for this browser session. A synthetic Processing order #11 with a USD 5 shipping item was created.

The same form-encoded POST body was sent twice to `/wp-admin/admin-ajax.php` with action `wpe_scm_commit_invoice_rows`, row 2, order/reference 11, USD 9.25, invoice-final evidence, exclusive tax basis and one 64-character batch digest. The nonce was taken from the authenticated dashboard; no nonce or cookie value is retained in this report.

| Request | HTTP status | Row status | Aggregate flags |
|---|---:|---|---|
| First POST | 200 | `committed` | `persisted: true`, `committed: true` |
| Byte-identical replay | 200 | `already_imported` | `persisted: false`, `committed: false` |

WooCommerce CRUD read-back returned cost USD 9.25, the expected import fingerprint and exactly one plugin import note after both requests. A separate bad nonce request returned HTTP 403 and the expiration message before the authenticated request. This is real HTTP AJAX transport in Playground with synthetic data, not a production MySQL or carrier-account test. The exact replay result complements [the controlled HPOS ambiguity runtime fixture](P7-11-AMBIGUITY-RUNTIME-PROBE.md) and [the malformed/mismatch/re-import handler probe](P7-11-12-ISOLATED-RUNTIME-PROBE.md). Earlier el-test browser preview covered refund-status/negative-credit exclusion; see the canonical Phase 7 register for that environment's evidence. No concurrent-request race test or real third-party order-number extension is claimed.
