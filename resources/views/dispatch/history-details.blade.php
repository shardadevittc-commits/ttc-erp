@extends('layouts.app')

@section('title', 'Completed Dispatch Details | TTC Robotronics')

@section('content')
@php
    $gate = $dispatchMaterial->gate;
    $order = $dispatchMaterial->saleOrder;
    $item = $dispatchMaterial->saleOrderItem;
    $weight = $dispatchMaterial->weight;
    $qtyUnit = $dispatchMaterial->dispatch_qty_unit
        ?: ((int) $item?->qty_type === \App\Models\SaleOrderItem::QTY_KGS ? 'KGS' : 'Tons');
@endphp

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h5 class="mb-1">Completed Dispatch #{{ $dispatchMaterial->id }}</h5>
            <small class="text-muted">Read-only dispatch record.</small>
        </div>
        <a href="{{ route('dispatch.history') }}" class="btn btn-secondary">Back to History</a>
    </div>

    <div class="alert alert-success">Final Dispatched · This record is view-only.</div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Vehicle and Weight</h6></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Truck Number</dt><dd class="col-sm-7">{{ $gate?->truckno ?: '-' }}</dd>
                        <dt class="col-sm-5">Company</dt><dd class="col-sm-7">{{ $gate?->customer?->company_name ?: '-' }}</dd>
                        <dt class="col-sm-5">Driver</dt><dd class="col-sm-7">{{ $gate?->drivername ?: '-' }}</dd>
                        <dt class="col-sm-5">Contact</dt><dd class="col-sm-7">{{ $gate?->contactno ?: '-' }}</dd>
                        <dt class="col-sm-5">Transport</dt><dd class="col-sm-7">{{ $gate?->transportname ?: '-' }}</dd>
                        <dt class="col-sm-5">Vehicle Entry</dt>
                        <dd class="col-sm-7">{{ $gate?->created_at?->format('d M Y, h:i A') ?? '-' }}</dd>
                        <dt class="col-sm-5">Tare Weight</dt>
                        <dd class="col-sm-7">{{ number_format((float) ($weight?->tareweight ?? 0), 2) }} KG</dd>
                        <dt class="col-sm-5">Gross Weight</dt>
                        <dd class="col-sm-7">{{ number_format((float) ($weight?->unload_loaded_weight ?? 0), 2) }} KG</dd>
                        <dt class="col-sm-5">Net Weight</dt>
                        <dd class="col-sm-7">{{ number_format((float) ($weight?->material_weight ?? 0), 2) }} KG</dd>
                        <dt class="col-sm-5">Item Actual Weight</dt>
                        <dd class="col-sm-7">{{ number_format((float) ($dispatchMaterial->actual_qty ?? 0), 3) }} KG</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><h6 class="mb-0">Order and Dispatch</h6></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Sale Order</dt><dd class="col-sm-7">#{{ $order?->id ?? $dispatchMaterial->sale_order_id }}</dd>
                        <dt class="col-sm-5">Customer PO</dt><dd class="col-sm-7">{{ $order?->customer_po_no ?: '-' }}</dd>
                        <dt class="col-sm-5">Sale Order Item</dt><dd class="col-sm-7">#{{ $item?->id ?? $dispatchMaterial->sale_order_item_id }}</dd>
                        <dt class="col-sm-5">Material</dt><dd class="col-sm-7">{{ $item?->product?->product_name ?: '-' }}</dd>
                        <dt class="col-sm-5">Grade / Brand</dt>
                        <dd class="col-sm-7">{{ $item?->grade?->grade_name ?: '-' }} / {{ $item?->brand?->brand_name ?: '-' }}</dd>
                        <dt class="col-sm-5">Size</dt>
                        <dd class="col-sm-7">{{ $item?->size?->size_name ?: $item?->size_unit ?: '-' }}</dd>
                        <dt class="col-sm-5">Ordered Quantity</dt>
                        <dd class="col-sm-7">{{ number_format((float) ($item?->item_qty ?? 0), 3) }} {{ $qtyUnit }}</dd>
                        <dt class="col-sm-5">Dispatch Quantity</dt>
                        <dd class="col-sm-7">{{ number_format((float) ($dispatchMaterial->dispatch_qty ?? 0), 3) }} {{ $qtyUnit }}</dd>
                        <dt class="col-sm-5">Dispatch Date</dt>
                        <dd class="col-sm-7">{{ $dispatchMaterial->updated_at?->format('d M Y, h:i A') ?? '-' }}</dd>
                        <dt class="col-sm-5">Dispatch Status</dt>
                        <dd class="col-sm-7"><span class="badge bg-success">Final Dispatched</span></dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header"><h6 class="mb-0">Production Snapshot</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-lg-3"><strong>Bundle Weight:</strong> {{ $dispatchMaterial->fs_bundle_weight ?? '-' }} {{ $dispatchMaterial->fs_bundle_unit ?: '' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Full Bundles:</strong> {{ $dispatchMaterial->total_full_bundle_count ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Loose Bundle Weight:</strong> {{ $dispatchMaterial->lose_bundle_weight ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Loose Bundle Count:</strong> {{ $dispatchMaterial->lose_bundle_count ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Per Piece Weight:</strong> {{ $dispatchMaterial->per_pcs_weight ?? '-' }} KG</div>
                        <div class="col-sm-6 col-lg-3"><strong>Total Pieces:</strong> {{ $dispatchMaterial->total_pcs ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Boxes:</strong> {{ $dispatchMaterial->no_of_boxes ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Loose Box Pieces:</strong> {{ $dispatchMaterial->loss_box_pcs ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Loose Box Count:</strong> {{ $dispatchMaterial->loss_box_count ?? '-' }}</div>
                        <div class="col-sm-6 col-lg-3"><strong>Return Quantity:</strong> {{ $dispatchMaterial->return_qty ?? 0 }} {{ $qtyUnit }}</div>
                        <div class="col-12"><strong>Return Remarks:</strong> {{ $dispatchMaterial->return_remarks ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
