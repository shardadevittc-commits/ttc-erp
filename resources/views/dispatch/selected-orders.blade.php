@extends('layouts.app')

@section('title', 'Selected Sale Orders | TTC Robotronics')

@section('content')

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h5 class="mb-1">Selected Sale Orders</h5>
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

    @forelse($orders as $order)

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <h6 class="mb-0">
                        Sale Order #{{ $order->id }}
                    </h6>

                    <strong class="text-primary">
                        Basic Rate: {{ $order->basic_rate ?? '-' }}
                    </strong>

                </div>
            </div>

            <div class="card-body">

                @if($order->items && $order->items->count())

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>Grade</th>
                                    <th>Size</th>
                                    <th>Item Price</th>
                                    <th>Quantity</th>
                                    <th>Bundle Weight</th>
                                    <th>Bundle Unit</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($order->items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->grade_name ?? '-' }}</td>
                                        <td>{{ $item->size_name ?? '-' }}</td>
                                        <td>{{ $item->sale_item_prices ?? '-' }}</td>
                                        <td>{{ $item->item_qty ?? '-' }}</td>
                                        <td>{{ $item->fs_bundle_weight ?? '-' }}</td>
                                        <td>{{ $item->fs_bundle_unit_name ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else
                    <p class="text-muted mb-0">No items found for this order.</p>
                @endif

            </div>
        </div>

    @empty

        <div class="alert alert-info">
            No Sale Orders selected.
        </div>

    @endforelse

</div>

@endsection
