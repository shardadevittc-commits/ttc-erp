
@extends('layouts.app')

@section('title', 'Production Dashboard')

@section('content')

<style>
    .production-dashboard {
        background: #f5f7fb;
        min-height: 100vh;
        padding: 24px;
    }

    .pd-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .pd-header h3 {
        font-weight: 800;
        color: #172033;
        margin-bottom: 5px;
    }

    .pd-subtitle {
        color: #7b8497;
        font-size: 13px;
    }

    .pd-card {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 14px;
        box-shadow: 0 4px 15px rgba(25, 40, 70, .04);
        height: 100%;
        overflow: hidden;
    }

    .pd-kpi {
        padding: 22px;
        position: relative;
    }

    .pd-kpi-label {
        font-size: 13px;
        color: #7b8497;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .pd-kpi-value {
        font-size: 27px;
        font-weight: 800;
        color: #172033;
        margin-bottom: 8px;
    }

    .pd-kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .pd-card-header {
        padding: 20px 22px 10px;
        border-bottom: 1px solid #f0f2f6;
    }

    .pd-card-header h5 {
        font-size: 16px;
        font-weight: 750;
        margin-bottom: 5px;
        color: #172033;
    }

    .pd-card-body {
        padding: 20px 22px;
    }

    .pd-table {
        margin-bottom: 0;
        white-space: nowrap;
    }

    .pd-table thead th {
        background: #f7f8fb;
        color: #7b8497;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 14px;
        border: none;
    }

    .pd-table tbody td {
        padding: 14px;
        color: #465166;
        font-size: 13px;
        vertical-align: middle;
        border-color: #f0f2f6;
    }

    .pd-badge {
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        display: inline-block;
    }

    .pd-section-title {
        font-size: 17px;
        font-weight: 800;
        color: #172033;
    }

    .pd-progress {
        height: 11px;
        background: #edf0f5;
        border-radius: 20px;
        overflow: hidden;
    }

    .pd-progress-bar {
        height: 100%;
        border-radius: 20px;
        background: #16a36a;
    }

    @media(max-width:767px) {
        .production-dashboard {
            padding: 14px;
        }

        .pd-kpi-value {
            font-size: 23px;
        }
    }
</style>

<div class="production-dashboard">

    {{-- HEADER --}}
    <div class="pd-header">
        <div>
            <h3>Production & Manufacturing</h3>
            <div class="pd-subtitle">
                Work orders, production performance and scrap monitoring
            </div>
        </div>

        <div class="text-muted small">
            <i class="fas fa-calendar-alt me-1"></i>
            {{ now()->format('d M Y') }}
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="pd-card pd-kpi">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="pd-kpi-label">Today's Production</div>
                        <div class="pd-kpi-value">
                            {{ number_format($todayProduction, 2) }}
                        </div>
                        <small class="text-muted">Today's quantity</small>
                    </div>

                    <div class="pd-kpi-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-industry"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="pd-card pd-kpi">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="pd-kpi-label">Total Production</div>
                        <div class="pd-kpi-value">
                            {{ number_format($totalProduction, 2) }}
                        </div>
                        <small class="text-muted">All recorded quantity</small>
                    </div>

                    <div class="pd-kpi-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="pd-card pd-kpi">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="pd-kpi-label">Pending Production</div>
                        <div class="pd-kpi-value">
                            {{ number_format($pendingProduction, 2) }}
                        </div>
                        <small class="text-muted">Pending / In process</small>
                    </div>

                    <div class="pd-kpi-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="pd-card pd-kpi">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="pd-kpi-label">Total Scrap / Loss</div>
                        <div class="pd-kpi-value text-danger">
                            {{ number_format($totalScrap, 2) }}
                        </div>
                        <small class="text-muted">
                            Today's: {{ number_format($todayScrap, 2) }}
                        </small>
                    </div>

                    <div class="pd-kpi-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-recycle"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- CHARTS --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-8">
            <div class="pd-card">
                <div class="pd-card-header">
                    <h5>Production Trend</h5>
                    <small class="text-muted">Last 7 days production</small>
                </div>

                <div class="pd-card-body">
                    <canvas id="productionTrend" height="105"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="pd-card">
                <div class="pd-card-header">
                    <h5>Product-wise Production</h5>
                    <small class="text-muted">Top 6 products</small>
                </div>

                <div class="pd-card-body">

                    @if($productWise->isNotEmpty())
                        <canvas id="productChart"></canvas>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-3"></i>
                            <p class="mb-0">No product data available.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

    {{-- EFFICIENCY --}}
    <div class="pd-card mb-4">
        <div class="pd-card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="pd-section-title">Production Efficiency</div>
                    <small class="text-muted">
                        Production compared with production plus scrap
                    </small>
                </div>

                <h4 class="fw-bold text-success mb-0">
                    {{ number_format($efficiency, 2) }}%
                </h4>
            </div>

            <div class="pd-progress">
                <div class="pd-progress-bar"
                     style="width: {{ min(max($efficiency, 0), 100) }}%">
                </div>
            </div>

        </div>
    </div>

    {{-- RECENT WORK ORDERS --}}
    <div class="pd-card mb-4">

        <div class="pd-card-header">
            <h5>Recent Work Orders & Processes</h5>
            <small class="text-muted">Latest 10 records</small>
        </div>

        <div class="pd-card-body">
            <div class="table-responsive">

                <table class="table pd-table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Work Order</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentOrders as $index => $order)

                            @php
                                $status = $statusCol
                                    ? ($order->{$statusCol} ?? 'N/A')
                                    : 'N/A';

                                $statusLower = strtolower((string) $status);

                                $badgeClass = str_contains($statusLower, 'complete')
                                    ? 'bg-success bg-opacity-10 text-success'
                                    : (str_contains($statusLower, 'pending')
                                        ? 'bg-warning bg-opacity-10 text-warning'
                                        : 'bg-primary bg-opacity-10 text-primary');
                            @endphp

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td class="fw-semibold">
                                    {{ $orderCol
                                        ? ($order->{$orderCol} ?? '-')
                                        : ($order->id ?? '-') }}
                                </td>

                                <td>
                                    {{ $productCol
                                        ? ($order->{$productCol} ?? '-')
                                        : '-' }}
                                </td>

                                <td>
                                    {{ $workQtyCol
                                        ? number_format((float) ($order->{$workQtyCol} ?? 0), 2)
                                        : '-' }}
                                </td>

                                <td>
                                    <span class="pd-badge {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $workDateCol && !empty($order->{$workDateCol})
                                        ? \Carbon\Carbon::parse($order->{$workDateCol})->format('d M Y')
                                        : '-' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No work orders found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>

    {{-- SCRAP RECORDS --}}
    <div class="pd-card">

        <div class="pd-card-header">
            <h5>Recent Scrap / Loss Records</h5>
            <small class="text-muted">Latest 10 scrap entries</small>
        </div>

        <div class="pd-card-body">

            <div class="table-responsive">
                <table class="table pd-table table-hover">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Scrap Quantity</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentScrap as $index => $scrap)

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td class="fw-bold text-danger">
                                    {{ $scrapQtyCol
                                        ? number_format((float) ($scrap->{$scrapQtyCol} ?? 0), 2)
                                        : '-' }}
                                </td>

                                <td>
                                    {{ $scrapDateCol && !empty($scrap->{$scrapDateCol})
                                        ? \Carbon\Carbon::parse($scrap->{$scrapDateCol})->format('d M Y')
                                        : '-' }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No scrap records found.
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Production trend chart
    const trendCanvas = document.getElementById('productionTrend');

    if (trendCanvas) {

        new Chart(trendCanvas, {
            type: 'line',

            data: {
                labels: @json($productionLabels),

                datasets: [{
                    label: 'Production',
                    data: @json($productionData),

                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.12)',

                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: true,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Product-wise chart
    const productCanvas = document.getElementById('productChart');

    if (productCanvas) {

        new Chart(productCanvas, {
            type: 'doughnut',

            data: {
                labels: @json($productWise->pluck('product')->values()),

                datasets: [{
                    data: @json($productWise->pluck('total')->values()),

                    backgroundColor: [
                        '#2563eb',
                        '#16a34a',
                        '#f59e0b',
                        '#dc2626',
                        '#7c3aed',
                        '#0891b2'
                    ],

                    borderWidth: 0,
                    hoverOffset: 7
                }]
            },

            options: {
                responsive: true,

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },

                cutout: '65%'
            }
        });
    }

});
</script>

@endpush
