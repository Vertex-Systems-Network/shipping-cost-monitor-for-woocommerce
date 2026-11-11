# 5. Exporting the report as CSV

[← Guide contents](README.md)

Click **Export CSV** on the dashboard. The file is named `shipping-cost-report-<from>-to-<to>.csv` and covers the dates shown in the header.

## What is in it — and what is not

One row per eligible order. The file deliberately contains **no customer names, addresses, emails, phone numbers or notes**, so it can be shared with an accountant without exposing customer data.

| Column | Example | Notes |
|---|---|---|
| **Order ID** | `10` | Internal WooCommerce ID |
| **Order number** | `10` | What customers see; differs from the ID if an order-number plugin is used |
| **Order date** | `2026-11-11 16:44:02` | Store time zone |
| **Status** | `processing` | |
| **Currency** | `USD` | Order currency |
| **Shipping refunded (ex tax)** | `0.00` | |
| **Shipping charged net (ex tax)** | `12.00` | Charged minus refunded |
| **Recorded cost** | `8.25` | Blank if none, or if the stored value is invalid |
| **Cost currency** | `USD` | |
| **Evidence** | `invoice_final` | Internal key: `invoice_final`, `merchant_confirmed`, `booked_label`, `estimate` |
| **Cost tax treatment** | `exclusive` | `exclusive`, `inclusive` or `unknown` |
| **Comparable variance** | `3.75` | Filled only when the comparison status is Comparable |
| **Comparison status** | `Comparable` | See below |

## Comparison status values

| Status | Meaning | What to do |
|---|---|---|
| **Comparable** | Confirmed, tax-exclusive, same currency. In the variance. | Nothing |
| **No confirmed cost** | No cost recorded, or the stored amount is not a valid non-negative number. | Record the cost |
| **Evidence not confirmed** | A booked label or estimate is recorded. | Replace it with the invoice amount |
| **Currency mismatch** | Cost currency differs from the order currency. | Enter the cost in the order currency if you want it compared |
| **Tax basis not comparable** | Cost is tax-inclusive or unknown. | Enter the tax-exclusive amount |

## Opening in a spreadsheet

The file is UTF-8 with commas. In Excel use **Data → From Text/CSV** and pick UTF-8 if accents look wrong. Text cells that start with `=`, `+`, `-` or `@` are prefixed with an apostrophe so the spreadsheet cannot run them as formulas.

## Large stores

The export reads orders in pages of 100, so it does not load the whole store into memory at once. Exports have been measured on 100 orders; timings for thousands of orders are not yet published.
