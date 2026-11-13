@extends('layouts.app')

@section('title', 'Dispatch Sale Orders | TTC Robotronics')

@section('content')

<div class="container-fluid py-3">

    <form action="" method="POST">
        @csrf

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h5 class="mb-1">Dispatch Sale Orders</h5>
                <small class="text-muted">
                    Total Selected: {{ $orders->count() }}
                </small>
            </div>

            <button type="button"
                    class="btn btn-secondary"
                    onclick="history.back()">
                Back
            </button>
        </div>

        {{-- Selected Orders --}}
        @forelse($orders as $order)

            @php
                $totalOrderQty = $order->items->sum('item_qty');
            @endphp

            <div class="card shadow-sm mb-3 order-card"
                 data-order-id="{{ $order->id }}">

                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                        <h6 class="mb-0">
                            Sale Order #{{ $order->id }}
                        </h6>

                        <div class="d-flex flex-wrap gap-3">
                            <strong class="text-primary">
                                Basic Rate: {{ $order->basic_rate ?? '-' }}
                            </strong>

                            <strong class="text-success">
                                Total Dispatch Qty:
                                <span class="total-dispatch-qty">0</span>
                            </strong>

                            <strong class="text-danger">
                                Total Pending Qty:
                                <span class="total-pending-qty">0</span>
                            </strong>
                        </div>

                    </div>
                </div>

                <div class="card-body">

                    {{-- Order ID --}}
                    <input type="hidden"
                           name="order_ids[]"
                           value="{{ $order->id }}">

                    @if($order->items && $order->items->count())

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">

                                <thead class="table-light">
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Grade</th>
                                        <th>Size</th>
                                        <th>Item Price</th>
                                        <th>Order Quantity</th>
                                        <th>Dispatch Quantity</th>
                                        <th>Pending Quantity</th>
                                        <th>Bundle Weight</th>
                                        <th>Bundle Unit</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($order->items as $index => $item)

                                        <tr class="item-row">

                                            <td>{{ $index + 1 }}</td>

                                            <td>
                                                {{ $item->grade->grade_name ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->size->size_name ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->sale_item_prices ?? '-' }}
                                            </td>

                                            {{-- Order Quantity --}}
                                            <td>
                                                <span class="order-qty">
                                                    {{ $item->item_qty ?? 0 }}
                                                </span>
                                            </td>

                                            {{-- Dispatch Quantity --}}
                                            <td>
                                                @if($order->weight_completed)

                                                    <input type="number"
                                                        name="items[{{ $order->id }}][{{ $item->id }}][dispatch_qty]"
                                                        class="form-control form-control-sm dispatch-qty"
                                                        min="0"
                                                        max="{{ $item->item_qty ?? 0 }}"
                                                        step="any"
                                                        value="{{ $item->item_qty ?? 0 }}"
                                                        required>

                                                @else

                                                    <input type="number"
                                                        class="form-control form-control-sm"
                                                        value="0"
                                                        disabled>

                                                    <small class="text-danger fw-bold">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        Weight Pending
                                                    </small>

                                                @endif
                                            </td>

                                            {{-- Pending Quantity --}}
                                            <td>
                                                <strong class="pending-qty text-danger">
                                                    0
                                                </strong>
                                            </td>

                                            <td>
                                                {{ $item->fs_bundle_weight ?? '-' }}
                                            </td>

                                            <td>
                                                {{ $item->fs_bundle_unit == 1 ? 'Tons' : ($item->fs_bundle_unit == 2 ? 'Kgs' : '-') }}
                                            </td>

                                        </tr>

                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                    @else
                        <p class="text-muted mb-0">
                            No items found for this order.
                        </p>
                    @endif

                </div>
            </div>

        @empty

            <div class="alert alert-info">
                No Sale Orders selected.
            </div>

        @endforelse

        @if($orders->count())
            <div class="text-end mb-3">
                <button type="submit" class="btn btn-primary">
                    Save Dispatch
                </button>
            </div>
        @endif

    </form>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    function calculateOrder(orderCard) {

        let totalDispatch = 0;
        let totalPending = 0;

        orderCard.querySelectorAll('.item-row').forEach(function (row) {

            const orderQtyElement = row.querySelector('.order-qty');
            const dispatchInput = row.querySelector('.dispatch-qty');
            const pendingElement = row.querySelector('.pending-qty');

            const orderQty = parseFloat(orderQtyElement.textContent) || 0;
            let dispatchQty = parseFloat(dispatchInput.value) || 0;

            // Prevent dispatch quantity from exceeding order quantity
            if (dispatchQty < 0) {
                dispatchQty = 0;
                dispatchInput.value = 0;
            }

            if (dispatchQty > orderQty) {
                dispatchQty = orderQty;
                dispatchInput.value = orderQty;
            }

            // Pending = Order Quantity - Dispatch Quantity
            const pendingQty = Math.max(0, orderQty - dispatchQty);

            pendingElement.textContent = formatQty(pendingQty);

            totalDispatch += dispatchQty;
            totalPending += pendingQty;
        });

        orderCard.querySelector('.total-dispatch-qty').textContent =
            formatQty(totalDispatch);

        orderCard.querySelector('.total-pending-qty').textContent =
            formatQty(totalPending);
    }

    function formatQty(value) {
        return Number(value.toFixed(4)).toString();
    }

    document.querySelectorAll('.order-card').forEach(function (orderCard) {

        orderCard.querySelectorAll('.dispatch-qty').forEach(function (input) {
            input.addEventListener('input', function () {
                calculateOrder(orderCard);
            });
        });

        // Calculate initial values on page load
        calculateOrder(orderCard);
    });

});
</script>
@endpush
