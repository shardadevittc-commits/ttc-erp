<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Invoice {{ $dispatch->invoice_no }}</title>
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
                min-width: 540px;
            }

            .average-wrap {
                min-width: 460px;
            }
        }
    </style>
</head>
<body>
    @php
        $customerName = $customer?->company_name ?: '-';
        $customerGst = $customer?->gst_no ?: '-';
        $dispatchCategories = collect($categories)->filter(function ($category, $key) use ($dispatch) {
            return (float) ($dispatch->{$key . '_dispatch'} ?? 0) > 0;
        });
        $totalDispatchKg = $dispatchCategories->keys()->sum(
            fn ($key) => (float) ($dispatch->{$key . '_dispatch'} ?? 0)
        );
        $scrapSubtotal = $dispatchCategories->keys()->sum(
            fn ($key) => (float) ($dispatch->{$key . '_total'} ?? 0)
        );
    @endphp

    <div class="bill-actions no-print">
        <a href="{{ route('scrapDispatch.history') }}">Back to Scrap Dispatch History</a>
        <span class="invoice-action-status" data-invoice-status role="status" aria-live="polite"></span>
        <button type="button" data-invoice-action="print">Print</button>
        <button type="button" class="primary" data-invoice-action="download">Download PDF</button>
    </div>

    <main class="bill" data-invoice-number="{{ $dispatch->invoice_no }}">
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
                <div class="meta-line"><span class="meta-label">Invoice No.:</span><strong>{{ $dispatch->invoice_no ?: '-' }}</strong></div>
                <div class="meta-line"><span class="meta-label">Slip No.:</span><strong>{{ $dispatch->id }}</strong></div>
                <div class="meta-line"><span class="meta-label">Date:</span><strong>{{ $dispatch->invoice_date ? \Illuminate\Support\Carbon::parse($dispatch->invoice_date)->format('d-m-Y') : '-' }}</strong></div>
                <div class="meta-line"><span class="meta-label">Vehicle No.:</span><strong>{{ $vehicle?->truckno ?: '-' }}</strong></div>
                <div class="meta-line"><span class="meta-label">Vehicle Type:</span><strong>{{ $dispatch->vehicle_type ?: '-' }}</strong></div>
            </div>
        </section>

        <div class="remarks"><strong>REMARKS:</strong> {{ $dispatch->remarks ?: '-' }}</div>

        <table class="items">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>SCRAP CATEGORY</th>
                    <th class="text-end">QTY. (KG)</th>
                    <th class="text-end">RATE (₹ / KG)</th>
                    <th class="text-end">TOTAL (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($dispatchCategories as $key => $category)
                    @php
                        $quantity = (float) ($dispatch->{$key . '_dispatch'} ?? 0);
                        $rate = (float) ($dispatch->{$key . '_rate'} ?? 0);
                        $lineTotal = (float) ($dispatch->{$key . '_total'} ?? ($quantity * $rate));
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category['label'] }}<span class="product-subtext">{{ $category['recoverability'] }}</span></td>
                        <td class="text-end">{{ number_format($quantity, 3) }}</td>
                        <td class="text-end">{{ number_format($rate, 2) }}</td>
                        <td class="text-end">{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No scrap quantities were recorded for this dispatch.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format($totalDispatchKg, 3) }}</td>
                    <td></td>
                    <td class="text-end">{{ number_format($scrapSubtotal, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="average-title">WEIGHT DETAILS:</div>
        <div class="average-wrap">
            <table class="average">
                <tbody>
                    <tr><th>FIRST WEIGHT (KG)</th><td class="text-end">{{ number_format((float) ($dispatch->first_weight ?? 0), 3) }}</td></tr>
                    <tr><th>LAST WEIGHT (KG)</th><td class="text-end">{{ number_format((float) ($dispatch->last_weight ?? 0), 3) }}</td></tr>
                    <tr><th>NET WEIGHT (KG)</th><td class="text-end">{{ number_format((float) ($dispatch->actual_weight ?? 0), 3) }}</td></tr>
                </tbody>
            </table>
            <div class="labor-total">
                <span>PAYMENT</span>
                <span>{{ $dispatch->mode_of_payment ?: '-' }}</span>
            </div>
        </div>

        <table class="summary-row">
            <tbody>
                <tr><td class="label">INVOICE AMOUNT</td><td class="text-end" style="width: 135px">{{ number_format((float) ($dispatch->invoice_amt ?? 0), 2) }}</td></tr>
                <tr><td class="label">GST</td><td class="text-end">{{ number_format((float) ($dispatch->invoice_gst ?? 0), 2) }}</td></tr>
                <tr><td class="label">MISC. AMOUNT</td><td class="text-end">{{ number_format((float) ($dispatch->misc_amt ?? 0), 2) }}</td></tr>
                <tr><td class="label">GRAND TOTAL</td><td class="text-end">{{ number_format((float) ($dispatch->final_amt ?? 0), 2) }}</td></tr>
            </tbody>
        </table>
        {!! invoiceQrCode(route('invoices.public.scrap', ['token' => $dispatch->public_token])) !!}
    </main>
</body>
</html>
