@extends(request()->boolean('embedded') ? 'layouts.bundle-stickers-embed' : 'layouts.app')

@section('title', 'Bundle Stickers | Work Order #' . $workOrder->id)

@push('styles')
    <style>
        .sticker-page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .sticker-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 360px), 1fr));
            justify-items: center;
            gap: 1.5rem;
        }

        .sticker-item {
            width: min(100%, 360px);
        }

        .bundle-sticker {
            box-sizing: border-box;
            width: 95mm;
            min-height: 138mm;
            overflow: hidden;
            border: 1px solid #182a38;
            background: #fff;
            color: #20252a;
            font-family: Arial, Helvetica, sans-serif;
            box-shadow: 0 2px 12px rgb(0 0 0 / 14%);
        }

        .sticker-header {
            display: flex;
            align-items: center;
            min-height: 33mm;
            padding: 3mm 5mm;
            background: #064d78;
            color: #fff;
        }

        .sticker-brand-mark {
            display: flex;
            flex: 0 0 21mm;
            width: 21mm;
            height: 21mm;
            overflow: hidden;
            align-items: center;
            justify-content: center;
            margin-right: 4mm;
            padding: 1.5mm;
            border: 1px solid #e6d600;
            border-radius: 50%;
            background: #fff;
            color: #17668d;
            font-size: 15pt;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .sticker-brand-logo {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        .sticker-company {
            flex: 1 1 0;
            min-width: 0;
            overflow: hidden;
            font-size: 8pt;
            font-weight: 700;
            line-height: 1.15;
        }

        .sticker-company-name {
            margin-bottom: 1mm;
            font-size: clamp(7pt, 3vw, 11pt);
            font-weight: 800;
            line-height: 1.05;
            overflow-wrap: anywhere;
        }

        .sticker-company-tagline {
            margin-bottom: 2mm;
            font-size: 5.5pt;
        }

        .sticker-company-address {
            overflow-wrap: anywhere;
        }

        .sticker-yellow-rule {
            height: 2.5mm;
            background: #e6d600;
        }

        .sticker-details {
            padding: 4mm 5mm 1mm;
        }

        .sticker-detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 9.5mm;
            align-items: center;
            border-bottom: 1px solid #d5d5d5;
            font-size: 8pt;
        }

        .sticker-detail-label {
            padding-left: 1mm;
        }

        .sticker-detail-value {
            padding-left: 2mm;
            font-weight: 800;
        }

        .sticker-weight-row {
            background: #faf6d9;
        }

        .sticker-qr-area {
            display: flex;
            height: 27mm;
            align-items: center;
            justify-content: space-between;
            margin: 4mm 5mm 2mm;
            padding: 1mm 2mm;
            border: 1px solid #075b9d;
            border-radius: 1mm;
        }

        .sticker-origin {
            max-width: 48mm;
            color: #111;
            font-size: 13pt;
            font-weight: 900;
            line-height: 1;
            text-align: center;
        }

        .sticker-origin small {
            display: block;
            margin-top: 1.5mm;
            font-size: 6pt;
            letter-spacing: 1px;
        }

        .sticker-qr {
            display: block;
            width: 25mm;
            height: 25mm;
        }

        .sticker-footer {
            padding: 0 3mm 2mm;
            color: #07507d;
            font-size: 7pt;
            font-weight: 700;
            text-align: center;
        }

        .sticker-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .sticker-actions .btn {
            flex: 1;
        }

        .sticker-qr-error {
            display: none;
        }

        @media print {
            @page {
                size: 95mm 138mm;
                margin: 0;
            }

            body {
                margin: 0;
                background: #fff;
            }

            .bundle-sticker {
                width: 95mm;
                height: 138mm;
                min-height: 138mm;
                margin: 0;
                box-shadow: none;
                break-after: page;
                page-break-after: always;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endpush

@section('content')
    <div class="sticker-page-header">
        <div>
            <h1 class="h4 mb-1">Product Bundle Stickers</h1>
            <div class="text-muted">Work Order #{{ $workOrder->id }} · {{ $productName }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('productions.workflow', ['workOrder' => $workOrder->id]) }}"
                class="btn btn-outline-secondary">Back to Work Order</a>
            @if ($stickers)
                <button type="button" class="btn btn-primary" id="printAllStickers" disabled>Print All Stickers</button>
            @endif
        </div>
    </div>
    @if (!$stickers)
        <div class="alert alert-info" role="status">
            No bundle or box details are available for this completed work order.
        </div>
    @else
        <div class="alert alert-danger sticker-qr-error" id="stickerQrError" role="alert"></div>
        <div class="sticker-grid" id="stickerGrid">
            <script type="application/json" id="sticker-company-data">@json($stickerCompany)</script>
            @foreach ($stickers as $sticker)
                <div class="sticker-item">
                    <article class="bundle-sticker">
                        <header class="sticker-header">
                            <div class="sticker-brand-mark">
                                @if(!empty($stickerCompany['logo']))
                                    <img
                                        src="{{ asset('storage/' . $stickerCompany['logo']) }}"
                                        alt="{{ $stickerCompany['company_name'] }} logo"
                                        class="sticker-brand-logo"
                                    >
                                    <span class="sticker-brand-fallback" style="display: none">{{ strtoupper(substr($stickerCompany['company_name'], 0, 2)) }}</span>
                                @else
                                    <span class="sticker-brand-fallback">{{ strtoupper(substr($stickerCompany['company_name'], 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="sticker-company">
                                <div class="sticker-company-name">{{ $stickerCompany['company_name'] }}</div>
                                @if (!empty($stickerCompany['certificate_desc']))
                                    <div class="sticker-company-tagline">{{ $stickerCompany['certificate_desc'] }}</div>
                                @endif
                                @if (!empty($stickerCompany['phone']))
                                    <div>☎ &nbsp;{{ $stickerCompany['phone'] }}</div>
                                @endif
                                @if (!empty($stickerCompany['address']))
                                    <div class="sticker-company-address">• &nbsp;{!! $stickerCompany['address'] !!}</div>
                                @endif
                            </div>
                        </header>
                        <div class="sticker-yellow-rule"></div>
                        <div class="sticker-details">
                            <div class="sticker-detail-row">
                                <span class="sticker-detail-label">PRODUCT</span>
                                <span class="sticker-detail-value">{{ $productName }}</span>
                            </div>
                            <div class="sticker-detail-row">
                                <span class="sticker-detail-label">BUNDLE NO.</span>
                                <span class="sticker-detail-value">{{ $sticker['number'] }}</span>
                            </div>
                            <div class="sticker-detail-row">
                                <span class="sticker-detail-label">HEAT NO.</span>
                                <span class="sticker-detail-value">{{ $workOrder->heat_no ?: '-' }}</span>
                            </div>
                            <div class="sticker-detail-row">
                                <span class="sticker-detail-label">GRADE</span>
                                <span class="sticker-detail-value">{{ $gradeName }}</span>
                            </div>
                            <div class="sticker-detail-row">
                                <span class="sticker-detail-label">SIZE</span>
                                <span class="sticker-detail-value">{{ $sizeLabel }}</span>
                            </div>
                            <div class="sticker-detail-row sticker-weight-row">
                                <span class="sticker-detail-label">WEIGHT</span>
                                <span class="sticker-detail-value">{{ $sticker['weight'] }}</span>
                            </div>
                        </div>
                        <div class="sticker-qr-area">
                            <div class="sticker-origin">
                                MAKE IN INDIA
                                <small>{{ $stickerCompany['company_name'] }}</small>
                            </div>
                            <img class="sticker-qr" alt="Scan to open signed bundle details" width="260" height="260">
                        </div>
                        <footer class="sticker-footer">
                            WO: {{ $workOrder->id }} &nbsp; | &nbsp; Bundle: {{ $sticker['number'] }}
                        </footer>
                        <script type="application/json" class="sticker-qr-payload">@json($sticker['qrPayload'])</script>
                    </article>
                    <div class="sticker-actions">
                        <button type="button" class="btn btn-success print-sticker" disabled>Print Sticker</button>
                        <button type="button" class="btn btn-outline-primary download-sticker" disabled>Download JPEG</button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    @vite('resources/js/bundle-stickers.js')
@endpush
