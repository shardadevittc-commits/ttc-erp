<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    @vite('resources/js/invoice-qr.js')
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 24px;
            background: #f3f4f6;
            color: #252a2f;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .bill-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            width: 100%;
            max-width: 760px;
            margin: 0 auto 12px;
        }

        .bill-actions a,
        .bill-actions button {
            border: 1px solid #29313a;
            border-radius: 4px;
            background: #fff;
            color: #20252b;
            cursor: pointer;
            font-size: 13px;
            padding: 8px 12px;
            text-decoration: none;
        }

        .bill-actions .primary {
            background: #29313a;
            color: #fff;
        }

        .bill {
            position: relative;
            isolation: isolate;
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
            border: 1px solid #111;
            background: #fff;
        }

        .bill-watermark {
            position: absolute;
            z-index: 0;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            overflow: hidden;
            color: #23364a;
            opacity: 0.07;
            pointer-events: none;
            text-align: center;
        }

        .bill-watermark img {
            display: block;
            width: min(42%, 260px);
            height: auto;
            object-fit: contain;
        }

        .bill-watermark span {
            max-width: 90%;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .bill > :not(.bill-watermark) {
            position: relative;
            z-index: 1;
        }

        .bill-header {
            border-bottom: 1px solid #111;
            padding: 8px 12px 10px;
            text-align: center;
        }

        .bill-header h1 {
            margin: 0 0 20px;
            font-size: 24px;
            font-weight: 400;
        }

        .bill-header .company {
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 700;
        }

        .bill-header .gst {
            font-weight: 700;
        }

        .meta {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            border-bottom: 1px solid #111;
        }

        .meta > div {
            min-width: 0;
            padding: 8px;
        }

        .meta > div + div {
            border-left: 1px solid #111;
        }

        .meta-line {
            display: flex;
            gap: 8px;
            margin: 3px 0;
            font-size: 11px;
            line-height: 1.35;
        }

        .meta-label {
            flex: 0 0 auto;
            text-transform: uppercase;
        }

        .remarks {
            min-height: 25px;
            border-bottom: 1px solid #111;
            padding: 5px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items th,
        .items td,
        .average th,
        .average td {
            border: 1px solid #111;
            padding: 5px 6px;
            text-align: left;
            vertical-align: top;
        }

        .items th,
        .average th {
            background: #f1f1f1;
            font-weight: 700;
        }

        .items tbody td {
            height: 150px;
        }

        .items tfoot td {
            height: auto;
            font-weight: 700;
        }

        .text-end {
            text-align: right !important;
            white-space: nowrap;
        }

        .product-subtext {
            display: block;
            margin-top: 3px;
            font-size: 10px;
        }

        .summary-row td {
            border-top: 1px solid #111;
            padding: 5px 7px;
        }

        .summary-row .label {
            text-align: right;
            font-weight: 700;
        }

        .average-title {
            border-top: 1px solid #111;
            padding: 5px 7px;
            font-weight: 700;
        }

        .average-wrap {
            display: grid;
            grid-template-columns: 1fr 155px;
            border-top: 1px solid #111;
        }

        .average-wrap .average {
            align-self: start;
        }

        .labor-total {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 10px;
            border-left: 1px solid #111;
            padding: 8px;
            font-weight: 700;
        }

        .bill-total {
            border-top: 1px solid #111;
            padding: 6px 8px;
            text-align: right;
            font-weight: 700;
        }

        .separate-adjustments {
            border-top: 1px solid #aaa;
            padding: 6px 8px;
            color: #444;
            font-size: 11px;
        }

        .invoice-qr {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 10px 8px;
            border-top: 1px solid #111;
            text-align: center;
        }

        .invoice-qr__code,
        .invoice-qr__code svg {
            display: block;
            width: 108px;
            height: 108px;
        }

        .invoice-qr__label {
            font-size: 10px;
            font-weight: 700;
        }

        .invoice-qr--error .invoice-qr__label {
            color: #b42318;
        }

        .invoice-action-status {
            align-self: center;
            margin-right: auto;
            color: #475569;
            font-size: 11px;
        }

        .invoice-action-status--error {
            color: #b42318;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            body {
                padding: 0;
                background: #fff;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .bill {
                max-width: none;
                margin: 0;
            }

            .bill-watermark {
                opacity: 0.07;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }

        @media screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .bill {
                overflow-x: auto;
            }

            .items {
                min-width: 650px;
            }

            .average-wrap {
                min-width: 540px;
            }
        }
    </style>
</head>
<body>
    @php
        $customerName = $invoice->billed_to ?: ($customer?->company_name ?: '-');
        $customerGst = $customer?->gst_no ?: '-';
        $remarks = $materials->pluck('saleOrder.remarks')->filter()->unique()->implode(' / ');
        $totalQtyTons = $materials->sum(function ($dispatch) {
            $qty = (float) ($dispatch->dispatch_qty ?? 0);

            return (int) $dispatch->saleOrderItem?->qty_type === \App\Models\SaleOrderItem::QTY_KGS
                ? $qty / 1000
                : $qty;
        });
        $materialSubtotal = $materials->sum(function ($dispatch) {
            $qty = (float) ($dispatch->dispatch_qty ?? 0);
            $qtyTons = (int) $dispatch->saleOrderItem?->qty_type === \App\Models\SaleOrderItem::QTY_KGS
                ? $qty / 1000
                : $qty;
            $item = $dispatch->saleOrderItem;
            $rate = (float) ($item?->sale_item_prices ?? 0) + (float) ($item?->size_extra ?? 0);

            return $qtyTons * $rate;
        });
        $productSummaries = $materials->groupBy(function ($dispatch) {
            return $dispatch->saleOrderItem?->product?->product_name ?: 'Other';
        })->map(function ($productMaterials) {
            $qtyTons = $productMaterials->sum(function ($dispatch) {
                $qty = (float) ($dispatch->dispatch_qty ?? 0);

                return (int) $dispatch->saleOrderItem?->qty_type === \App\Models\SaleOrderItem::QTY_KGS
                    ? $qty / 1000
                    : $qty;
            });
            $amount = $productMaterials->sum(function ($dispatch) {
                $qty = (float) ($dispatch->dispatch_qty ?? 0);
                $qtyTons = (int) $dispatch->saleOrderItem?->qty_type === \App\Models\SaleOrderItem::QTY_KGS
                    ? $qty / 1000
                    : $qty;
                $item = $dispatch->saleOrderItem;
                $rate = (float) ($item?->sale_item_prices ?? 0) + (float) ($item?->size_extra ?? 0);

                return $qtyTons * $rate;
            });

            return [
                'qty' => $qtyTons,
                'average_rate' => $qtyTons > 0 ? $amount / $qtyTons : 0,
            ];
        });
        $gstPercent = $invoice->gst_type === 'IGST'
            ? (float) $invoice->igst_percent
            : (float) $invoice->sgst_percent + (float) $invoice->cgst_percent;
    @endphp

    <div class="bill-actions no-print">
        <a href="{{ route('accounts.addInvoice', ['gate' => $gate->id]) }}">Back to Account</a>
        <span class="invoice-action-status" data-invoice-status role="status" aria-live="polite"></span>
        <button type="button" data-invoice-action="print">Print</button>
        <button type="button" class="primary" data-invoice-action="download">Download PDF</button>
    </div>

    <main class="bill" data-invoice-number="{{ $invoice->invoice_no }}">
        <div class="bill-watermark" aria-hidden="true">
            <img src="{{ asset('assets/images/ttc-logo.png') }}" alt="">
            <span>TTC Robotronics Private Limited</span>
        </div>
        <header class="bill-header">
            <h1>INVOICE</h1>
            <div class="company">{{ $customerName }}</div>
            <div class="gst">GST NO. {{ $customerGst }}</div>
        </header>

        <section class="meta">
            <div>
                <div class="meta-line"><span class="meta-label">Billed To:</span><strong>{{ $customerName }}</strong></div>
                <div class="meta-line"><span class="meta-label">GST No.:</span><strong>{{ $customerGst }}</strong></div>
                <div class="meta-line"><span class="meta-label">Address:</span><span>{{ $customer?->address ?: '-' }}</span></div>
            </div>
            <div>
                <div class="meta-line"><span class="meta-label">Invoice No.:</span><strong>{{ $invoice->invoice_no }}</strong></div>
                <div class="meta-line"><span class="meta-label">Slip No.:</span><strong>{{ $gate->id }}</strong></div>
                <div class="meta-line"><span class="meta-label">Date:</span><strong>{{ $invoice->invoice_date?->format('d-m-Y') ?: '-' }}</strong></div>
                <div class="meta-line"><span class="meta-label">Token No.:</span><strong>{{ $gate->tokenno ?: '-' }}</strong></div>
                <div class="meta-line"><span class="meta-label">Vehicle No.:</span><strong>{{ $gate->truckno ?: '-' }}</strong></div>
            </div>
        </section>

        <div class="remarks"><strong>ORDER REMARKS:</strong> {{ $remarks ?: '-' }}</div>

        <table class="items">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>PRODUCT</th>
                    <th class="text-end">QTY. (MT)</th>
                    <th class="text-end">PRICE (₹)</th>
                    <th class="text-end">EXTRA (₹)</th>
                    <th class="text-end">NET AMNT. (₹)</th>
                    <th class="text-end">TOTAL (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materials as $dispatch)
                    @php
                        $item = $dispatch->saleOrderItem;
                        $dispatchQty = (float) ($dispatch->dispatch_qty ?? 0);
                        $qtyTons = (int) $item?->qty_type === \App\Models\SaleOrderItem::QTY_KGS
                            ? $dispatchQty / 1000
                            : $dispatchQty;
                        $price = (float) ($item?->sale_item_prices ?? 0);
                        $extra = (float) ($item?->size_extra ?? 0);
                        $netRate = $price + $extra;
                        $lineTotal = $qtyTons * $netRate;
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $item?->product?->product_name ?: '-' }}
                            @if ($item?->size?->size_name || $item?->grade?->grade_name)
                                <span class="product-subtext">{{ $item?->size?->size_name ?: '' }} {{ $item?->grade?->grade_name ?: '' }}</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($qtyTons, 3) }}</td>
                        <td class="text-end">{{ number_format($price, 2) }}</td>
                        <td class="text-end">{{ number_format($extra, 2) }}</td>
                        <td class="text-end">{{ number_format($netRate, 2) }}</td>
                        <td class="text-end">{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No dispatch materials found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format($totalQtyTons, 3) }}</td>
                    <td colspan="3"></td>
                    <td class="text-end">{{ number_format($materialSubtotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="summary-row">
            <table>
                <tr>
                    <td class="label">CASH DISCOUNT {{ number_format((float) $invoice->cash_discount_percent, 3) }}%</td>
                    <td class="text-end" style="width: 135px">{{ number_format((float) $invoice->cash_discount_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="average-title">AVERAGE SALE RATE (₹):</div>
        <div class="average-wrap">
            <table class="average">
                <thead>
                    <tr><th>PRODUCT</th><th class="text-end">QTY (MT)</th><th class="text-end">AVG RATE</th></tr>
                </thead>
                <tbody>
                    @forelse ($productSummaries as $productName => $summary)
                        <tr>
                            <td>{{ $productName }}</td>
                            <td class="text-end">{{ number_format($summary['qty'], 3) }}</td>
                            <td class="text-end">{{ number_format($summary['average_rate'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">-</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="labor-total">
                <span>LABOR (₹)</span>
                <span>{{ number_format((float) $invoice->labor_charges, 2) }}</span>
            </div>
        </div>

        <table class="summary-row">
            <tbody>
                @if ((float) $invoice->other_charges !== 0.0)
                    <tr><td class="label">OTHER CHARGES (₹)</td><td class="text-end" style="width: 135px">{{ number_format((float) $invoice->other_charges, 2) }}</td></tr>
                @endif
                @if ((float) ($invoice->freight_amount ?? 0) !== 0.0)
                    <tr><td class="label">FREIGHT ({{ (int) ($invoice->freight_type ?? 1) === 2 ? 'FOR' : 'EX' }}) (₹)</td><td class="text-end">{{ number_format((float) $invoice->freight_amount, 2) }}</td></tr>
                @endif
                <tr><td class="label">TAXABLE AMT.</td><td class="text-end">{{ number_format((float) $invoice->taxable_amount, 2) }}</td></tr>
                @if ($invoice->gst_type === 'SGST_CGST')
                    <tr><td class="label">GST {{ number_format($gstPercent, 3) }}%</td><td class="text-end">{{ number_format((float) $invoice->gst_amount, 2) }}</td></tr>
                @elseif ($invoice->gst_type === 'IGST')
                    <tr><td class="label">GST {{ number_format($gstPercent, 3) }}%</td><td class="text-end">{{ number_format((float) $invoice->gst_amount, 2) }}</td></tr>
                @endif
                <tr><td class="label">TCS</td><td class="text-end">{{ number_format((float) $invoice->tcs_amount, 2) }}</td></tr>
                <tr><td class="label">GRAND TOTAL</td><td class="text-end">{{ number_format((float) $invoice->total_amount, 2) }}</td></tr>
            </tbody>
        </table>

        @if ((float) $invoice->tds_amount !== 0.0 || (float) $invoice->insurance_amount !== 0.0 || (float) $invoice->advance_payment !== 0.0 || (int) $invoice->due_days > 0)
            <div class="separate-adjustments">
                <strong>Other invoice details:</strong>
                TDS ₹{{ number_format((float) $invoice->tds_amount, 2) }} |
                Insurance ₹{{ number_format((float) $invoice->insurance_amount, 2) }} |
                Advance ₹{{ number_format((float) $invoice->advance_payment, 2) }} |
                Due days {{ $invoice->due_days }}
            </div>
        @endif
        {!! invoiceQrCode(route('invoices.public.account', ['token' => $invoice->public_token])) !!}
    </main>
</body>
</html>
