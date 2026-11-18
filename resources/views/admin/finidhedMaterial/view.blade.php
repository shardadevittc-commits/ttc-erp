{{-- resources/views/admin/finidhedMaterial/view.blade.php --}}
@extends('layouts.app')

@section('title', $productName . ' Finished Material #' . $workOrder->id)

@section('content')
@php
    $sizeLabel = $saleOrderItem->size?->size_name ?? $saleOrderItem->size_unit ?? '-';
    $qtyUnit = $saleOrderItem->qty_type == 1 ? 'KG' : ($saleOrderItem->qty_type == 2 ? 'Ton' : '');
@endphp

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
        <div>
            <h4 class="mb-1">{{ $productName }} - Finished Material</h4>
            <div class="text-muted">Work Order #{{ $workOrder->id }} / {{ _dt($workOrder->created_at) }}</div>
        </div>
        <a href="{{ route('finishedMaterial') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    {{-- Order details --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-semibold border-bottom pb-2 mb-3">Order Details</h6>
            <div class="row g-3">
                <div class="col-md-3"><div class="text-muted small">Party Name</div>{{ $saleOrderItem->saleOrder?->customer?->company_name ?? '-' }}</div>
                <div class="col-md-3"><div class="text-muted small">Sale Order</div><strong class="text-danger">{{ $saleOrderItem->saleOrder?->id ?? '-' }}</strong></div>
                <div class="col-md-3"><div class="text-muted small">Customer PO No</div>{{ $saleOrderItem->saleOrder?->customer_po_no ?: '-' }}</div>
                <div class="col-md-3"><div class="text-muted small">Product</div>{{ $saleOrderItem->product?->product_name ?? $productName }}</div>
                <div class="col-md-3"><div class="text-muted small">Grade</div>{{ $saleOrderItem->grade?->grade_name ?? '-' }}</div>
                <div class="col-md-3"><div class="text-muted small">Size</div>{{ $sizeLabel }}</div>
                <div class="col-md-3"><div class="text-muted small">Rate</div>{{ $saleOrderItem->sale_item_prices ?? '-' }}</div>
                <div class="col-md-3"><div class="text-muted small">Order Qty</div>{{ _rz($saleOrderItem->item_qty) }} {{ $qtyUnit }}</div>
                <div class="col-md-3"><div class="text-muted small">Heat No</div>{{ $workOrder->heat_no ?: '-' }}</div>
                <div class="col-md-3"><div class="text-muted small">Vehicle</div>{{ $workOrder->vehicle_id ?: '-' }}</div>
                <div class="col-md-6"><div class="text-muted small">Remarks</div>{{ $workOrder->j_messages ?: '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Finished summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Sent for Work (Kg)</div>
                <div class="fs-5 fw-semibold">{{ number_format($summary['sent_kg'], 3) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Scrap (Kg)</div>
                <div class="fs-5 fw-semibold text-danger">{{ number_format($summary['scrap_kg'], 3) }}</div>
                <div class="small text-muted">
                    Recoverable {{ number_format($recoverableScrapKg, 3) }} |
                    Non-recoverable {{ number_format($nonRecoverableScrapKg, 3) }} |
                    Adjusted {{ number_format($adjustedScrapKg, 3) }}
                </div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Finished (Kg)</div>
                <div class="fs-5 fw-semibold text-success">{{ number_format($summary['finished_kg'], 3) }}</div>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Packing</div>
                <div class="fs-6 fw-semibold">{{ $summary['packing'] }}</div>
            </div></div>
        </div>
    </div>

    {{-- Operations --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-semibold border-bottom pb-2 mb-3">Production Operations</h6>

            @forelse ($operations as $operation)
                <div class="border rounded p-3 mb-3">
                    <div class="fw-semibold mb-2">
                        <span class="badge bg-success me-2">{{ $loop->iteration }}</span>{{ $operation['title'] }}
                    </div>
                    <div class="row g-2">
                        @foreach ($operation['fields'] as $label => $value)
                            <div class="col-md-3">
                                <div class="text-muted small">{{ $label }}</div>
                                {{ $value }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-muted">No operation details available.</div>
            @endforelse
        </div>
    </div>

    {{-- Scrap entries --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="fw-semibold border-bottom pb-2 mb-3">Scrap Entries</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Stage</th>
                            <th>Type</th>
                            <th class="text-end">Loss (Kg)</th>
                            <th class="text-end">Adjusted (Kg)</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($losses as $loss)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-capitalize">{{ str_replace('_', ' ', $loss->stage_name ?? '-') }}</td>
                                <td>{{ (int) $loss->scrap_type === 1 ? 'Recoverable' : ((int) $loss->scrap_type === 2 ? 'Non-recoverable' : '-') }}</td>
                                <td class="text-end">{{ number_format((float) $loss->loss_weight, 3) }}</td>
                                <td class="text-end">{{ number_format((float) ($loss->adjustable_material ?? 0), 3) }}</td>
                                <td>{{ _dt($loss->created_at) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">No scrap entries.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection