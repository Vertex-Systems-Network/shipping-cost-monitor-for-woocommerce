# P7-12 Playground browser CSV import flow

Date: 2026-11-11. Isolated WordPress Playground, WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 (PHP.wasm), CI build `33201201ade1`. Synthetic CSVs were selected using the actual browser file input; no merchant data was used.

1. Existing-cost case: a CSV row for synthetic order #13, USD 8.25, parsed and mapped automatically. After clicking Match, the UI displayed `Existing cost on order #13`, compared the prior malformed USD -5.00 with CSV USD 8.25, and withheld the save action. No overwrite.
2. Fresh synthetic processing shipping order #14 was created with USD 12 shipping and no stored cost. A second CSV row for order #14, USD 8.25, was selected in the browser, parsed and mapped. Tax treatment was set to tax-exclusive. Match returned `matched: #14 (processing)`; Save matched costs to orders required a confirmation dialog. After confirming, the row displayed `committed`.
3. A WooCommerce CRUD read showed order #14 cost `8.25`, currency `USD`, evidence `invoice_final`, tax treatment `exclusive`, and a 64-character import fingerprint. The dashboard Update report action displayed USD 22 total shipping across two eligible orders, USD 8.25 confirmed cost, USD 3.75 comparable variance and 1/2 cost coverage. The malformed existing cost of order #13 remained excluded from coverage.

The browser file flow, preview matching, confirmation, authenticated save and report refresh succeeded. This is one synthetic Playground flow and does not constitute an independent security/privacy review or performance/beta evidence. P7-12 remains Partial.
