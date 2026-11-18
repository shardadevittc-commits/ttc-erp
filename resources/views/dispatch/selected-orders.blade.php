@extends('layouts.app')

@section('title', 'Dispatch Sale Orders | TTC Robotronics')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h5 class="mb-1">Dispatch Sale Orders</h5>
            <small class="text-muted">
                Truck {{ $gate->truckno ?: '-' }} · {{ $gate->customer?->company_name ?? '-' }}
                · Total Selected: {{ $orders->count() }}
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dispatch.history') }}" class="btn btn-outline-primary">Dispatch History</a>
            <a href="{{ route('dispatch.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($weight?->second_weight === 'done')
        <div class="alert alert-info">
            <strong>Weight Department Process Completed.</strong>
            Recorded vehicle weights are read-only in Dispatch.
            <div class="mt-1">
                Tare: {{ number_format((float) ($weight->tareweight ?? 0), 2) }} KG
                · Gross: {{ number_format((float) ($weight->unload_loaded_weight ?? 0), 2) }} KG
                · Net: {{ number_format((float) ($weight->material_weight ?? 0), 2) }} KG
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <strong>Vehicle Sent for Weight.</strong>
            Dispatch quantities unlock after the Weight Department completes weighing.
            @if($weight?->tareweight !== null)
                Current recorded tare weight: {{ number_format((float) $weight->tareweight, 2) }} KG
            @endif
        </div>
    @endif

    <form action="{{ route('dispatch.material.store') }}" method="POST">
        @csrf
        <input type="hidden" name="gate_id" value="{{ $gate->id }}">
        @forelse($orders as $order)
            <div class="card shadow-sm mb-3 order-card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0">Sale Order #{{ $order->id }}</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <strong class="text-primary">Basic Rate: {{ $order->basic_rate ?? '-' }}</strong>
                            <strong class="text-success">
                                Dispatch Qty: <span class="total-dispatch-qty">0</span>
                            </strong>
                            <strong class="text-danger">
                                Order Qty Remaining: <span class="total-pending-qty">0</span>
                            </strong>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <input type="hidden" name="order_ids[]" value="{{ $order->id }}">

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Product</th>
                                    <th>Grade</th>
                                    <th>Brand</th>
                                    <th>Size</th>
                                    <th>Rate</th>
                                    <th>Purchase</th>
                                    <th>Order Qty</th>
                                    <th>Production Entries</th>
                                    <th>Produced</th>
                                    <th>Production Remaining</th>
                                    <th>Production Status</th>
                                    <th>Previously Dispatched</th>
                                    <th>Weight Dept. Actual Qty (KG)</th>
                                    <th>Available to Dispatch</th>
                                    <th>Weight / Dispatch Status</th>
                                    <th>Dispatch Quantity</th>
                                    <th>Order Quantity Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $k => $item)
                                    @php
                                        $unit = (int) $item->qty_type === 2 ? 'KG' : 'Tons';
                                        $dispatchField = "items[{$order->id}][{$item->id}][dispatch_qty]";
                                        $dispatchValue = old("items.{$order->id}.{$item->id}.dispatch_qty", '');
                                        $canDispatch = $item->can_dispatch;
                                    @endphp
                                    <tr class="item-row">
                                        <td>#{{ $item->id }}</td>
                                        <td>{{ $item->product?->product_name ?? '-' }}</td>
                                        <td>{{ $item->grade?->grade_name ?? '-' }}</td>
                                        <td>{{ $item->brand?->brand_name ?? '-' }}</td>
                                        <td>{{ $item->size?->size_name ?? $item->size_unit ?? '-' }}</td>
                                        <td>{{ $item->sale_item_prices ?? '-' }}</td>
                                        <td>{{ (int) $item->purchase_entry_count > 0 ? 'Yes (' . $item->purchase_entry_count . ')' : 'No' }}</td>
                                        <td class="order-qty" data-qty="{{ $item->item_qty ?? 0 }}" data-already-dispatched="{{ $item->already_dispatched_qty }}">
                                            {{ $item->item_qty ?? 0 }} {{ $unit }}
                                        </td>
                                        <td>{{ $item->production_entry_count }}</td>
                                        <td>{{ $item->completed_production_qty }} {{ $unit }}</td>
                                        <td>{{ $item->production_remaining_qty }} {{ $unit }}</td>
                                        <td>{{ $item->production_status }}</td>
                                        <td>{{ $item->already_dispatched_qty }} {{ $unit }}</td>
                                        <td>{{ $item->weighed_qty_kg }} KG</td>
                                        <td class="available-qty">{{ $item->dispatch_available_qty }} {{ $unit }}</td>
                                        <td>
                                            @switch($item->dispatch_status)
                                                @case('weight_completed') Weight Department Process Completed @break
                                                @case('dispatched') Final Dispatched @break
                                                @case('sent_for_weight') Vehicle Sent for Weight @break
                                                @default Pending
                                            @endswitch
                                        </td>
                                        <td>
                                            @if($canDispatch)
                                                <input type="text"
                                                    name="{{ $dispatchField }}"
                                                    class="form-control form-control-sm dispatch-qty number_only"
                                                    min="0"
                                                    max="{{ $item->dispatch_available_qty }}"
                                                    step="0.001"
                                                    value="{{ $dispatchValue }}"
                                                    inputmode="decimal">
                                            @else
                                                <input type="text" class="form-control form-control-sm number_only" value="{{ $dispatchRows[$k]->dispatch_qty  }}" disabled>
                                                <small class="text-danger fw-bold">
                                                    {{ $item->dispatch_status === 'dispatched' ? 'Already Dispatched' : ($item->dispatch_status === 'sent_for_weight' ? 'Awaiting Weight Department' : 'Dispatch quantity locked or unavailable') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="pending-qty text-danger">0</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">No production-eligible sale-order items were selected.</div>
        @endforelse

        @if($orders->flatMap(fn ($order) => $order->items)->contains(fn ($item) => $item->can_dispatch))
            <div class="text-end mb-3">
                <button type="submit" class="btn btn-primary">Save Dispatch</button>
            </div>
        @endif
    </form>

    @if($weight?->second_weight !== 'done')
        <div class="alert alert-info">Dispatch quantities remain locked until the Weight Department completes weighing.</div>
    @elseif(!$orders->flatMap(fn ($order) => $order->items)->contains(fn ($item) => $item->can_dispatch))
        <div class="alert alert-info">No remaining dispatch quantities are available for this vehicle.</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.order-card').forEach(function (orderCard) {
        let totalDispatch = 0;
        let totalPending = 0;

        orderCard.querySelectorAll('.item-row').forEach(function (row) {
            const quantityCell = row.querySelector('.order-qty');
            const orderQty = parseFloat(quantityCell.dataset.qty) || 0;
            const alreadyDispatched = parseFloat(quantityCell.dataset.alreadyDispatched) || 0;
            const dispatchInput = row.querySelector('.dispatch-qty');
            let dispatchQty = parseFloat(dispatchInput?.value) || 0;

            if (dispatchInput) {
                const availableQty = parseFloat(dispatchInput.max) || 0;
                dispatchQty = Math.min(Math.max(dispatchQty, 0), availableQty);
                dispatchInput.value = dispatchQty;
                dispatchInput.addEventListener('input', function () {
                    updateTotals(orderCard);
                });
            }

            row.querySelector('.pending-qty').textContent = formatQty(Math.max(0, orderQty - alreadyDispatched - dispatchQty));
            totalDispatch += dispatchQty;
            totalPending += Math.max(0, orderQty - alreadyDispatched - dispatchQty);
        });

        orderCard.querySelector('.total-dispatch-qty').textContent = formatQty(totalDispatch);
        orderCard.querySelector('.total-pending-qty').textContent = formatQty(totalPending);
    });

    function updateTotals(orderCard) {
        let totalDispatch = 0;
        let totalPending = 0;

        orderCard.querySelectorAll('.item-row').forEach(function (row) {
            const quantityCell = row.querySelector('.order-qty');
            const orderQty = parseFloat(quantityCell.dataset.qty) || 0;
            const alreadyDispatched = parseFloat(quantityCell.dataset.alreadyDispatched) || 0;
            const dispatchInput = row.querySelector('.dispatch-qty');
            const availableQty = parseFloat(dispatchInput?.max) || 0;
            const dispatchQty = Math.min(Math.max(parseFloat(dispatchInput?.value) || 0, 0), availableQty);
            if (dispatchInput) dispatchInput.value = dispatchQty;

            const pendingQty = Math.max(0, orderQty - alreadyDispatched - dispatchQty);
            row.querySelector('.pending-qty').textContent = formatQty(pendingQty);
            totalDispatch += dispatchQty;
            totalPending += pendingQty;
        });

        orderCard.querySelector('.total-dispatch-qty').textContent = formatQty(totalDispatch);
        orderCard.querySelector('.total-pending-qty').textContent = formatQty(totalPending);
    }

    function formatQty(value) {
        return Number(value.toFixed(3)).toString();
    }
});
</script>
@endpush
