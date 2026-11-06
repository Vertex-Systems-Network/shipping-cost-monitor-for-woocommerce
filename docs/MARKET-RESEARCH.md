# Market research: WooCommerce shipping-cost intelligence

**Reviewed:** 2026-11-06  
**Purpose:** Identify existing workflows, provider API capabilities and a credible product gap. This is a focused desk review, not a full customer survey or exhaustive provider audit. Listing details and API access change; recheck before implementation and Marketplace submission.

## What the market already covers

| Product / source | Existing capability relevant to this product | Product implication |
|---|---|---|
| [Cost of Shipping for WooCommerce](https://wordpress.org/plugins/woo-cost-of-shipping/) | Manual actual shipping cost on each order; imports supported costs from WooCommerce Shipping/WooCommerce Services and ShipStation; multiple packages and void/reship scenarios are described. | Manual tracking and some label-cost imports are not unique differentiators. Our opportunity needs broader invoice matching and clearer exception handling. |
| [Profitrics documentation](https://woocommerce.com/document/profitrics/) | Shipping-cost rules by method and class, per-order profit ledger, manual overrides, reports, trends and exports. | Method/class cost rules and general profit reporting are established patterns. Keep this plugin shipping-focused; avoid rebuilding a full profit suite. |
| [Cost & Reports for WooCommerce](https://woocommerce.com/products/cost-reports-woocommerce/) | Cost, shipping, profit and financial reporting with filtering and exports. | General profit reports overlap; win through shipping-cost data quality and operational reconciliation. |
| [WooCommerce Cost of Goods](https://woocommerce.com/products/woocommerce-cost-of-goods/) | Cost fields and profit reporting; docs describe configurable inclusion/exclusion of shipping and historical cost behavior. | Interoperability and unambiguous definitions matter. Don't silently redefine store profit. |
| [WooCommerce Shipping](https://wordpress.org/plugins/woocommerce-shipping/) | Label purchasing, live label-rate comparison, split shipments, tracking and address verification for supported markets. | Labeling/fulfilment is already served. Integrate/import cost evidence when available; do not compete as another label tool. |

## Carrier API discovery

The initial provider shortlist is **UPS, FedEx, DHL Express**, plus platforms/aggregators already used by target merchants (including ShipStation). It is intentionally a shortlist, not an exhaustive or promised integration list.

Official API documentation shows different data capabilities:

| Provider | Publicly documented API capability found | What must be validated for our use |
|---|---|---|
| [UPS Developer Portal](https://developer.ups.com/get-started) | Rating and shipping APIs; account number can affect negotiated rating, and payment details can bill a shipment. | Which endpoints expose post-shipment billed charges/invoice adjustments for the merchant's account, access approval and regional availability. |
| [FedEx Developer Portal](https://developer.fedex.com/api/en-us/catalog/comprehensive-rate.html) | Account-specific rate estimates including discounts, surcharges and fees; its API catalog includes broader billing-related functionality. | A rate response is still a quote. Verify the exact endpoint for final billed shipment/invoice charges and the required account permissions. |
| [DHL Express MyDHL API](https://developer.dhl.com/api-reference/dhl-express-mydhl-api) | Account-based rates, shipment creation/labels, pickup, tracking, and related shipment services. | Verify what actual invoice/adjustment data is accessible through API versus invoice documents/portals and by region. |
| [ShipStation API](https://www.shipstation.com/developer-api/) | Platform integration is already supported by a related shipping-cost plugin for shipment-cost import workflows. | Determine current API fields, authentication model, account tiers and actual-vs-estimated cost meaning before adopting or expanding the adapter. |

### Important distinction: quote versus actual charge

A carrier **rating/quote endpoint** predicts a rate from shipment details; it is not proof of the final billed amount. A **label-purchase or manifest response** may provide a booked/label amount, which can still differ from final invoice charges after adjustments. The product should label evidence state and source, and only treat invoice-final amounts or reviewed merchant-confirmed values as actual final cost.

## Findings and differentiation

- Actual order-level cost storage and manual entry already exist.
- Some shipping-label costs can already flow into orders from selected platforms.
- Shipping-method and shipping-class rules, order-level overrides, aggregate charts, date filters and exports also exist in adjacent profit products.
- Generic courier invoice CSV ingestion, reviewable order matching, unmatched/duplicate/delta exceptions and shipping-specific loss analysis appear less directly covered in the reviewed primary listings/docs. Treat this as a hypothesis to validate, not guaranteed exclusivity.
- Carrier API support can improve automation, but connectors alone are not a moat. The product advantage should be a consistent cross-provider cost ledger and reconciliation workflow.
- Rate-change simulation should be a later decision-support feature. It must use labelled assumptions and historical data, never change live rates automatically.

## Integration prioritization method

Score each requested provider against:

1. Share of target merchants using it and target-region relevance.
2. Whether API returns order/shipment identifiers needed for reliable matching.
3. Whether it exposes final invoice/billed charges, not only rate quotes.
4. Authentication/security burden, developer approval and account eligibility.
5. Rate limits, webhook/polling support, stability and long-term maintenance.
6. Whether an existing aggregator integration can cover the same workflow with lower support cost.

Build a common adapter interface, then ship only the providers with strongest merchant demand and a reliable actual-cost data path. Keep manual entry and CSV as fallback.

## Proposed validation questions

1. Which courier invoice formats do target stores receive, and how often do formats change?
2. What identifiers are present reliably: order number, tracking ID, AWB, date, recipient, amount, currency?
3. Which APIs expose final billed charges, adjustments or refunds to the merchant's account?
4. How often do invoice charges split across parcels, include surcharges, or get credited/refunded later?
5. Do merchants need zone reporting from WooCommerce shipping zones, postcode regions, or their own territory mapping?
6. Which imports and reports are essential in Free, and which reconciliation volume/automation justifies Pro?
7. What does a merchant consider “actual shipping cost” when taxes, COD fees, fuel surcharge, return-to-origin, and insurance are separate?

## Scope conclusion

The first release should not promise “all courier integrations.” Start with dependable manual costs and a configurable CSV reconciliation pipeline, alongside only those API adapters that pass the capability/security review and have validated merchant demand. Do not mark a quote-only provider integration as actual-cost automation.

## Sources

- [Cost of Shipping for WooCommerce — WordPress.org listing](https://wordpress.org/plugins/woo-cost-of-shipping/)
- [Profitrics — WooCommerce Marketplace documentation](https://woocommerce.com/document/profitrics/)
- [Cost & Reports for WooCommerce — Marketplace listing](https://woocommerce.com/products/cost-reports-woocommerce/)
- [Cost of Goods — WooCommerce docs](https://woocommerce.com/document/cost-of-goods-sold/)
- [WooCommerce Shipping — WordPress.org listing](https://wordpress.org/plugins/woocommerce-shipping/)
- [UPS API getting started](https://developer.ups.com/get-started)
- [FedEx Comprehensive Rates API](https://developer.fedex.com/api/en-us/catalog/comprehensive-rate.html)
- [DHL Express MyDHL API](https://developer.dhl.com/api-reference/dhl-express-mydhl-api)
- [ShipStation developer API](https://www.shipstation.com/developer-api/)
