
@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name']) . ' Dashboard | TTC Robotronics - Dispatch ERP')

@section('content')

<div class="container-fluid py-4">

    {{-- Dashboard Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">Dispatch Dashboard</h3>
            <p class="text-muted mb-0">
                Welcome back, {{ $user->first_name ?? 'User' }}!
                Manage your dispatch operations.
            </p>
        </div>

        <a href="{{ route('dispatch.history') }}"
           class="btn btn-primary">
            <i class="fas fa-history me-1"></i>
            Dispatch History
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Total Dispatches</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalDispatches) }}
                            </h3>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-truck text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Dispatch Quantity</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalDispatchQty, 2) }}
                            </h3>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-boxes-stacked text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Total Vehicles</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalVehicles) }}
                            </h3>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-truck-moving text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Completed Production</p>
                            <h3 class="fw-bold mb-0">
                                {{ number_format($totalProduction, 2) }}
                            </h3>
                            <small class="text-muted">KG</small>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-industry text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Dispatches --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Recent Dispatches</h5>

            <a href="{{ route('dispatch.history') }}"
               class="btn btn-sm btn-outline-primary">
                View All
                <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>PO Number</th>
                            <th>Product</th>
                            <th>Vehicle</th>
                            <th>Quantity</th>
                            <th>Dispatched By</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentDispatches as $index => $dispatch)

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($dispatch->updated_at)->format('d-m-Y h:i A') }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $dispatch->company_name }}
                                </td>

                                <td>
                                    {{ $dispatch->customer_po_no ?? '-' }}
                                </td>

                                <td>
                                    {{ $dispatch->product_name ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $dispatch->truckno ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ number_format($dispatch->dispatch_qty, 2) }}
                                    {{ $dispatch->dispatch_qty_unit }}
                                </td>

                                <td>
                                    {{ trim(($dispatch->first_name ?? '') . ' ' . ($dispatch->last_name ?? '')) ?: '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-success">
                                        Dispatched
                                    </span>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="fas fa-truck-loading fs-2 text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">
                                        No dispatch records found.
                                    </p>
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
