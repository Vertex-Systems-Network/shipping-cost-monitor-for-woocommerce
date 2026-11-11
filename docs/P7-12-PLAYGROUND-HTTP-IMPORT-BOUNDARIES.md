# P7-12 authenticated Playground import HTTP boundaries

2026-11-11. Isolated WordPress Playground: WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 (PHP.wasm), installed CI package from `33201201ade1fb4ed88b695c0954c1c9e0f9f893`, dashboard build ID `33201201ade1`. Synthetic processing order #13 had shipping USD 10, deliberately malformed cost metadata `-5`, and no import fingerprint.

Requests used the actual dashboard-generated `import_nonce` from the current authenticated admin page and form-encoded POST to `/wp-admin/admin-ajax.php` with `action=wpe_scm_commit_invoice_rows`. Nonce values are omitted from this record.

| Case | HTTP | Response |
|---|---:|---|
| Invalid nonce | 403 | `success:false`, import request expired |
| 129-character nonce | 403 | `success:false`, import request expired |
| 51 rows with valid nonce | 400 | `success:false`, commit between 1 and 50 matched rows |
| One row with valid nonce and 65-character numeric amount | 200 | `success:true`, row `invalid`, `persisted:false`, `committed:false` |

After all requests, direct WooCommerce CRUD read still returned cost `-5` and empty import fingerprint; no write occurred. This is actual HTTP AJAX transport on the synthetic Playground site, beyond the earlier direct handler probe. It does not establish unauthenticated HTTP behavior, a complete browser CSV upload/preview/commit flow, an independent security/privacy review, or production database behavior. P7-12 remains Partial.
