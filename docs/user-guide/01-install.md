# 1. Install and first run

[← Guide contents](README.md)

## Requirements

| Item | Minimum |
|---|---|
| WordPress | 7.0 |
| WooCommerce | 11.2 |
| PHP | 8.1 |
| Order storage | High-Performance Order Storage (HPOS) or legacy post storage; both are supported |
| User role | Shop Manager or Administrator (the `manage_woocommerce` capability) |

## Install the free plugin

**From WordPress.org** (once published):

1. In WordPress go to **Plugins → Add New**.
2. Search for **Shipping Cost Monitor for WooCommerce**.
3. Click **Install Now**, then **Activate**.

**From a ZIP file:**

1. Go to **Plugins → Add New → Upload Plugin**.
2. Choose `shipping-cost-monitor-for-woocommerce.zip` and click **Install Now**.
3. Click **Activate Plugin**.

If WooCommerce is not active, WordPress will not activate the plugin and shows that it requires WooCommerce. Activate WooCommerce first.

## Install the Pro add-on

1. Make sure the free plugin is installed and active.
2. Upload `shipping-cost-monitor-pro-for-woocommerce.zip` under **Plugins → Add New → Upload Plugin** and activate it.

Pro shows **"Shipping Cost Monitor Pro needs Shipping Cost Monitor for WooCommerce and WooCommerce to be installed and active."** if either is missing, and stays inactive until they are.

On the **Plugins** screen the free plugin then shows **Required by: Shipping Cost Monitor Pro for WooCommerce**. WordPress will not let you deactivate the free plugin while Pro is active; deactivate Pro first.

## Where to find it

**WooCommerce → Shipping Cost Monitor** opens the dashboard. The cost fields are in every order's edit screen (**WooCommerce → Orders → open an order**).

## What you see on day one

Until you record costs, the dashboard shows what customers paid for shipping but no confirmed cost, so variance reads **Not available** and coverage is **0%**. That is expected: the plugin never guesses a cost.

Next: [Key ideas](02-key-ideas.md) explains which costs count. Then [record your first cost](03-record-a-cost.md).

## Upgrading from a test build named "WPEssential Shipping Cost Monitor"

Test builds before version 1.0.0 used a different folder name. If one is still active, the new plugin pauses itself and shows **"Shipping Cost Monitor for WooCommerce is paused because an older build named "WPEssential Shipping Cost Monitor" is active."** Deactivate the old build; your cost records carry over because they are stored on the orders.

## Removing the plugin

- **Free:** deactivating or deleting keeps every cost record on your orders. Reinstalling shows them again. The free plugin stores no settings.
- **Pro:** deleting Pro removes the stored ShipStation API key. Costs it saved stay on the orders.
