@extends('layouts.app')

@section('title', 'Truck Order Details | TTC Robotronics')

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Truck Order Details</h6>
                <small class="text-muted">
                    {{ $gate->truckno ?: '-' }} · {{ $gate->customer?->company_name ?? '-' }}
                </small>
            </div>
        </div>
        <div class="page-header__actions">
            @if($dispatchStatuses->contains('sent_for_weight') || $dispatchStatuses->contains('weight_completed') || $dispatchStatuses->contains('dispatched'))
                <a href="{{ route('dispatch.selected-orders.form', ['gate_id' => $gate->id]) }}" class="btn btn-warning">
                    View Selected Items / Dispatch
                </a>
            @endif
            <a href="{{ route('dispatch.index') }}" class="btn btn-outline-secondary">Back to Dispatch</a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($vehicleStatus === 'Vehicle Sent for Weight')
    <div class="alert alert-info"><strong>{{ $vehicleStatus }}</strong> · Selected items are assigned to the Weight Department.</div>
@elseif($vehicleStatus !== 'Pending')
    <div class="alert alert-warning"><strong>Vehicle Status:</strong> {{ $vehicleStatus }}</div>
@endif

<div class="card mt-3 mb-4">
    <div class="card-header"><h5 class="mb-0">Truck and Company</h5></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><strong>Truck Number:</strong> {{ $gate->truckno ?: '-' }}</div>
            <div class="col-md-3"><strong>Company:</strong> {{ $gate->customer?->company_name ?? '-' }}</div>
            <div class="col-md-3"><strong>Driver:</strong> {{ $gate->drivername ?: '-' }}</div>
            <div class="col-md-3"><strong>Contact:</strong> {{ $gate->contactno ?: '-' }}</div>
            <div class="col-md-3"><strong>Transport:</strong> {{ $gate->transportname ?: '-' }}</div>
            <div class="col-md-3"><strong>Token:</strong> {{ $gate->id }}</div>
            <div class="col-md-3"><strong>Entry Date:</strong> {{ $gate->created_at?->format('d M Y, h:i A') ?? '-' }}</div>
        </div>
    </div>
</div>

@forelse($orders as $order)
    <div class="card mb-4">
        <div class="card-header bg-white">
            <div class="d-flex flex-wrap justify-content-between gap-2">
                <div>
                    <h5 class="mb-1">Sale Order #{{ $order->id }}</h5>
                    <span class="text-muted">Customer PO: {{ $order->customer_po_no ?: '-' }}</span>
                </div>
                <div class="text-md-end">
                    <div><strong>Order Date:</strong> {{ $order->created_at?->format('d M Y') ?? '-' }}</div>
                    <div><strong>Order Qty:</strong> {{ $order->order_qty ?? '-' }}</div>
                    <div><strong>Basic Rate:</strong> {{ $order->basic_rate ?? '-' }}</div>
                    <div><strong>Payment Term:</strong> {{ $order->payment_term ?: '-' }}</div>
                    <div><strong>Dispatch Date:</strong> {{ $order->dispatch_date ?: '-' }}</div>
                    <div><strong>Freight Basis:</strong> {{ $order->freight_basis == 1 ? 'EX' : ($order->freight_basis == 2 ? 'FOR' : '-') }}</div>
                </div>
            </div>
            @if($order->remarks)
                <div class="mt-2"><strong>Order Remarks:</strong> {{ $order->remarks }}</div>
            @endif
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Item 1</th>
                            <th>Product {{-- / Category --}}2</th>
                            <th>Grade 3</th>
                            <th>Brand 4</th>
                            <th>Size / Unit 5</th>
                            <th>Order Quantity 6</th>
                            <th>Size Extra 7</th>
                            <th>Rate 8</th>
                            <th>Purchase 9</th>
                            <th>Production Entries 10</th>
                            <th>Produced 11</th>
                            <th>Production <br> Remaining / Status 12</th>
                            {{-- <th>Production Status 13</th> --}}
                            <th>Previously Dispatched 14</th>
                            <th>This Truck 15</th>
                            <th>Dispatch Status 16</th>
                            <th>Dispatch Available 17</th>
                            <th>{{-- Sale Item  --}}Dispatch Qty <strong>Tons </strong> 18</th>
                            <th>{{-- Sale Item  --}}Pending Qty <strong>Tons</strong> 19</th>
                            <th>Completion 20</th>
                            <th>Bundle 21</th>
                            {{-- <th>Processing Details</th> --}}
                            <th>Item Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                {{-- 1 --}}<td>{{ $loop->iteration }} <small class="text-muted">(#{{ $item->id }})</small></td>
                                {{-- 2 --}}<td>
                                    {{ $item->product?->product_name ?? '-' }}
                                    {{-- <small class="d-block text-muted">{{ $item->product?->product_category ?? '-' }}</small> --}}
                                {{-- 3 --}}</td>
                                {{-- 4 --}}<td>{{ $item->grade?->grade_name ?? '-' }}</td>
                                {{-- 5 --}}<td>{{ $item->brand?->brand_name ?? '-' }}</td>
                                {{-- 6 --}}<td>{{ $item->size?->size_name ?? $item->size_unit ?? '-' }}</td>
                                {{-- 7 --}}<td>{{ _rz($item->item_qty) ?? 0 }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }}</td>
                                {{-- 8 --}}<td>{{ $item->size_extra ?? '-' }}</td>
                                {{-- 9 --}}<td>{{ _rz($item->sale_item_prices) ?? '-' }}</td>
                                {{-- 10 --}}<td>
                                    {{ (int) $item->purchase_entry_count > 0 ? 'Yes' : 'No' }}
                                    @if((int) $item->purchase_entry_count > 0)
                                        <small class="d-block text-muted">
                                            {{ $item->purchase_entry_count }} PO item(s)
                                        </small>
                                    @endif
                                </td>
                                {{-- 11 --}}<td>{{ $item->production_entry_count }}</td>
                                {{-- 12 --}}<td>{{ $item->completed_production_qty }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }} </td>
                                {{-- 13 --}}<td>{{ $item->production_remaining_qty }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }} / {{$item->production_status}}</td>
                                {{-- 14<td>{{ $item->production_status }}</td> --}}
                                {{-- 15 --}}<td>{{ $item->already_dispatched_qty }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }}</td>
                                {{-- 16 --}}<td>{{ $item->truck_dispatched_qty }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }}</td>
                                {{-- 17 --}}<td>
                                    @php($itemDispatchStatus = $dispatchStatuses->get($item->id))
                                    @switch($itemDispatchStatus)
                                        @case('sent_for_weight') Vehicle Sent for Weight @break
                                        @case('weight_completed') Weight Department Process Completed @break
                                        @case('dispatched') Final Dispatched @break
                                        @default Pending
                                    @endswitch
                                </td>
                                {{-- 18 --}}<td>{{ _rz($item->dispatchable_qty) }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }}</td>
                                {{-- 19 --}}<td>{{ _rz($item->dispatch_qty) ?? '-' }}</td>
                                {{-- 20 --}}<td>{{ _rz($item->pending_qty) ?? '-' }}</td>
                                {{-- 21 --}}<td>
                                    {{ $item->s_marked_completed == 1 ? 'Not Completed' : ($item->s_marked_completed == 2 ? 'Completed' : '-') }}
                                </td>
                                {{-- 22 --}}<td>
                                    {{ _rz($item->fs_bundle_weight) ?? '-' }}
                                    {{ $item->fs_bundle_unit == 1 ? 'KG' : ($item->fs_bundle_unit == 2 ? 'Tons' : '') }}
                                </td>
                                {{-- <td>
                                    Straightening: {{ $item->straightening == 1 ? 'Yes' : ($item->straightening == 2 ? 'No' : '-') }}<br>
                                    Point discard: {{ $item->point_discard == 1 ? 'Yes' : ($item->point_discard == 2 ? 'No' : '-') }}<br>
                                    Phosphating: {{ $item->phosphating == 1 ? 'Yes' : ($item->phosphating == 2 ? 'No' : '-') }}<br>
                                    Hardness: {{ $item->hardness ?: '-' }} {{ $item->hardness_unit ?: '' }}<br>
                                    Length unit: {{ $item->pcs_length_unit ?: '-' }}<br>
                                    Coating: {{ $item->coating ?: '-' }}<br>
                                    Product type: {{ [1 => 'Bright Bar', 2 => 'HB Wire', 3 => 'HHB Wire', 4 => 'Annealed Bright Bar', 5 => 'Bolt Rods'][$item->product_type] ?? '-' }}
                                </td> --}}
                                <td>{{ $item->item_remarks ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach($order->items as $item)
                <details class="mt-3">
                    <summary class="fw-semibold">
                        Production history for item #{{ $item->id }}
                        ({{ $item->productionHistory->count() }} work order(s))
                    </summary>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Work Order</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Material Sent for Work (Tons)</th>
                                    <th>Completed Output</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->productionHistory as $workOrder)
                                    <tr>
                                        <td>#{{ $workOrder->id }}</td>
                                        <td>{{ $workOrder->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                        <td>{{ str_replace('_', ' ', ucfirst($workOrder->status ?: 'pending')) }}</td>
                                        <td>{{ $workOrder->material_send_for_work ?? 0 }}</td>
                                        <td>{{ $workOrder->completed_output_qty }} {{ (int) $item->qty_type === 2 ? 'KG' : 'Tons' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
@empty
    <div class="alert alert-info">No sale-order items have a production entry for this company.</div>
@endforelse
@endsection
