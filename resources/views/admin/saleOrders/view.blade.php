@extends('layouts.app')

@section('title', 'Sale Order #' . $saleOrder->id)

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Sale Order #{{ $saleOrder->id }}</h6>
            </div>
        </div>
        <div class="page-header__actions d-flex gap-2">
            <a href="{{ route('saleOrders.edit', ['id' => $saleOrder->id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('saleOrders') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Order Details</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><strong>Customer:</strong> {{ $saleOrder->customer?->company_name ?? '-' }}</div>
                    <div class="col-md-4"><strong>Customer P.O. No.:</strong> {{ $saleOrder->customer_po_no ?: '-' }}</div>
                    <div class="col-md-4"><strong>Payment Term:</strong> {{ $saleOrder->payment_term ?: '-' }}</div>
                    <div class="col-md-4"><strong>Freight Basis:</strong> {{ $saleOrder->freight_basis === \App\Models\SaleOrder::FREIGHT_EX ? 'EX' : ($saleOrder->freight_basis === \App\Models\SaleOrder::FREIGHT_FOR ? 'FOR' : '-') }}</div>
                    <div class="col-md-4"><strong>Order Qty:</strong> {{ $saleOrder->order_qty ?? '-' }}</div>
                    <div class="col-md-4"><strong>Basic Rate:</strong> {{ $saleOrder->basic_rate ?? '-' }}</div>
                    <div class="col-md-4"><strong>Dispatch Date:</strong> {{ $saleOrder->dispatch_date ?: '-' }}</div>
                    <div class="col-md-8"><strong>Remarks:</strong> {{ $saleOrder->remarks ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Sale Order Items</h5></div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Grade</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Size / Unit</th>
                            <th>Quantity</th>
                            <th>Size Extra</th>
                            <th>Rate</th>
                            <th>Dispatch Qty</th>
                            <th>Dispatched Qty</th>
                            <th>Pending Qty</th>
                            <th>Completion</th>
                            <th>Straightening</th>
                            <th>Point Discard</th>
                            <th>Phosphating</th>
                            <th>Hardness</th>
                            <th>Hardness Unit</th>
                            <th>Length Unit</th>
                            <th>Coating</th>
                            <th>FS Bundle Weight</th>
                            <th>FS Bundle Unit</th>
                            <th>Product Type ID</th>
                            <th>Item Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($saleOrder->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product?->product_name ?? '-' }}</td>
                                <td>{{ $item->product?->product_category ? ucfirst(str_replace('_', ' ', $item->product->product_category)) : '-' }}</td>
                                <td>{{ $item->grade?->grade_name ?? '-' }}</td>
                                <td>{{ $item->brand?->brand_name ?? '-' }}</td>
                                <td>{{ $item->size?->size_name ?? '-' }}</td>
                                <td>{{ $item->size_unit ?: '-' }}</td>
                                <td>{{ $item->item_qty ?? '-' }} {{ $item->qty_type === 1 ? 'Tons' : ($item->qty_type === 2 ? 'KGs' : '') }}</td>
                                <td>{{ $item->size_extra ?? '-' }}</td>
                                <td>{{ $item->sale_item_prices ?? '-' }}</td>
                                <td>{{ $item->dispatch_qty ?? '-' }}</td>
                                <td>{{ $item->dispatched_qty ?? '-' }}</td>
                                <td>{{ $item->pending_qty ?? '-' }}</td>
                                <td>{{ $item->s_marked_completed === 1 ? 'Not Completed' : ($item->s_marked_completed === 2 ? 'Completed' : '-') }}</td>
                                <td>{{ $item->straightening === 1 ? 'Yes' : ($item->straightening === 2 ? 'No' : '-') }}</td>
                                <td>{{ $item->point_discard === 1 ? 'Yes' : ($item->point_discard === 2 ? 'No' : '-') }}</td>
                                <td>{{ $item->phosphating === 1 ? 'Yes' : ($item->phosphating === 2 ? 'No' : '-') }}</td>
                                <td>{{ $item->hardness ?: '-' }}</td>
                                <td>{{ $item->hardness_unit ?: '-' }}</td>
                                <td>{{ $item->pcs_length_unit ?: '-' }}</td>
                                <td>{{ $item->coating ?: '-' }}</td>
                                <td>{{ $item->fs_bundle_weight ?? '-' }}</td>
                                <td>{{ $item->fs_bundle_unit === 1 ? 'KG' : ($item->fs_bundle_unit === 2 ? 'Tons' : '-') }}</td>
                                <td>{{ $item->product_type ?? '-' }}</td>
                                <td>{{ $item->item_remarks ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="25" class="text-center py-3">No items on this sale order.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
