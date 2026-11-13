@php
    $inlineCard = $inlineCard ?? false;
    $cardId = $cardId ?? 'rm-unload-card-' . $item->id;
    $purchaseOrderItem = $item->purchaseOrderItem;
    $saleOrder = $purchaseOrderItem?->saleOrder ?? $purchaseOrderItem?->purchaseOrder?->saleOrder;
    $saleCustomer = $saleOrder?->customer->toArray();
    $linkedSaleOrderId = $purchaseOrderItem?->ref_saleorder_id ?: $purchaseOrderItem?->purchaseOrder?->ref_saleorder_id;
    $finishedSaleOrderItems = $saleOrder?->items ?? collect();
    $rawMaterialGradeId = $item->grade_id ?? $purchaseOrderItem?->grade_id;

    $finishedSaleOrderItems = $rawMaterialGradeId ? $finishedSaleOrderItems->where('grade_id', $rawMaterialGradeId)->values() : collect();

    $finishedMaterialBrands = $finishedSaleOrderItems
        ->map(fn ($saleOrderItem) => $saleOrderItem->product?->product_name)
        ->filter()
        ->unique()
        ->values();
    $cardUrl = route('unloader.vehicleHistoryView', $vehicle->id) . '#' . $cardId;
@endphp

<article id="{{ $cardId }}" class="rm-print-card" aria-label="RM unload card {{ $cardNumber }}">
    <div class="rm-print-card-actions">
        @unless($inlineCard)
            <span class="rm-card-record">Received material {{ $cardNumber }}</span>
        @endunless
        <button type="button" class="btn btn-primary btn-sm" data-print-card="{{ $cardId }}">
            <i class="fa-solid fa-print me-1" aria-hidden="true"></i>
            {{ $inlineCard ? 'Print' : 'Print Card' }}
        </button>
    </div>

    <div class="rm-card-sheet">
        <header class="rm-card-header">
            <div class="rm-card-heading">
                <span class="rm-card-title">WORK ORDER</span>
                <span class="rm-card-po">
                    PO No:
                    {{ $purchaseOrderItem?->purchaseOrder?->id ? '#' . $purchaseOrderItem->purchaseOrder->id : '—' }}
                </span>
            </div>
            <div class="rm-card-tag">RM UNLOAD TAG</div>
        </header>

        <div class="rm-card-identity">
            <div class="rm-card-company">
                <img class="rm-card-logo" src="{{ asset('assets/images/ttc-logo.png') }}" alt="TTC Robotronics Private Limited">
                <span class="rm-card-company-name">TTC Robotronics Private Limited</span>
            </div>

            <div class="rm-card-qr-wrap">
                <div class="rm-card-qr" data-qr-value="{{ $cardUrl }}" role="img" aria-label="QR code for received material {{ $cardNumber }}">
                    <span class="rm-qr-status">Preparing QR code</span>
                </div>
            </div>

            <div class="rm-card-meta">
                <div class="rm-meta-field">
                    <span class="rm-field-label">ISSUE DATE</span>
                    <strong>
                        {{ $item->created_at?->format('d-m-y') ?? '—' }}<br>
                        {{ $item->created_at?->format('H:i') ?? '' }}
                    </strong>
                </div>
                <div class="rm-meta-field">
                    <span class="rm-field-label">S. CODE</span>
                    <strong>{{ $item->customer?->cust_code ?? $vehicle->customer?->cust_code ?? '—' }}</strong>
                </div>
            </div>
        </div>

        <div class="rm-card-material-grid">
            <div class="rm-material-field">
                <span class="rm-field-label">PRODUCT</span>
                <strong>{{ $purchaseOrderItem?->product?->product_name ?? '—' }}</strong>
            </div>
            <div class="rm-material-field">
                <span class="rm-field-label">SIZE</span>
                <strong>{{ $item->size?->size_name ?? $purchaseOrderItem?->size?->size_name ?? '—' }}</strong>
            </div>
            <div class="rm-material-field">
                <span class="rm-field-label">LENGTH</span>
                <strong>{{ $purchaseOrderItem?->length ?: '—' }}</strong>
            </div>
            <div class="rm-material-field">
                <span class="rm-field-label">GRADE</span>
                <strong>{{ $item->grade?->grade_name ?? $purchaseOrderItem?->grade?->grade_name ?? '—' }}</strong>
            </div>
            <div class="rm-material-field">
                <span class="rm-field-label">HEAT NUMBER</span>
                <strong>
                    {{ $item->genrated_heat_no ?? '—' }}
                    @if($item->furnance_heat_no)
                        <small>Furnace: {{ $item->furnance_heat_no }}</small>
                    @endif
                </strong>
            </div>
            <div class="rm-material-field">
                <span class="rm-field-label">QTY (MT)</span>
                <strong>{{ number_format((float) $item->received_qty, 3) }}</strong>
            </div>
        </div>

        <section class="rm-finished-materials" aria-label="Finished material details">
            @if(!$linkedSaleOrderId)
                <p class="rm-finished-empty rm-finished-message">
                    No linked work order or finished material is recorded for this received lot.
                </p>
            @else
                <div class="rm-finished-header">
                    <span><i class="fa-solid fa-cube me-1" aria-hidden="true"></i> FINISHED MATERIAL:</span>
                    <span>B. CODE: {{ isset($saleCustomer) && $saleCustomer ? $saleCustomer['cust_code'] : '—' }}</span>
                </div>

                <div class="rm-finished-table-wrap">
                    <table class="rm-finished-table">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">PRODUCT / SIZE</th>
                                <th scope="col">GRADE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finishedSaleOrderItems as $index => $saleOrderItem)
                                @php
                                    $finishedSize = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '—';
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $saleOrderItem->product?->product_name ?? '—' }} / {{ $finishedSize }}
                                    </td>
                                    <td>{{ $saleOrderItem->grade?->grade_name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="rm-finished-empty">
                                        No finished material details are available for this linked Sale Order.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
</article>
