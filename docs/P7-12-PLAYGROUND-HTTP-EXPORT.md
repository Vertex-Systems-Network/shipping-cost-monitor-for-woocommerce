# P7-12 Playground HTTP CSV export and nonce probe

Date: 2026-11-11. Isolated WordPress Playground: WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3.36 (PHP.wasm). Installed CI package from commit `33201201ade1fb4ed88b695c0954c1c9e0f9f893`; dashboard displayed build ID `33201201ade1`.

Synthetic processing order #13 had USD 10 shipping charged and deliberately malformed recorded cost `-5`, invoice-final, USD, tax-exclusive. Dashboard showed one eligible order, zero covered orders, USD 10 charged and no comparable variance.

The dashboard's Export CSV link was requested through Playground's HTTP request tool with its actual page-generated nonce. HTTP 200 returned `text/csv; charset=utf-8` and attachment disposition. Relevant complete data row:

```csv
13,13,"2026-11-11 01:34:56",processing,USD,0.00,10.00,,USD,invoice_final,exclusive,,"No confirmed cost"
```

The Recorded cost and Comparable variance fields are empty. Repeating the same export URL with `_wpnonce=invalid` returned HTTP 403 and WordPress's “The link you followed has expired” response. No order was modified.

The browser click itself did not produce an observable download event in the Playground wrapper; the authenticated Playground HTTP request returned the CSV body. This verifies the actual HTTP export route and nonce rejection, not a browser file-download UX claim. The fixture is synthetic and does not establish independent security/privacy review, import AJAX transport, parser browser matrix, or production database behavior. P7-12 remains Partial.
