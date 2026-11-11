# 2. Key ideas: evidence, tax basis and currency

[← Guide contents](README.md)

Three rules decide whether a cost is counted in the totals. They exist so the report never mixes a guess with a bill, or dollars with euros, or prices with and without tax.

## Rule 1: evidence — how sure is this number?

Every cost carries an **evidence** type that says where the number came from.

| Evidence (on screen) | Meaning | Counted as confirmed cost? |
|---|---|---|
| **Invoice-final** | Taken from the courier's final invoice or statement. | Yes |
| **Merchant-confirmed** | You checked it yourself and stand behind it (for example a cash payment to a local driver). | Yes |
| **Booked label / manifest** | The price shown when the label was bought. Couriers often adjust it later for weight, size, fuel or address corrections. | No — shown, never counted |
| **Estimate only** | A quote or rate-table guess. | No — shown, never counted |

**Why:** a label price of 4.00 can become an invoice of 6.30 after a dimensional-weight check. Counting the label would make delivery look 2.30 cheaper than it was.

## Rule 2: tax basis — does the number include tax?

WooCommerce's shipping total in this report is **before shipping tax**. To compare like with like, a cost must also be **before tax**.

| Tax treatment (on screen) | Compared? |
|---|---|
| **Tax-exclusive** | Yes |
| **Tax-inclusive** | No — the plugin does not know the tax rate to remove |
| **Unknown — do not compare** | No |

If your invoice shows tax separately, enter the amount without tax and choose **Tax-exclusive**.

## Rule 3: currency — same money only

A cost is compared only when its currency equals the order's currency. The plugin never converts currencies, because exchange rates on the invoice date and order date differ and a silent conversion would hide that.

A cost in another currency is kept on the order, excluded from the totals, and counted in a notice above the method table: **"N confirmed cost record(s) use a different currency and are excluded from the confirmed-cost total and coverage."**

## Putting the rules together

A cost is **comparable** only when all three hold:

```
evidence is Invoice-final or Merchant-confirmed
AND tax treatment is Tax-exclusive
AND cost currency = order currency
```

Comparable costs are the only ones in **Shipping variance** and in the method table.

## Which orders are in the report at all

An order is **eligible** when it:

- has the status **Processing** or **Completed**, and
- has at least one WooCommerce shipping line (local pickup counts; orders with no shipping line do not), and
- was created inside the selected date range.

Pending, on-hold, cancelled, refunded and failed orders are left out, because they are not settled sales.

## Refunds

If you refund part of the shipping on an order, the report uses **net shipping**: shipping charged minus the shipping refunded, never below zero. A cost record is not changed by a refund; if the courier also credited you, update the cost.

## The six example orders used in this guide

| Order | Method | Customer paid | Cost recorded | Evidence | Tax | Comparable? |
|---|---|---|---|---|---|---|
| A | Flat rate | 12.00 | 8.25 | Invoice-final | Exclusive | Yes |
| B | Flat rate | 10.00 | 11.40 | Invoice-final | Exclusive | Yes |
| C | Flat rate | 15.00 | — | — | — | No cost yet |
| D | Express | 25.00 | 19.90 | Merchant-confirmed | Exclusive | Yes |
| E | Express | 25.00 | 27.10 | Invoice-final | Exclusive | Yes |
| F | Local pickup | 5.00 | 4.00 | Booked label | Unknown | No — label price |

All in USD, all Processing. [Chapter 4](04-dashboard.md) walks through the totals these produce.
