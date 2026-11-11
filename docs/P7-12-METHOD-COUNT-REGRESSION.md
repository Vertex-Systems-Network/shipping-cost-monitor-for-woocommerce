# Shipping method order-count regression

2026-11-11. Source review of `Summary::build()` and the admin table found that each method row's `shipping_charged` includes all eligible shipping orders for that method, but the UI's Orders cell used `comparable_orders`. This made the displayed count inconsistent with the charged total whenever some orders lacked comparable cost evidence.

On isolated WP 7.1.4 / WC 11.2.2 / PHP 8.3.36 Playground, two synthetic processing shipping orders used the same fixture method. One had malformed negative cost and one had confirmed invoice-final tax-exclusive USD 8.25 cost. Before the fix the method row displayed **Orders 1 / Shipping charged USD 22.00 / Confirmed cost USD 8.25 / Variance USD 3.75**. The report summary showed two eligible orders and 1/2 cost coverage.

Commit `3b8b5c9162ca78a6e2fe99cf2b03cc3da49f4fc4` changes the UI Orders cell to use `method.orders`, which is populated by the same accumulation path as `shipping_charged`. The exact one-line change was applied to the installed Playground test copy, then the dashboard report was refreshed. The row displayed **Orders 2 / Shipping charged USD 22.00 / Confirmed cost USD 8.25 / Variance USD 3.75**. The count and amount now share the same population. The variance remains based on one comparable order.

This verifies the focused UI regression in Playground. The installed plugin package still identifies as build `33201201ade1` because this was a direct test-copy source edit; it is not evidence that a CI package from the new repository commit was installed. The independent security/privacy review and P7-13 benchmark remain open.
