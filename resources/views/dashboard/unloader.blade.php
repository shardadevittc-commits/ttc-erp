
@extends('layouts.app')

@section('title', 'Unloader Dashboard | TTC Robotronics')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-1">Unloader Dashboard</h3>
            <p class="text-muted mb-0">
                Welcome back, {{ $user->name ?? 'Unloader' }}!
                Here's your unloading overview.
            </p>
        </div>

        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href=""
               class="btn btn-primary">
                <i class="fas fa-history me-1"></i>
                View All History
            </a>
        </div>
    </div>


    {{-- Dashboard Cards --}}
    <div class="row g-3 mb-4">

        {{-- Total Vehicles --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">
                                Total Vehicles
                            </p>

                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalVehicles) }}
                            </h3>

                            <small class="text-success">
                                <i class="fas fa-truck"></i>
                                Unloading Records
                            </small>
                        </div>

                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <i class="fas fa-truck fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Total Materials --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">
                                Total Materials
                            </p>

                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalMaterials) }}
                            </h3>

                            <small class="text-info">
                                <i class="fas fa-boxes"></i>
                                Received Entries
                            </small>
                        </div>

                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <i class="fas fa-boxes fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Received Quantity --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">
                                Total Received Qty
                            </p>

                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalReceivedQty, 2) }}
                            </h3>

                            <small class="text-success">
                                <i class="fas fa-weight"></i>
                                Material Quantity
                            </small>
                        </div>

                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <i class="fas fa-weight-hanging fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Last Receiving --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">
                                Last Receiving
                            </p>

                            <h5 class="fw-bold mb-1">
                                {{ $lastReceiving
                                    ? \Carbon\Carbon::parse($lastReceiving)->format('d M Y')
                                    : 'N/A' }}
                            </h5>

                            <small class="text-muted">
                                Latest Material Entry
                            </small>
                        </div>

                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <i class="fas fa-calendar-check fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- Recent Unloading History --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-0">
                        Recent Unloading History
                    </h5>

                    <small class="text-muted">
                        Latest 10 vehicle unloading records
                    </small>
                </div>

                <a href=""
                   class="btn btn-sm btn-outline-primary">
                    View All
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>

            </div>
        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Vehicle Number</th>
                            <th>Customer</th>
                            <th>Total Materials</th>
                            <th>Received Qty</th>
                            <th>Last Receiving</th>
                            <th>Vehicle Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentUnloadings as $key => $item)

                            <tr>

                                <td>
                                    {{ $key + 1 }}
                                </td>

                                <td>
                                    <span class="fw-semibold">
                                        {{ $item->truckno ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->customer->company_name ?? 'N/A' }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $item->customer->cust_code ?? '' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-info">
                                        {{ $item->total_materials }}
                                    </span>
                                </td>

                                <td class="fw-semibold">
                                    {{ number_format($item->total_received_qty, 2) }}
                                </td>

                                <td>
                                    {{ $item->last_receiving_date
                                        ? \Carbon\Carbon::parse($item->last_receiving_date)->format('d M Y h:i A')
                                        : 'N/A' }}
                                </td>

                                <td>
                                    {{ $item->created_at
                                        ? $item->created_at->format('d M Y')
                                        : 'N/A' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">

                                    <i class="fas fa-truck-loading fa-3x text-muted mb-3"></i>

                                    <h6 class="text-muted">
                                        No Unloading Records Found
                                    </h6>

                                    <small class="text-muted">
                                        Unloading history will appear here.
                                    </small>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')
@endpush
