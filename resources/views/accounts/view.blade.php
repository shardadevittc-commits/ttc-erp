@extends('layouts.app')
@section('title', 'Vehicle Details | Accounts')
@section('content')
@php
    $partyName = $gate->customer?->company_name ?? '-';
    $tareWeight = $weights->max('tareweight') ?? $materials->first()?->tareweight ?? 0;
    $loadedWeight = $weights->max('loadedweight') ?? $weights->max('unload_loaded_weight') ?? $weights->max('material_weight') ?? 0;
    $subtotal = 0;
@endphp
<div class="container-fluid py-3">
    <div class="page-header mb-3">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title"><h6>Vehicle Details</h6></div>
            </div>
            <div class="page-header__actions">
                <a href="{{ route('accounts') }}" class="btn btn-outline-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Vehicle Details (Material In)</div>
                <div class="card-body p-2">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            <tr><th>Token No.</th><td>{{ $gate->tokenno ?: '-' }}</td></tr>
                            <tr><th>Party Name</th><td>{{ $partyName }}</td></tr>
                            <tr><th>Vehicle No.</th><td>{{ $gate->truckno ?: '-' }}</td></tr>
                            <tr><th>Vehicle Type</th><td>{{ $gate->formlist ?: '-' }}</td></tr>
                            <tr><th>Transporter</th><td>{{ $gate->transportname ?: '-' }}</td></tr>
                            <tr><th>Driver</th><td>{{ $gate->drivername ?: '-' }}</td></tr>
                            <tr><th>Mobile No.</th><td>{{ $gate->contactno ?: '-' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Check In / Check Out</div>
                <div class="card-body p-2">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            <tr><th>Check In</th><td>{{ $gate->created_at ? \Illuminate\Support\Carbon::parse($gate->created_at)->format('d-m-Y h:i A') : '-' }}</td></tr>
                            <tr><th>Check Out</th><td>{{ $gate->check_out ? \Illuminate\Support\Carbon::parse($gate->check_out)->format('d-m-Y h:i A') : '-' }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Weight &amp; Material (Dispatch Slip No.: {{ $gate->id }})</strong>
        </div>
        <div class="card-body">
            <div class="mb-2"><strong>Party Name:</strong> {{ $partyName }}</div>
            <div class="mb-3"><strong>Vehicle Tare Weight:</strong> {{ number_format((float) $tareWeight, 3) }} Tons</div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order No.</th>
                            <th>Product</th>
                            <th>Size / Dia</th>
                            <th>Length</th>
                            <th>Grade</th>
                            <th>Brand</th>
                            <th class="text-end">Actual Qty</th>
                            <th class="text-end">Dispatch Qty</th>
                            <th class="text-end">Return Qty</th>
                            <th class="text-end">Net Qty</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Size Extra</th>
                            <th class="text-end">Net Rate</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materials as $dispatch)
                            @php
                                $actual = (float) ($dispatch->actual_qty ?? $dispatch->dispatch_qty ?? 0);
                                $returned = (float) ($dispatch->return_qty ?? 0);
                                $net = max(0, $actual - $returned);
                                $price = (float) ($dispatch->sale_item_prices ?? 0);
                                $extra = (float) ($dispatch->size_extra ?? 0);
                                $netRate = $price + $extra;
                                $lineSubtotal = $net * $netRate;
                                $subtotal += $lineSubtotal;
                            @endphp
                            <tr>
                                <td>{{ $dispatch->sale_order_id }}</td>
                                <td>{{ $dispatch->product_name ?: '-' }}</td>
                                <td>{{ $dispatch->size_name ?: ($dispatch->size_unit ?: '-') }}</td>
                                <td>{{ $dispatch->pcs_length_unit ?: '-' }}</td>
                                <td>{{ $dispatch->grade_name ?: '-' }}</td>
                                <td>{{ $dispatch->brand_name ?: '-' }}</td>
                                <td class="text-end">{{ number_format($actual, 3) }}</td>
                                <td class="text-end">{{ number_format((float) ($dispatch->dispatch_qty ?? 0), 3) }}</td>
                                <td class="text-end">{{ number_format($returned, 3) }}</td>
                                <td class="text-end">{{ number_format($net, 3) }}</td>
                                <td class="text-end">{{ number_format($price, 3) }}</td>
                                <td class="text-end">{{ number_format($extra, 3) }}</td>
                                <td class="text-end">{{ number_format($netRate, 3) }}</td>
                                <td class="text-end">{{ number_format($lineSubtotal, 3) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="14" class="text-center text-muted py-3">No dispatch materials found.</td></tr>
                        @endforelse
                        <tr class="table-light fw-semibold">
                            <td colspan="13" class="text-end">Total</td>
                            <td class="text-end">{{ number_format($subtotal, 3) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3"><strong>Vehicle Loaded Weight:</strong> {{ number_format((float) $loadedWeight, 3) }}</div>
        </div>
    </div>
</div>
@endsection