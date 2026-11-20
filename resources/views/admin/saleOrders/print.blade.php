
<!DOCTYPE html>
<html>
<head>
    <title>Sale Order #{{ $listing->id }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .slip-container {
            width: 100%;
            padding: 10px;
        }

        /* COMPANY HEADER */

        .slip-header {
            position: relative;
            color: #10233f;
            padding-top: 25px;
            margin-bottom: 15px;
        }

        .slip-tax-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 25px;
        }

        .slip-top-lines {
            height: 28px;
            border-top: 2px solid #f79400;
            margin-bottom: 29px;
            position: relative;
        }

        .slip-top-lines::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: 5px;
            height: 20px;
            background: #064b43;
        }

        .slip-logo {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 1px solid #25364d;
            border-radius: 50%;
            background: #fff;
            top: 62px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .slip-logo img {
            width: 90%;
            height: 90%;
            object-fit: contain;
        }

        .slip-header-content {
            display: grid;
            grid-template-columns: 30% 40% 30%;
            align-items: center;
            min-height: 78px;
        }

        .slip-header-left {
            text-align: center;
            font-size: 11px;
            padding: 5px;
            line-height: 1.6;
        }

        .slip-header-center {
            text-align: center;
            border-left: 2px solid #f79400;
            border-right: 2px solid #f79400;
            min-height: 75px;
            padding: 5px;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .slip-header-center h2 {
            font-size: 17px;
            font-weight: bold;
            color: #064b43;
            margin: 0 0 5px;
        }

        .slip-header-center p {
            font-size: 10px;
            margin: 2px 0;
        }

        .slip-header-right {
            text-align: right;
            font-size: 11px;
            padding: 5px;
            line-height: 2;
        }

        .slip-bottom-lines {
            margin-top: 5px;
            height: 10px;
            border-top: 3px solid #f79400;
            border-bottom: 3px solid #064b43;
        }

        /* TITLE */

        .slip-title {
            text-align: center;
            margin: 25px 0;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* ORDER DETAILS */

        .info-table,
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .info-table th,
        .info-table td,
        .item-table th,
        .item-table td {
            border: 1px solid #000;
            padding: 9px;
            font-size: 12px;
            text-align: left;
        }

        .info-table th {
            width: 25%;
            background: #eee;
        }

        .item-table th {
            background: #eee;
            text-align: center;
        }

        .item-table td {
            text-align: center;
        }

        .total-section {
            text-align: right;
            font-size: 14px;
            margin-top: 15px;
            font-weight: bold;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 100px;
        }

        .signature-section div {
            width: 30%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 8px;
            font-size: 12px;
        }

        .no-print {
            margin-bottom: 20px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .slip-header,
            .slip-header *,
            .item-table th {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .slip-top-lines::after {
                background: #064b43 !important;
            }

            .slip-header-center {
                border-left: 2px solid #f79400 !important;
                border-right: 2px solid #f79400 !important;
            }

            .slip-bottom-lines {
                border-top: 3px solid #f79400 !important;
                border-bottom: 3px solid #064b43 !important;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>

<div class="no-print">
    <button onclick="window.print()">
        Print Sale Order
    </button>
</div>

<div class="slip-container">

    {{-- COMPANY HEADER --}}

    <div class="slip-header">

        <div class="slip-tax-row">
            <div>
                <strong>GSTIN:</strong>
                {{ $branchData['gst'] }}
            </div>

            <div>
                <strong>PAN:</strong>
                {{ $branchData['pan'] }}
            </div>
        </div>

        <div class="slip-top-lines"></div>

        <div class="slip-logo">

            @if($branchData['logo'])
                <img src="{{ $branchData['logo'] }}" alt="Company Logo">
            @else
                <strong>
                    {{ substr($branchData['company_name'], 0, 1) }}
                </strong>
            @endif

        </div>

        <div class="slip-header-content">

            <div class="slip-header-left">
                {{ $branchData['address'] }}
            </div>

            <div class="slip-header-center">

                <h2>{{ $branchData['company_name'] }}</h2>

                <p>{{ $branchData['certificate_desc'] }}</p>

            </div>

            <div class="slip-header-right">

                @if($branchData['email'])
                    <div>{{ $branchData['email'] }}</div>
                @endif

                @if($branchData['website'])
                    <div>{{ $branchData['website'] }}</div>
                @endif

                @if($branchData['phone'])
                    <div>{{ $branchData['phone'] }}</div>
                @endif

            </div>

        </div>

        <div class="slip-bottom-lines"></div>

    </div>


    {{-- SALE ORDER TITLE --}}

    <div class="slip-title">
        SALE ORDER
    </div>


    {{-- ORDER INFORMATION --}}

    <table class="info-table">

        <tr>
            <th>Order Number</th>
            <td>#{{ $listing->id }}</td>

            <th>Order Date</th>
            <td>{{ _d($listing->created_at) ?? '-' }}</td>
        </tr>

        <tr>
            <th>Party Name</th>
            <td colspan="3">
                {{ $listing->customer?->company_name ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Deal Quantity (Tons)</th>
            <td>
                {{ _rz($listing->order_qty ?? 0) }}
            </td>

            <th>Total Order Quantity</th>
            <td>
                {{ _rz($listing->total_qty ?? 0) }}
            </td>
        </tr>

    </table>


    {{-- PRODUCT DETAILS --}}

    <h4 style="margin-top:25px;">
        Sale Order Product Details
    </h4>

    <table class="item-table">

        <thead>
            <tr>
                <th>Sr.</th>
                <th>Product</th>
                <th>Size</th>
                <th>Grade</th>
                <th>Sale Rate</th>
                <th>Item Qty (Tons)</th>
            </tr>
        </thead>

        <tbody>

            @forelse($listing->items as $key => $item)

                <tr>

                    <td>{{ $key + 1 }}</td>

                    <td>
                        {{ $item->product?->product_name ?? '-' }}
                    </td>

                    <td>
                        {{ $item->size?->size_name ?? '-' }}
                    </td>

                    <td>
                        {{ $item->grade?->grade_name ?? '-' }}
                    </td>

                    <td>
                        {{ _rz($item->sale_item_prices) }}
                    </td>

                    <td>
                        {{ _rz($item->item_qty) }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6">No products found.</td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- TOTAL --}}

    <div class="total-section">
        Total Quantity:
        {{ _rz($listing->total_qty ?? 0) }} Tons
    </div>


    {{-- SIGNATURES --}}

    {{-- <div class="signature-section">

        <div>
            Prepared By
        </div>

        <div>
            Customer Signature
        </div>

        <div>
            Authorized Signature
        </div>

    </div> --}}

</div>

<script>
    window.addEventListener('load', function () {

        const images = Array.from(document.images);

        Promise.all(
            images.map(img => {

                if (img.complete) {
                    return Promise.resolve();
                }

                return new Promise(resolve => {
                    img.onload = resolve;
                    img.onerror = resolve;
                });

            })
        ).then(() => {
            window.print();
        });

    });
</script>

</body>
</html>
