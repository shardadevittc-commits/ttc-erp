@extends('layouts.app')

@section('title', 'Gate Dashboard | TTC Robotronics')

@section('content')

@php
    $vehicleInToday = $vehicleInToday ?? 0;
    $weighmentsToday = $weighmentsToday ?? 0;
    $pendingWeighment = $pendingWeighment ?? 0;
    $recentVehicles = $recentVehicles ?? collect();
@endphp

{{-- Dashboard Header --}}
<div class="dashboard-hero-card mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="badge border px-2 py-1 mb-2">
                <i class="fa-solid fa-road me-1"></i>
                GATE & VEHICLE MOVEMENT
            </span>

            <h2 class="hero-title">Gate Control Dashboard 👋</h2>

            <p class="hero-subtitle">
                Monitor vehicle entry, exit, weighment and gate operations.
            </p>
        </div>

        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('material_in') }}"
               class="btn btn-sm btn-danger px-3 py-2">
                <i class="fa-solid fa-plus me-1"></i>
                New Vehicle Entry
            </a>

            <a href="{{ route('gate.index') }}"
               class="btn btn-sm btn-outline-secondary px-3 py-2">
                View Entries
            </a>
        </div>
    </div>
</div>


{{-- KPI Cards --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-blue">
                    <i class="fa-solid fa-truck-moving"></i>
                </div>
            </div>

            <div class="kpi-title">Vehicle In Today</div>

            <div class="kpi-value-row">
                <div class="kpi-value">
                    {{ number_format($vehicleInToday) }}
                </div>
            </div>

            <div class="small text-muted mt-1">
                Total entries recorded today
            </div>
        </div>
    </div>


    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-green">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </div>
            </div>

            <div class="kpi-title">Vehicle Out Today</div>

            <div class="kpi-value-row">
                <div class="kpi-value">—</div>
            </div>

            <div class="small text-muted mt-1">
                Exit data not connected
            </div>
        </div>
    </div>


    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-amber">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
            </div>

            <div class="kpi-title">Vehicles Inside Plant</div>

            <div class="kpi-value-row">
                <div class="kpi-value">—</div>
            </div>

            <div class="small text-muted mt-1">
                Requires vehicle exit status
            </div>
        </div>
    </div>


    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-red">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>

            <div class="kpi-title">Pending Weighment</div>

            <div class="kpi-value-row">
                <div class="kpi-value">
                    {{ number_format($pendingWeighment) }}
                </div>
            </div>

            <div class="small text-muted mt-1">
                Entries without linked weighment records
            </div>
        </div>
    </div>

</div>


{{-- Charts --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-xl-8">
        <div class="chart-box-card">
            <div class="card-header-flex">
                <div>
                    <h3>Vehicle Movement Trend</h3>
                    <p class="text-muted small mb-0">
                        Hourly vehicle entries
                    </p>
                </div>

                <span class="badge border px-2 py-1">
                    {{ now()->format('d M Y') }}
                </span>
            </div>

            <div style="height:290px;position:relative;">
                <canvas id="vehicleMovementChart"></canvas>
            </div>
        </div>
    </div>


    <div class="col-12 col-xl-4">
        <div class="chart-box-card">
            <div class="card-header-flex">
                <div>
                    <h3>Vehicle Type</h3>
                    <p class="text-muted small mb-0">
                        Vehicle category distribution
                    </p>
                </div>
            </div>

            <div style="height:250px;position:relative;">
                <canvas id="vehicleTypeChart"></canvas>
            </div>
        </div>
    </div>

</div>


{{-- Weighbridge and Gate Pass --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-lg-6">
        <div class="chart-box-card h-100">
            <div class="card-header-flex">
                <div>
                    <h3>
                        <i class="fa-solid fa-scale-balanced me-2"></i>
                        Today's Weighbridge
                    </h3>

                    <p class="text-muted small mb-0">
                        Vehicle weighment summary
                    </p>
                </div>

                <span class="badge border px-2 py-1">
                    {{ number_format($weighmentsToday) }} Weighments
                </span>
            </div>

            <div class="row g-3 mt-2">

                <div class="col-6">
                    <div class="p-3 rounded-3"
                         style="background:var(--bg-input);">
                        <div class="small text-muted">Gross Weight</div>
                        <div class="fs-4 fw-bold mt-1">
                            — <span class="fs-6 text-muted">MT</span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 rounded-3"
                         style="background:var(--bg-input);">
                        <div class="small text-muted">Tare Weight</div>
                        <div class="fs-4 fw-bold mt-1">
                            — <span class="fs-6 text-muted">MT</span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 rounded-3"
                         style="background:var(--accent-green-subtle);">
                        <div class="small text-muted">Net Weight</div>
                        <div class="fs-4 fw-bold mt-1"
                             style="color:var(--accent-green);">
                            — <span class="fs-6">MT</span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 rounded-3"
                         style="background:var(--accent-amber-subtle);">
                        <div class="small text-muted">Pending</div>
                        <div class="fs-4 fw-bold mt-1"
                             style="color:var(--accent-amber);">
                            {{ number_format($pendingWeighment) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="col-12 col-lg-6">
        <div class="chart-box-card h-100">
            <div class="card-header-flex">
                <div>
                    <h3>
                        <i class="fa-solid fa-id-card me-2"></i>
                        Gate Pass Summary
                    </h3>

                    <p class="text-muted small mb-0">
                        Gate pass status overview
                    </p>
                </div>
            </div>

            <div class="row g-3 mt-2">

                @foreach([
                    'Issued',
                    'Used',
                    'Pending',
                    'Expired'
                ] as $status)

                    <div class="col-6 col-md-3">
                        <div class="text-center p-3 rounded-3"
                             style="background:var(--bg-input);">
                            <div class="fs-4 fw-bold">—</div>
                            <div class="small text-muted">
                                {{ $status }}
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>

            <p class="small text-muted mt-3 mb-0">
                Connect the gate pass table to display actual counts.
            </p>
        </div>
    </div>

</div>


{{-- Recent Vehicle Entries --}}
<div class="plant-table-card mb-4">

    <div class="card-header-flex p-3 px-4 border-bottom mb-0"
         style="border-color:var(--border-color)!important;">

        <div>
            <h3 class="fs-6 m-0 fw-bold">
                <i class="fa-solid fa-list-check me-2"></i>
                Recent Gate Transactions
            </h3>

            <p class="text-muted small mb-0">
                Latest recorded vehicle entries
            </p>
        </div>

        <a href="{{ route('gate.index') }}"
           class="btn btn-sm btn-outline-secondary">
            View All
            <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>

    </div>

    <div class="table-responsive">
        <table class="table table-custom">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Time</th>
                    <th>Vehicle</th>
                    <th>Movement</th>
                    <th>Party</th>
                    <th>Form List</th>
                    <th>Gate</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($recentVehicles as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $item->created_at?->format('h:i A') ?? '-' }}
                        </td>

                        <td class="fw-bold">
                            {{ $item->truckno ?? '-' }}
                        </td>

                        <td>
                            <span class="badge-status"
                                  style="background:var(--accent-blue-subtle);color:var(--accent-blue);">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                IN
                            </span>
                        </td>

                        <td>
                            {{ $item->customer?->company_name ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $item->formlist ?? '-' }}
                        </td>

                        <td>—</td>

                        <td>
                            <span class="badge-status badge-status-verified">
                                <i class="fa-solid fa-circle-check"></i>
                                Entry Recorded
                            </span>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fa-solid fa-inbox fa-2x text-muted mb-2"></i>
                            <p class="mb-0">
                                No vehicle entries found.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    if (typeof Chart === 'undefined') {
        console.warn('Chart.js is not loaded.');
        return;
    }

    const isDark =
        document.documentElement.getAttribute('data-theme') === 'dark';

    const gridColor = isDark
        ? 'rgba(255,255,255,.05)'
        : 'rgba(11,18,32,.06)';

    const labelColor = isDark
        ? '#8493A8'
        : '#64748B';

    const hourlyEntries = {{ Js::from($hourlyVehicleEntries ?? [0, 0, 0, 0, 0, 0, 0, 0]) }};

    const vehicleTypes = {{ Js::from($vehicleTypeCounts ?? [0, 0, 0, 0]) }};


    // Vehicle Movement Chart
    const movementCanvas =
        document.getElementById('vehicleMovementChart');

    if (movementCanvas) {
        new Chart(movementCanvas.getContext('2d'), {
            type: 'line',

            data: {
                labels: [
                    '9 AM', '10 AM', '11 AM', '12 PM',
                    '1 PM', '2 PM', '3 PM', '4 PM'
                ],

                datasets: [{
                    label: 'Vehicle IN',
                    data: hourlyEntries,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59,130,246,.10)',
                    borderWidth: 2.5,
                    tension: .4,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3B82F6'
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: labelColor,
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    }
                },

                scales: {
                    x: {
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        },
                        ticks: {
                            color: labelColor
                        }
                    },

                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: labelColor
                        },
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }


    // Vehicle Type Doughnut Chart
    const typeCanvas =
        document.getElementById('vehicleTypeChart');

    if (typeCanvas) {
        new Chart(typeCanvas.getContext('2d'), {
            type: 'doughnut',

            data: {
                labels: ['Truck', 'Trailer', 'Tanker', 'Other'],

                datasets: [{
                    data: vehicleTypes,
                    backgroundColor: [
                        '#3B82F6',
                        '#E52D3F',
                        '#10B981',
                        '#F59E0B'
                    ],
                    borderColor: isDark ? '#10192A' : '#FFFFFF',
                    borderWidth: 3
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',

                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: labelColor,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

});
</script>
@endpush
