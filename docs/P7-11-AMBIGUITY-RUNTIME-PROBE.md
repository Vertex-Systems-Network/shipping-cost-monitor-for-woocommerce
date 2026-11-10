# P7-11 ambiguity and commit-flag runtime probe — 2026-11-10

## Exact environment

Disposable WordPress Playground site `raod-adqg-srqq`: WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.4.27 (PHP.wasm), HPOS OrdersTableDataStore. The CI installable ZIP for `b67db0262b2937e15b48e10c4ac1801caa2ee83c` (inner SHA-256 `14eb7ec17b86112e419243f2464bc2a38a3994fb95490255f4ee31ae877eec43`) was installed and activated. The follow-up ZIP for `ddca326f64e02f768224feebb5eeb55102044961` (inner SHA-256 `a050f3612878b724aa40280e1cce8b991f501c6ac4756f4ed1c0681020a6c9e7`) replaced it; `BUILD-ID.txt` was read back as the latter commit. GitHub Actions run [#42925897007](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42925897007) produced the later artifact; the preceding run [#42924563880](https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/actions/runs/42924563880) passed.

Two synthetic Processing orders, #11 and #12, each had a USD 5 shipping line. Within each isolated PHP request, a temporary `woocommerce_order_number` filter made #12 display number `11`, and WooCommerce's documented HPOS search-results filter `woocommerce_cot_shop_order_search_results` returned IDs [11,12] for term `11`. This is a controlled search-index collision fixture, not proof that a particular third-party order-number plugin produces the same results. The filters were not stored in the plugin or site.

| Build/probe | Observed response | Stored outcome |
|---|---|---|
| `b67db02` matcher preview, reference 11 | `ambiguous`; `persisted: false`, `committed: false` | No cost write |
| `b67db02` direct commit for #11 with both candidates | Row `ambiguous` | #11/#12 cost fields remained empty |
| `ddca326` direct commit with same collision | Row `ambiguous`; `persisted: false`, `committed: false` | No cost write |
| `ddca326` unique reference #11 without collision filters | Row `committed`; `persisted: true`, `committed: true` | USD 9.25 committed to #11 |

The earlier `b67db02` handler falsely returned aggregate `persisted: true`/`committed: true` when its only row was rejected. Commit `ddca326` calculates these flags from actual `committed` row statuses. These probes invoke the PHP AJAX handler in WordPress with constructed `$_POST`, current user and nonce. They do not exercise HTTP transport, browser CSV input, concurrent requests or a production carrier/order-number extension. P7-11 remains Partial pending those remaining scopes, including actual HTTP replay.
