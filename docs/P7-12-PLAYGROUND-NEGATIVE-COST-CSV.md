# P7-12 Playground negative-cost CSV regression

**Date:** 2026-11-11  
**Repository:** `Vertex-Systems-Network/wpessential-shipping-cost-monitor`  
**CI package:** commit `33201201ade1fb4ed88b695c0954c1c9e0f9f893`, Actions run `42936293499`, artifact `13817888476`; inner installable ZIP SHA-256 `e6f8c58f54c04306a167ebafbf6ced20a3cd5af6fe073c4ec822c861dbdacf0a`.  
**Environment:** isolated temporary WordPress Playground, WordPress 7.1.4, WooCommerce 11.2.2, PHP 8.3.36 (PHP.wasm/Playground); WooCommerce and plugin activated.

## Synthetic fixture and observed result

Created processing WooCommerce shipping order #13 using WooCommerce CRUD, currency USD, flat-rate shipping USD 10.00, and deliberately malformed `_wpe_scm_cost_amount = -5` with `invoice_final` evidence, USD cost currency and `exclusive` tax basis. The order total was USD 10.00; stored malformed cost remained `-5`.

`Summary::build()` returned `eligible_orders: 1`, `confirmed_cost_orders: 0`, `comparable_orders: 0`; USD shipping charged was `10.00`, confirmed cost and variance were `0.00`. Direct execution of the installed `CsvExport::export()` with an administrator user and a fresh action nonce returned:

```csv
"Order ID","Order number","Order date",Status,Currency,"Shipping refunded (ex tax)","Shipping charged net (ex tax)","Recorded cost","Cost currency",Evidence,"Cost tax treatment","Comparable variance","Comparison status"
13,13,"2026-11-11 01:34:56",processing,USD,0.00,10.00,,USD,invoice_final,exclusive,,"No confirmed cost"
```

**Result:** Pass for this controlled negative-cost regression: recorded-cost and comparable-variance cells are blank, and the CSV does not label the malformed cost Comparable. The original order metadata was not rewritten by the report or export. A direct HTTP export attempt using a nonce created in a separate PHP invocation returned 403 (expired link), so this evidence is PHP runtime export execution, not a passed HTTP download/authentication test.

**Remaining P7-12:** independent security/privacy review, browser/HTTP malformed-input and export transport matrix. This result does not close P7-12 or establish production database behavior.
