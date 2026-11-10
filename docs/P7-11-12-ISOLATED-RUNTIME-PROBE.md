# P7-11/12 isolated runtime probe — 2026-11-10

## Environment and scope

Disposable WordPress Playground site `pjju-pkmv-rihh`: WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.4.27 (PHP.wasm). Installed and activated the CI installable ZIP for source commit `bbf95766b084` (inner ZIP SHA-256 `a01f715c09b872be4d734da64e3f18c56e5ec97b2506af76d09ed4703a2ab5fe`). These calls invoked the actual PHP AJAX handler method in the WordPress runtime with constructed `$_POST`, current user and nonce. This is an isolated handler probe, not an HTTP/browser AJAX transport test and not a MySQL staging test.

Synthetic order #12 had a USD 5 shipping item and Processing status. Admin ID 1 had `edit_shop_order` permission. A reviewed invoice-final, tax-exclusive USD 11.00 row with reference `12`, row 2 and a 64-character batch digest was committed. A second synthetic order #13 was created for an order-number collision attempt.

| Probe | Actual handler response | Stored effect |
|---|---|---|
| Initial commit | `committed` | Order #12 cost USD 11.00 and one private order note |
| Exact same row/batch replay | `already_imported` | Cost remained USD 11.00; one note total |
| Different batch, same order | `existing_cost` | No overwrite |
| Text amount `abc` | `invalid` | No overwrite |
| Negative amount `-3` | `invalid` | No overwrite |
| Currency `EUR` for USD order | `currency_mismatch` | No overwrite |
| Reference `999` against order #12 | `order_mismatch` | No overwrite |
| Two rows targeting order #12 in one request | Both `duplicate_order_in_batch` | No overwrite |
| Anonymous user | Error: permission denied | No write |
| Invalid nonce | Error: request expired | No write |
| 51-row request | Error: 1–50 rows required | No write |

No invoice row, raw file, customer identity or credential is recorded in this evidence. The persisted order cost and note count were read back after the probes. The output flag `persisted: true, committed: true` describes handler completion even when every row is rejected; consumers must inspect each row status. Consider making aggregate flags precise in a separate change.

## Remaining gate limits

An `_order_number` meta collision plus a temporary `woocommerce_order_number` display filter made order #13 display number 12, but `wc_order_search('12')` returned only order #12. This fixture does not reach the `ambiguous` matcher branch. A genuine duplicate-number provider/search-index fixture is still required; do not mark it passed from the source branch alone. Refund/credit preview was covered previously on staging, but this probe did not replay that browser path.

P7-12 still requires independent review of all listed boundaries and a staging HTTP/browser request matrix, including frontend CSV parser and export formula handling. No security certification is claimed. P7-13 requires 100/1,000/10,000-order five-run measurements on a suitable staging copy with PHP memory and DB queries. P7-14 requires consented real-merchant beta and owner acceptance. Neither can be substituted by the disposable synthetic Playground.
