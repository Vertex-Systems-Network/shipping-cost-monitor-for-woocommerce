# Carrier and platform API capability matrix

**Desk review date:** 2026-11-06  
**Scope:** Global shortlist plus GCC and South Asia candidates. This matrix uses public provider documentation only. It does not claim that credentials, approvals, commercial terms, endpoints, or final billing data have been tested against merchant accounts.

## Evidence labels

- **Final billing:** post-shipment invoice/adjustment charge data that can be reconciled to the carrier's final bill.
- **Booked amount:** price returned during label/shipment booking; may be adjusted later.
- **Quote:** estimated/contract rate, not evidence of what was ultimately billed.
- **Unknown:** public documentation reviewed did not establish that the required data is exposed to a merchant account.

| Candidate | Region relevance | Public API evidence found | Cost evidence class established publicly | Decision / next validation |
|---|---|---|---|---|
| WooCommerce Shipping | US stores (USD) | Official docs describe a Shipping Labels analytics report with CSV download listing purchase date, order, label price, service, tracking number and refund status. Label buying requires a connected WordPress.com account and is limited to supported US/USD stores. | Booked label price and completed label refunds; carrier adjustments can later change final cost, so this is not final invoice evidence. | Best near-term no-API source. A conservative source preset suggests order/price fields, uses matched-order currency and booked-label evidence, and holds refund/multiple-label exceptions. Verify actual export headers and split-label behavior with anonymized samples before treating it as validated. |
| UPS | Global, account/market dependent | OAuth 2.0; Rating and Shipping APIs; account number can affect negotiated rates and shipping billing. | Quote and shipment/label workflow verified; final post-shipment invoice feed unknown in public docs reviewed. | High-priority global candidate, but verify billing/adjustment endpoints and account eligibility before calling cost sync automatic. |
| FedEx | Global, account/market dependent | Rate/Transit, Ship and visibility APIs; account-specific rate estimates include applicable surcharges; provider docs describe broader billing-related API capabilities. | Quote and label/shipment workflows; final transport invoice export/API not established in the public sources reviewed. | High-priority global candidate. Ask FedEx/API program which account API exposes final transportation charges and licensing terms. |
| DHL Express / MyDHL | Global, account/market dependent | Account-based rating, shipment/label creation, tracking and other shipment APIs. | Quote and shipment/label workflows; final invoice/adjustment read API not confirmed. An invoice upload endpoint is not proof of invoice retrieval. | High-priority global candidate; validate invoice/adjustment access by region and API product. |
| Aramex | GCC and international markets | Rate calculator, shipment creation/labels, pickup and tracking APIs. | Rate quote and shipment booking; final billed-cost feed unknown. | High regional candidate for GCC; request merchant account API docs for invoice or shipment charge lookup. |
| Emirates Post | UAE | Official integration docs list booking/delivery, labels, tracking, and a Prices API that calculates the customer's rate-card price. | Rate-card quote; final courier invoice evidence unknown. | UAE candidate if merchants use it; rate API alone does not satisfy actual-cost import. |
| iMile | UAE, Saudi Arabia, Egypt and other stated markets | Official FAQ advertises REST API for order creation, tracking webhooks and label generation; availability varies by market. | Booking/label workflow; final invoice amount unknown. | Validate account-level costs and invoicing access before ranking for an adapter. |
| iMile Express (Merdi Express Ltd.) | Hong Kong-based 3PL platform serving Far East destinations; not confirmed as the same iMile courier operation used across GCC/South Asia | Official developer docs publish UAT/live REST endpoints, bearer test/live keys, cursor-based shipment listing, and shipment detail with `billing.status`, `charged_hkd`, `adjustments_hkd`, currency and settled time. Some routes may remain pending and never expose a final fee. | Final billing fields are documented for settled shipment records; pending records are not final costs. Account access and representativeness for the intended market are unverified. | Technical adapter candidate only. Confirm target merchants use this exact service, obtain a beta account, validate fields/terms, and complete Gate C-3 credential-security review before enabling sync. |
| TCS | Pakistan | Official TCS developer portal exposes API documentation and Ship4Me API material. | Shipment/booking API existence found; cost-vs-invoice semantics and billing feed not confirmed from public page. | Pakistan candidate; request sandbox/account documentation and determine whether booked charges or final invoices are returned. |
| PostEx Pakistan | Pakistan (stronger regional fit for local merchants) | Official PostEx Pakistan pages advertise user-friendly APIs for e-commerce platforms, list WooCommerce as an available integration, and describe nationwide delivery, COD, return management and early settlement. | API/WooCommerce integration existence is confirmed publicly; exact shipment identifiers, final billed freight, adjustments/credits and invoice endpoints remain unknown. Upfront COD settlement is not equivalent to final shipping-cost evidence. | Promote to merchant-discovery shortlist, not an approved adapter. Obtain current merchant API docs, an account sandbox and anonymized settled shipment/invoice examples; verify cost fields, scopes, terms and security before implementation. Do not confuse PostEx Pakistan (`postex.pk`) with the unrelated Postex Netherlands docs at `postex.com`. |
| Leopards Courier | Pakistan | Official e-commerce services and merchant portal found; public API capability documentation was not confirmed in this desk review. | Unknown. | Keep on discovery list; ask for current merchant API documentation and account availability. |
| ShipStation V2 | Global; account, plan and connected-carrier availability dependent | Official API documents `GET https://api.shipstation.com/v2/labels`, `API-Key` authentication, date filtering, pagination, label/tracking/shipment identifiers and a `shipment_cost` amount/currency object. [List Labels](https://docs.shipstation.com/list-labels); [Authentication](https://docs.shipstation.com/getting-started). | **Booked label only**, not final carrier invoice; later carrier adjustments, voids/refunds, surcharges and tax basis must be reconciled separately. | Read-only adapter implemented. Preview only; explicit merchant match/review and existing-cost/duplicate controls govern commit. Runtime auth/sync needs an eligible merchant API key; no final-cost claim. |

## Market implication

1. **Universal invoice CSV import is essential.** Public carrier APIs do not uniformly establish a reliable final invoice feed.
2. **Capture booked label cost when available**, but store it as a separate evidence class. Reconcile it later against invoice data/adjustments.
3. **Rate APIs serve rate comparison and simulation**, not historical actual cost.
4. **Build a shared adapter framework, not a promise to integrate every carrier.** Pick one or two providers after merchant usage data, account access, regional coverage and real invoice examples are available.
5. **Best product wedge remains cross-source reconciliation:** link WooCommerce order, carrier shipment, booked cost and final invoice rows; surface unmatched, duplicate, split, returned and adjusted charges.

## Proposed integration sequence

- **Foundation:** manual entry + validated generic CSV invoice import + WooCommerce order/label evidence when supported.
- **First API connector:** select the provider most used by target beta merchants that supplies stable shipment identifiers and at least shipment/label amount data.
- **Final-cost automation:** add only after the provider confirms an API or structured export for final charges/credits.
- **Regional expansion:** choose GCC and South Asia carriers based on beta-merchant usage, not brand familiarity alone.

## Questions to resolve with provider accounts

- Which endpoint exposes shipment/charge/invoice IDs and final amount, currency, surcharge, refund/credit?
- Is data available to ordinary merchant accounts or only approved partners/enterprise accounts?
- Can a merchant authorize a third-party WooCommerce extension with OAuth or must API credentials be copied into WordPress?
- Are APIs/transactions paid, rate-limited, region-restricted, or subject to resale/compatibility certification?
- Are webhooks available and signed? If not, what polling cadence is permitted?
- What retention and privacy terms apply to shipment addresses, names, phone numbers and invoice data?

## Sources

- [WooCommerce Shipping docs](https://woocommerce.com/document/woocommerce-shipping/) — supported US/USD label workflow and carrier set.
- [UPS Get Started](https://developer.ups.com/get-started) and [UPS API FAQ](https://developer.ups.com/support?loc=en_UY) — account, rating/shipping and OAuth details.
- [FedEx Comprehensive Rates API](https://developer.fedex.com/api/en-us/catalog/comprehensive-rate.html) and [FedEx API portal](https://developer.fedex.com/api/en-us/home.html) — account rate estimates, APIs, webhooks and account/project prerequisites.
- [DHL Express MyDHL API](https://developer.dhl.com/api-reference/dhl-express-mydhl-api) — rating, shipment, tracking and invoice-data upload functionality.
- [Aramex API overview](https://www.aramex.com/ae/en/developers-solution-center/aramex-apis) — rates, shipping and tracking.
- [Emirates Post Integration Solutions](https://www.emiratespost.ae/all-services/integration-solutions) — booking, labels, tracking and rate-card prices API.
- [iMile business/API FAQ](https://www.imile.com/SA-en/CustomerService/FAQ) — REST API, order creation, tracking webhooks and label generation; does not establish final cost fields.
- [iMile Express API overview](https://docs.imileexpress.com/) and shipment/billing API reference — separate Hong Kong/Far East service; documents settled billing fields on shipment details. Public docs do not prove merchant account access or GCC/South Asia service equivalence.
- [TCS Developer Portal](https://developer.tcscourier.com/doc) — official API portal and Ship4Me material.
- [PostEx Pakistan COD and integration page](https://postex.pk/cod) — advertises APIs, WooCommerce integration, nationwide delivery and flexible pricing; does not publish final-billing schema.
- [PostEx Pakistan FAQs](https://postex.pk/faqs) — describes nationwide courier/COD/returns and early settlement; not evidence of final carrier invoice costs.
- [PostEx Pakistan COD services blog](https://postex.pk/blogs/cod-services-pakistan) — states API/plugin availability for WooCommerce; account-specific API docs and cost fields still require verification.
- Do not use [Postex Netherlands API docs](https://postex.com/en/postex-api/) as evidence for PostEx Pakistan; similarly named but distinct service.
- [Leopards Courier e-commerce services](https://pk.leopardscourier.com/business/ecommerce-economy) — merchant service offering; public API docs not established here.
- [Cost of Shipping for WooCommerce](https://wordpress.org/plugins/woo-cost-of-shipping/) — existing ShipStation and WooCommerce Shipping cost import feature.
