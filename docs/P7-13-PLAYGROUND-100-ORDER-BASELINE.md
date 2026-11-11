# P7-13 partial performance baseline: 100-order Playground lane

2026-11-11. Isolated WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 PHP.wasm Playground, installed plugin build `33201201ade1` with a direct test-copy one-line admin UI correction. This is not MySQL/MariaDB staging and is not a CI package of the later UI commit.

Fixture: exactly 100 eligible Processing shipping orders in 2026-10-13 through 2026-11-11. Two earlier synthetic orders had shipping USD 10 and USD 12; 98 further USD 12 orders were created. One had valid invoice-final tax-exclusive cost USD 8.25; one held deliberately malformed cost -5 and was excluded. Expected shipping charged: USD 10 + 99 × USD 12 = USD 1,198; covered 1/100. A first bulk fixture command timed out in the browser control layer but continued to write; the count was checked and 17 extra disposable orders were changed to Cancelled, leaving exactly 100 Processing orders before measurement.

Dashboard `Summary::build` was called five times in one WordPress-bootstrapped PHP process. Elapsed time is server-side `microtime(true)`; query count is the `get_num_queries()` delta; peak is `memory_get_peak_usage(true)`. The first run is cold within that process and later runs benefit from WooCommerce/WordPress object caching.

| Run | Seconds | Query delta | Peak PHP bytes | Eligible | Charged USD | Covered |
|---|---:|---:|---:|---:|---:|---:|
| 1 | 0.714 | 114 | 81,788,928 | 100 | 1,198.00 | 1 |
| 2 | 0.057 | 2 | 81,788,928 | 100 | 1,198.00 | 1 |
| 3 | 0.051 | 2 | 81,788,928 | 100 | 1,198.00 | 1 |
| 4 | 0.045 | 2 | 81,788,928 | 100 | 1,198.00 | 1 |
| 5 | 0.043 | 2 | 81,788,928 | 100 | 1,198.00 | 1 |

A separate prior single call measured 0.631 s, 114 queries, 81,788,928 peak bytes with the same 100/1,198/1 result. No slow-query capture was available. The CSV export handler was then measured separately in five WordPress-bootstrapped PHP executions with the same 100-order fixture and a valid admin nonce. Its streamed response was counted outside the PHP output buffer; the wrapper's internal `rows:0` was an instrumentation artifact, not an empty export.

| Export run | Seconds | Query delta | Peak PHP bytes | CSV data rows | First/last order ID |
|---|---:|---:|---:|---:|---|
| 1 | 0.706 | 118 | 81,788,928 | 100 | 112 / 13 |
| 2 | 0.637 | 118 | 81,788,928 | 100 | 112 / 13 |
| 3 | 0.785 | 118 | 81,788,928 | 100 | 112 / 13 |
| 4 | 0.786 | 118 | 81,788,928 | 100 | 112 / 13 |
| 5 | 0.821 | 118 | 81,788,928 | 100 | 112 / 13 |

Export row #14 contained USD 12 charged, USD 8.25 cost and USD 3.75 variance; row #13 retained blank invalid cost/variance. Unlike the summary loop, each export run was a separate PHP process; timings include WordPress bootstrap and the handler. This is direct handler execution, not five independent browser HTTP downloads. The export matches the independent 100-order fixture count. The 1,000 and 10,000 eligible-order sizes, slow-query capture, five independent HTTP request timings, and a real database staging copy remain unmeasured. These results are exploratory and do not establish a performance budget or P7-13 Pass.
