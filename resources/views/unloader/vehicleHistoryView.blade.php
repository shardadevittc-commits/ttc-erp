
@extends('layouts.app')

@section('content')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/unloader-print-card.css') }}">
@endpush

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Vehicle Material History Details</h4>
            <p class="text-muted mb-0">
                Complete material receiving history
            </p>
        </div>

        <a href="{{ route('unloader.vehicleHistory') }}"
           class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to History
        </a>
    </div>


    {{-- Vehicle Information --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">Vehicle Information</h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Vehicle Number</label>
                    <h5>{{ $vehicle->truckno ?? '---' }}</h5>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Customer Name</label>
                    <h5>{{ $vehicle->customer->company_name ?? '---' }}</h5>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Customer Code</label>
                    <h5>{{ $vehicle->customer->cust_code ?? '---' }}</h5>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="text-muted">Vehicle Entry Date</label>
                    <h5>
                        {{ $vehicle->created_at
                            ? $vehicle->created_at->format('d-m-Y')
                            : '---' }}
                    </h5>
                </div>

            </div>

        </div>
    </div>


    {{-- Summary Cards --}}
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <label class="text-muted">Total Material Entries</label>
                    <h3 class="mb-0">
                        {{ $receivedItems->count() }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <label class="text-muted">Total Received Quantity</label>
                    <h3 class="mb-0">
                        {{ number_format($receivedItems->sum('received_qty'), 2) }}
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <label class="text-muted">Total PO Quantity</label>
                    <h3 class="mb-0">
                        {{ number_format($receivedItems->sum('po_item_qty'), 2) }}
                    </h3>
                </div>
            </div>
        </div>

    </div>


    {{-- Material Details --}}
    <div class="card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Material Receiving Details</h5>

            <span class="badge badge-primary">
                {{ $receivedItems->count() }} Records
            </span>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">

                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Grade</th>
                            <th>Size</th>
                            <th>PO Qty</th>
                            <th>Received Qty</th>
                            <th>PO Rate</th>
                            <th>Received Rate</th>
                            <th>Furnace Heat No.</th>
                            <th>Generated Heat No.</th>
                            <th>Lab Test</th>
                            <th>Remarks</th>
                            <th>Receiving Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($receivedItems as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>
                                        {{ $item->purchaseOrderItem->product->product_name ?? '---' }}
                                    </strong>
                                </td>
                                <td>{{ $item->grade->grade_name ?? '---' }}</td>
                                <td>{{ $item->size->size_name ?? '---' }}</td>
                                <td>{{ number_format((float) $item->po_item_qty, 2) }}</td>
                                <td>
                                    <strong>
                                        {{ number_format((float) $item->received_qty, 2) }}
                                    </strong>
                                </td>

                                <td>{{ number_format((float) $item->order_price, 2) }}</td>
                                <td>{{ number_format((float) $item->received_price, 2) }}</td>
                                <td>{{ $item->furnance_heat_no ?? '---' }}</td>
                                <td>{{ $item->genrated_heat_no ?? '---' }}</td>
                                <td>{{ $item->lab_test ?? '---' }}</td>
                                <td>{{ $item->item_remarks ?? '---' }}</td>
                                <td>
                                    {{ $item->created_at ? _dt($item->created_at) : '---' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="13" class="text-center">
                                    No material receiving records found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                    @if($receivedItems->isNotEmpty())
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Total
                                </th>

                                <th>
                                    {{ number_format($receivedItems->sum('po_item_qty'), 2) }}
                                </th>

                                <th>
                                    {{ number_format($receivedItems->sum('received_qty'), 2) }}
                                </th>

                                <th colspan="7"></th>
                            </tr>
                        </tfoot>
                    @endif

                </table>

            </div>

        </div>
    </div>

    <section class="rm-print-cards-section mt-4" aria-labelledby="rm-print-cards-heading">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 id="rm-print-cards-heading" class="mb-1">RM Unload Print Cards</h5>
                <p class="text-muted mb-0">Print an individual card for each received material.</p>
            </div>
        </div>

        @forelse($receivedItems as $key => $item)
            @include('unloader.partials.print-card', [
                'vehicle' => $vehicle,
                'item' => $item,
                'cardNumber' => $key + 1,
            ])
        @empty
            <div class="alert alert-light border mb-0">
                Print cards will appear when this vehicle has received material records.
            </div>
        @endforelse
    </section>

</div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="{{ asset('js/unloader-print-card.js') }}"></script>
@endpush
