# 8. Troubleshooting and FAQ

[← Guide contents](README.md)

## Dashboard

**Variance says "Not available".**
No eligible order has a comparable cost yet. A cost is comparable only if it is Invoice-final or Merchant-confirmed, Tax-exclusive, and in the order's currency. See [key ideas](02-key-ideas.md).

**An order I edited is missing from the report.**
Check that it is Processing or Completed, has a shipping line, and was created inside the date range. The date is the order creation date, not the shipping date.

**Confirmed cost is lower than the costs I entered.**
Booked-label, estimate and other-currency costs are not counted. Export the CSV and look at **Comparison status**.

**"The selected dates were invalid or exceeded 366 days."**
Choose a range of 366 days or less, with From before To.

**"N confirmed cost record(s) use a different currency…"**
Those orders have a cost in another currency. They are kept but not totalled.

**The dashboard is blank.**
Check that WooCommerce is active and your browser has JavaScript enabled. The page needs WordPress's built-in React (`wp-element`), which another plugin can break; try with other plugins deactivated.

## Order editor

**My cost disappeared after saving.**
The amount was not accepted. Use digits and a period only: `12.50`, not `12,50`, `$12.50` or `-12.50`.

**I want to delete a cost.**
Empty **Merchant-paid shipping cost** and click **Update**. A private note records the removal.

## Invoice import messages

| Message | Fix |
|---|---|
| **Choose a .csv file.** | The file has another extension. Export as CSV. |
| **Files must be 5 MB or smaller.** | Split the file. |
| **This preview is limited to 5,000 data rows.** | Split the file. |
| **This preview supports up to 100 columns per row.** | Remove unused columns, or check the separator. |
| **A CSV field exceeds the 65,536-character preview limit.** | A cell is too long; usually a broken quote. |
| **The CSV has an unclosed quoted field.** | A `"` is not closed. Re-export, or fix the row. |
| **Unexpected text follows a closing quote.** | Text after a closing quote, like `"12"abc`. Re-export. |
| **A quote appears in an invalid position.** | A `"` in the middle of an unquoted value. |
| **Every CSV column needs a non-empty header.** | Name every column in the first row. |
| **Select a CSV with a header row and at least one data row.** | The file is empty or has only headers. |
| **The selected file could not be read.** | The browser could not open the file. Copy it to a local folder and try again. |
| **This profile belongs to a different source format or header layout.** | The saved mapping was made for other column names. Map by hand and save a new profile. |
| **Browser storage is unavailable, so the mapping profile was not saved.** | Private browsing or blocked storage. Profiles need browser local storage. |
| **The import request expired. Reload the page and review the rows again.** | The page was open too long. Reload, re-select the file and match again. |

**Every row says "Duplicate reference in this file".**
Your file has several rows per order (for example one per parcel or per surcharge). Pro saves one cost per order. Add the rows up per order in the spreadsheet first, then import.

**Every row says "No matching shipping order".**
The mapped column is not the WooCommerce order number, or your store uses an order-number plugin with a different format. Map the column that matches what you see in **WooCommerce → Orders**.

## FAQ

**Does it change my shipping rates or order totals?**
No. It only adds cost records and notes.

**Does it work with HPOS?**
Yes. All reads and writes use WooCommerce order APIs and work with both HPOS and legacy storage.

**Is my data sent anywhere?**
The free plugin contacts no external service. Pro contacts ShipStation only after you connect it, and only with the request described in [chapter 7](07-pro-shipstation.md#what-is-sent-to-shipstation).

**Can I record costs per parcel?**
Not in this version; one cost per order. Add parcel costs together.

**Does it convert currencies?**
No, by design. See [rule 3](02-key-ideas.md#rule-3-currency--same-money-only).
