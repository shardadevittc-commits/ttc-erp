@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name']) . ' Dashboard | TTC Robotronics - Steel Industry ERP')

@section('content')

    {{-- =====================================================
        1. WEIGHT DASHBOARD HERO
    ====================================================== --}}
    <div class="dashboard-hero-card mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge border px-2 py-2"
                          style="background:var(--bg-input);color:var(--text-heading);border-color:var(--border-color)!important;">
                        <i class="fa-solid fa-weight-scale me-1"></i>
                        WEIGHMENT MANAGEMENT
                    </span>

                    <span class="badge border px-2 py-2"
                          style="background:var(--accent-green-subtle);color:var(--accent-green);border-color:var(--accent-green-border)!important;">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        WEIGHT SYSTEM ACTIVE
                    </span>
                </div>

                <h2 class="hero-title">
                    Welcome back, {{ auth()->user()->first_name ?? 'Weight Operator' }}! 👋
                </h2>

                <p class="hero-subtitle mb-0">
                    Manage vehicle entries, material inward and outward movements,
                    return vehicles, weighment slips and customer-wise weight records
                    from one centralized dashboard.
                </p>
            </div>

            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('weight.index') }}"
                   class="btn btn-sm px-3 py-2 fw-semibold rounded-3 me-2 text-white"
                   style="background:var(--primary-red);">
                    <i class="fa-solid fa-weight-scale me-1"></i>
                    Weight Records
                </a>

                <a href="{{ route('material_weight') }}"
                   class="btn btn-sm px-3 py-2 fw-semibold rounded-3"
                   style="background:var(--bg-card);color:var(--text-heading);border:1px solid var(--border-color);">
                    <i class="fa-solid fa-plus me-1"></i>
                    New Weighment
                </a>
            </div>
        </div>
    </div>


    {{-- =====================================================
        2. WEIGHT KPI CARDS
    ====================================================== --}}
    <div class="row g-3 mb-4">

        {{-- TOTAL VEHICLES --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-blue">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <span class="kpi-trend-badge trend-neutral">
                        <i class="fa-solid fa-truck"></i> Entries
                    </span>
                </div>

                <div class="kpi-title">Total Vehicle Entries</div>

                <div class="kpi-value-row">
                    <div class="kpi-value">{{ $totalVehicles ?? 0 }}</div>
                    <i class="fa-solid fa-truck-moving fa-2x"
                       style="color:var(--accent-blue);opacity:.6;"></i>
                </div>

                <div class="small text-muted mt-1">
                    Registered vehicle entries
                </div>
            </div>
        </div>

        {{-- MATERIAL IN --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-green">
                        <i class="fa-solid fa-arrow-down-to-line"></i>
                    </div>
                    <span class="kpi-trend-badge trend-up">
                        Inward
                    </span>
                </div>

                <div class="kpi-title">Material In</div>

                <div class="kpi-value-row">
                    <div class="kpi-value">{{ $totalMaterialIn ?? 0 }}</div>
                    <i class="fa-solid fa-dolly fa-2x"
                       style="color:var(--accent-green);opacity:.6;"></i>
                </div>

                <div class="small text-muted mt-1">
                    Material inward weighment records
                </div>
            </div>
        </div>

        {{-- MATERIAL OUT --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-amber">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                    </div>
                    <span class="kpi-trend-badge trend-neutral">
                        Outward
                    </span>
                </div>

                <div class="kpi-title">Material Out</div>

                <div class="kpi-value-row">
                    <div class="kpi-value">{{ $totalMaterialOut ?? 0 }}</div>
                    <i class="fa-solid fa-truck-fast fa-2x"
                       style="color:#F59E0B;opacity:.6;"></i>
                </div>

                <div class="small text-muted mt-1">
                    Material outward weighment records
                </div>
            </div>
        </div>

        {{-- RETURN --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-red">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <span class="kpi-trend-badge trend-neutral">
                        Return
                    </span>
                </div>

                <div class="kpi-title">Return Vehicles</div>

                <div class="kpi-value-row">
                    <div class="kpi-value">{{ $totalReturns ?? 0 }}</div>
                    <i class="fa-solid fa-right-left fa-2x"
                       style="color:var(--primary-red);opacity:.6;"></i>
                </div>

                <div class="small text-muted mt-1">
                    Total return vehicle records
                </div>
            </div>
        </div>

    </div>


    {{-- =====================================================
        3. WEIGHMENT OVERVIEW + QUICK SUMMARY
    ====================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-xl-8">
            <div class="chart-box-card h-100">
                <div class="card-header-flex mb-3">
                    <div>
                        <h3>
                            <i class="fa-solid fa-chart-column me-2 text-muted"></i>
                            Weighment Overview
                        </h3>
                        <p class="text-muted small mb-0">
                            Vehicle movement summary by transaction type
                        </p>
                    </div>

                    <span class="badge border px-2 py-2"
                          style="background:var(--bg-input);color:var(--text-heading);border-color:var(--border-color)!important;">
                        <i class="fa-regular fa-calendar me-1"></i>
                        Overview
                    </span>
                </div>

                <div style="height:290px;position:relative;">
                    <canvas id="weightOverviewChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="chart-box-card h-100">
                <div class="card-header-flex mb-3">
                    <div>
                        <h3>Transaction Distribution</h3>
                        <p class="text-muted small mb-0">
                            Vehicle record categories
                        </p>
                    </div>
                </div>

                <div style="height:220px;position:relative;">
                    <canvas id="weightDistributionChart"></canvas>
                </div>

                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom"
                         style="border-color:var(--border-color)!important;">
                        <span class="small">
                            <i class="fa-solid fa-circle me-2" style="color:#10B981;"></i>
                            Material In
                        </span>
                        <strong>{{ $totalMaterialIn ?? 0 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom"
                         style="border-color:var(--border-color)!important;">
                        <span class="small">
                            <i class="fa-solid fa-circle me-2" style="color:#3B82F6;"></i>
                            Material Out
                        </span>
                        <strong>{{ $totalMaterialOut ?? 0 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="small">
                            <i class="fa-solid fa-circle me-2" style="color:#F59E0B;"></i>
                            Return
                        </span>
                        <strong>{{ $totalReturns ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- =====================================================
        4. WEIGHT MANAGEMENT OPERATIONS
    ====================================================== --}}
    <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="fs-5 m-0 fw-bold">
            <i class="fa-solid fa-gears me-2 text-muted"></i>
            Weight Management Operations
        </h3>

        <span class="small text-muted">
            Vehicle and weighment management
        </span>
    </div>

    <div class="row g-3 mb-4">

        {{-- VEHICLE ENTRY --}}
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('weight.index') }}"
               class="module-tile text-decoration-none h-100">
                <div class="module-icon-box">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div>
                    <h4>Vehicle Entries</h4>
                    <p>
                        View vehicle entry records, truck numbers,
                        customers and entry slips.
                    </p>
                </div>
            </a>
        </div>

        {{-- NEW WEIGHMENT --}}
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('material_weight') }}"
               class="module-tile text-decoration-none h-100">
                <div class="module-icon-box">
                    <i class="fa-solid fa-weight-scale"></i>
                </div>
                <div>
                    <h4>New Weighment</h4>
                    <p>
                        Record RST number, loaded weight, tare weight
                        and upload weighment slips.
                    </p>
                </div>
            </a>
        </div>

        {{-- MATERIAL IN --}}
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('weight.index', ['formlist' => 'Material In']) }}"
               class="module-tile text-decoration-none h-100">
                <div class="module-icon-box">
                    <i class="fa-solid fa-arrow-down-to-line"></i>
                </div>
                <div>
                    <h4>Material In</h4>
                    <p>
                        Manage incoming material, loaded weights
                        and inward vehicle records.
                    </p>
                </div>
            </a>
        </div>

        {{-- MATERIAL OUT --}}
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('weight.index', ['formlist' => 'Material Out']) }}"
               class="module-tile text-decoration-none h-100">
                <div class="module-icon-box">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                </div>
                <div>
                    <h4>Material Out</h4>
                    <p>
                        Manage outgoing material, tare weights
                        and dispatch-related weighment records.
                    </p>
                </div>
            </a>
        </div>

        {{-- RETURN --}}
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('weight.index', ['formlist' => 'Return']) }}"
               class="module-tile text-decoration-none h-100">
                <div class="module-icon-box">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h4>Return Vehicles</h4>
                    <p>
                        Review return entries, customer details
                        and associated weighment records.
                    </p>
                </div>
            </a>
        </div>

        {{-- REPORTS --}}
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('weight.index') }}"
               class="module-tile text-decoration-none h-100">
                <div class="module-icon-box">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <h4>Weight Reports</h4>
                    <p>
                        Review vehicle movements, customer records
                        and weighment information.
                    </p>
                </div>
            </a>
        </div>

    </div>


    {{-- =====================================================
        5. RECENT VEHICLE ENTRIES
    ====================================================== --}}
    <div class="plant-table-card mb-4">

        <div class="card-header-flex p-3 px-4 border-bottom mb-0"
             style="border-color:var(--border-color)!important;">
            <div>
                <h3 class="fs-6 m-0 fw-bold">
                    <i class="fa-solid fa-truck-moving me-2 text-muted"></i>
                    Recent Vehicle Entries
                </h3>
                <p class="text-muted small mb-0">
                    Latest vehicle entry and weighment information
                </p>
            </div>

            <a href="{{ route('weight.index') }}"
               class="btn btn-sm btn-outline-secondary">
                View All <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Truck Number</th>
                        <th>Customer</th>
                        <th>Form Type</th>
                        <th>Entry Date</th>
                        <th>Loaded Weight</th>
                        <th>Tare Weight</th>
                        <th>Net Weight</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse(($recentVehicles ?? collect()) as $vehicle)
                        @php
                            $record = $vehicle->weight ?? null;

                            $loaded = (float) ($record->loadedweight ?? 0);
                            $tare = (float) ($record->tareweight ?? 0);

                            $netWeight = ($record && $record->tareweight !== null)
                                ? abs($loaded - $tare)
                                : null;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td class="fw-bold" style="color:var(--text-heading);">
                                {{ $vehicle->truckno ?? '-' }}
                            </td>

                            <td>
                                {{ $vehicle->customer->company_name ?? '-' }}
                            </td>

                            <td>
                                @php
                                    $type = $vehicle->formlist ?? '-';
                                    $typeClass = match ($type) {
                                        'Material In' => 'badge-status-verified',
                                        'Material Out' => 'badge-status-gross',
                                        'Return' => 'badge-status-inward',
                                        default => 'badge-status-gross',
                                    };
                                @endphp

                                <span class="badge-status {{ $typeClass }}">
                                    {{ $type }}
                                </span>
                            </td>

                            <td>
                                {{ $vehicle->created_at?->format('d M Y, h:i A') ?? '-' }}
                            </td>

                            <td>
                                {{ $record && $record->loadedweight !== null
                                    ? number_format((float) $record->loadedweight, 2) . ' kg'
                                    : '-' }}
                            </td>

                            <td>
                                {{ $record && $record->tareweight !== null
                                    ? number_format((float) $record->tareweight, 2) . ' kg'
                                    : '-' }}
                            </td>

                            <td class="fw-bold">
                                {{ $netWeight !== null
                                    ? number_format($netWeight, 2) . ' kg'
                                    : '-' }}
                            </td>

                            <td>
                                @if($record)
                                    <span class="badge-status badge-status-verified">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Weighed
                                    </span>
                                @else
                                    <span class="badge-status badge-status-gross">
                                        <i class="fa-solid fa-clock"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-truck-ramp-box fa-2x mb-2 d-block"></i>
                                No vehicle entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- =====================================================
        6. WEIGHMENT SUMMARY
    ====================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <div class="chart-box-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon-pill kpi-icon-green">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Total Loaded Weight</div>
                        <h4 class="mb-0 mt-1" style="color:var(--text-heading);">
                            {{ number_format((float) ($totalLoadedWeight ?? 0), 2) }} kg
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="chart-box-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon-pill kpi-icon-amber">
                        <i class="fa-solid fa-weight-hanging"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Total Tare Weight</div>
                        <h4 class="mb-0 mt-1" style="color:var(--text-heading);">
                            {{ number_format((float) ($totalTareWeight ?? 0), 2) }} kg
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="chart-box-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="kpi-icon-pill kpi-icon-blue">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Total Net Weight</div>
                        <h4 class="mb-0 mt-1" style="color:var(--text-heading);">
                            {{ number_format((float) ($totalNetWeight ?? 0), 2) }} kg
                        </h4>
                    </div>
                </div>
            </div>
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

    const textColor = isDark ? '#94A3B8' : '#64748B';
    const gridColor = isDark
        ? 'rgba(255,255,255,.07)'
        : 'rgba(15,23,42,.07)';

    const materialIn = Number(@json($totalMaterialIn ?? 0));
    const materialOut = Number(@json($totalMaterialOut ?? 0));
    const returns = Number(@json($totalReturns ?? 0));

    // Vehicle movement overview
    const overviewCanvas = document.getElementById('weightOverviewChart');

    if (overviewCanvas) {
        new Chart(overviewCanvas, {
            type: 'bar',
            data: {
                labels: ['Material In', 'Material Out', 'Return'],
                datasets: [{
                    label: 'Records',
                    data: [materialIn, materialOut, returns],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B'],
                    borderRadius: 7,
                    maxBarThickness: 55
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Transaction distribution
    const distributionCanvas =
        document.getElementById('weightDistributionChart');

    if (distributionCanvas) {
        new Chart(distributionCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Material In', 'Material Out', 'Return'],
                datasets: [{
                    data: [materialIn, materialOut, returns],
                    backgroundColor: ['#10B981', '#3B82F6', '#F59E0B'],
                    borderColor: isDark ? '#10192A' : '#FFFFFF',
                    borderWidth: 3,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

});
</script>
@endpush
