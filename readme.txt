=== WPEssential Shipping Cost Monitor ===
Contributors: wpessential
Tags: woocommerce, shipping, shipping cost, reports, reconciliation
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Record what each WooCommerce order really cost to ship and compare it with what the customer paid for shipping.

== Description ==

WPEssential Shipping Cost Monitor is a WooCommerce add-on that keeps a shipping-cost record on each order and reports the difference between the shipping your customers paid and the shipping you paid.

It is built around one rule: a quote is an estimate, a booked label is provisional, and only an invoice-final or merchant-confirmed amount counts as a confirmed cost.

**What it does**

* Adds a shipping-cost record to the WooCommerce order editor: amount, currency, evidence type, tax basis and a source note. Every change is written to a private order note.
* Shows a dashboard under WooCommerce > Shipping Cost Monitor with net shipping charged, confirmed cost, variance and cost coverage for a date range of up to 366 days.
* Breaks comparable cost down by shipping method.
* Exports the report as CSV without customer names, addresses, emails or free-text notes.
* Previews a courier or label invoice CSV in your browser, lets you map its columns, matches rows to exact order IDs or order numbers, and saves only the rows you approve.
* Optionally reads booked-label costs from ShipStation (API V2) for review. Label costs are stored as booked-label evidence, never as a final invoice cost.

**What it does not do**

* It does not convert currencies. Costs in a different currency from the order are excluded from totals and shown as a review item.
* It never overwrites an existing cost record during an import.
* It does not upload or store your invoice file. The CSV is parsed in the browser; only the mapped reference, amount and currency of rows you choose are sent to your site.
* It does not buy labels, change shipping rates or edit order totals.

**Requirements**

WooCommerce 11.2 or later. The plugin uses WooCommerce order APIs and works with High-Performance Order Storage and legacy order storage.

== External services ==

This plugin can connect to ShipStation. The connection is optional and is off until a store manager enters a ShipStation API V2 key on the plugin dashboard.

* **What it is used for:** reading the cost of shipping labels already bought in your ShipStation account, so you can review them against WooCommerce orders.
* **What is sent and when:** when you connect, and each time you select "sync" for a date range of up to 31 days, your site sends a read-only request to `https://api.shipstation.com/v2/labels` containing your API key, the date range and a page number. No customer or order data from your store is sent.
* **What is stored:** the API key is stored encrypted in your site database and is deleted when you disconnect or uninstall the plugin. Label data is shown for review and is saved to an order only when you approve a matched row.

ShipStation is provided by Auctane LLC: [Terms of Service](https://www.shipstation.com/terms-of-service/), [Privacy Policy](https://www.shipstation.com/privacy-policy/).

== Installation ==

1. Install and activate WooCommerce 11.2 or later.
2. In WordPress, go to Plugins > Add New, search for "WPEssential Shipping Cost Monitor", then install and activate it. Or upload the plugin ZIP on the same screen.
3. Open WooCommerce > Shipping Cost Monitor.
4. Open any order with a shipping line and fill in the shipping-cost record, or import a courier invoice CSV from the dashboard.

== Frequently Asked Questions ==

= Which orders are included in the report? =

Processing and Completed orders that have a WooCommerce shipping line and were created in the selected period.

= Why is my cost not counted in the variance? =

Variance uses only costs marked invoice-final or merchant-confirmed, in the same currency as the order, with a tax-exclusive basis. Booked-label costs, unknown tax basis and other currencies are listed but not compared.

= Is my invoice file uploaded to my site? =

No. The file is read in your browser. Saved mapping profiles are kept in that browser's local storage and contain column settings only.

= Where is the data stored and what happens on uninstall? =

Cost records are stored as order metadata through WooCommerce and stay with your orders if you remove the plugin. The ShipStation API key, if any, is deleted on uninstall.

= Does it work with High-Performance Order Storage? =

Yes. All order reads and writes go through WooCommerce order APIs.

== Screenshots ==

1. Dashboard with shipping charged, confirmed cost, variance and coverage.
2. Shipping-cost record in the WooCommerce order editor.
3. Invoice CSV preview with column mapping and row review.

== Changelog ==

= 0.1.0 =
* First release: order shipping-cost record, dashboard report, CSV export, invoice CSV review and optional ShipStation booked-label import.

== Upgrade Notice ==

= 0.1.0 =
First release.
