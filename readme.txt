=== Shipping Cost Monitor for WooCommerce ===
Contributors: wpessential
Tags: woocommerce, shipping, shipping cost, reports, reconciliation
Requires at least: 7.0
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Record what each WooCommerce order really cost to ship and compare it with what the customer paid for shipping.

== Description ==

Shipping Cost Monitor for WooCommerce, by WPEssential, is an add-on that keeps a shipping-cost record on each order and reports the difference between the shipping your customers paid and the shipping you paid.

It is built around one rule: a quote is an estimate, a booked label is provisional, and only an invoice-final or merchant-confirmed amount counts as a confirmed cost.

**What it does**

* Adds a shipping-cost record to the WooCommerce order editor: amount, currency, evidence type, tax basis and a source note. Every change is written to a private order note.
* Shows a dashboard under WooCommerce > Shipping Cost Monitor with net shipping charged, confirmed cost, variance and cost coverage for a date range of up to 366 days.
* Breaks comparable cost down by shipping method.
* Exports the report as CSV without customer names, addresses, emails or free-text notes.

**What it does not do**

* It does not convert currencies. Costs in a different currency from the order are excluded from totals and shown as a notice.
* It does not buy labels, change shipping rates or edit order totals.
* It does not contact any external service.

**Pro add-on**

Shipping Cost Monitor Pro for WooCommerce is a separate, paid add-on from WPEssential. It adds courier invoice CSV import with exact order matching and reviewed saving, and read-only ShipStation label costs for review. This plugin works fully on its own without it.

**Requirements**

WooCommerce 11.2 or later. The plugin uses WooCommerce order APIs and works with High-Performance Order Storage and legacy order storage.

**Documentation**

Step-by-step guide with screenshots: https://github.com/Vertex-Systems-Network/wpessential-shipping-cost-monitor/blob/main/docs/user-guide/README.md

== Installation ==

1. Install and activate WooCommerce 11.2 or later.
2. In WordPress, go to Plugins > Add New, search for "Shipping Cost Monitor for WooCommerce", then install and activate it. Or upload the plugin ZIP on the same screen.
3. Open WooCommerce > Shipping Cost Monitor.
4. Open any order with a shipping line and fill in the shipping-cost record.

== Frequently Asked Questions ==

= Which orders are included in the report? =

Processing and Completed orders that have a WooCommerce shipping line and were created in the selected period.

= Why is my cost not counted in the variance? =

Variance uses only costs marked invoice-final or merchant-confirmed, in the same currency as the order, with a tax-exclusive basis. Booked-label costs, unknown tax basis and other currencies are listed but not compared.

= Where is the data stored and what happens on uninstall? =

Cost records are stored as order metadata through WooCommerce and stay with your orders if you remove the plugin. The plugin stores no other settings. Uninstalling it also removes a ShipStation key left by a pre-1.0 test build, unless the Pro add-on is installed.

= Does it work with High-Performance Order Storage? =

Yes. All order reads and writes go through WooCommerce order APIs.

== Screenshots ==

1. Dashboard with shipping charged, confirmed cost, variance and coverage.
2. Shipping-cost record in the WooCommerce order editor.
3. Cost by shipping method and CSV export.

== Changelog ==

= 1.0.0 =
* First public release: order shipping-cost record, dashboard report with shipping-method breakdown, and privacy-minimized CSV export.

== Upgrade Notice ==

= 1.0.0 =
First public release.
