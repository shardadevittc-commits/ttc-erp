@extends('layouts.app')

@section('title', 'Purchase Dashboard | TTC Robotronics')

@section('content')

@php
    $totalPurchase = (float) ($purchaseSummary->total_purchase ?? 0);
    $orderedQty = (float) ($purchaseSummary->ordered_qty ?? 0);
    $receivedQty = (float) ($purchaseSummary->received_qty ?? 0);
    $pendingQty = (float) ($purchaseSummary->pending_qty ?? 0);
    $pendingValue = (float) ($purchaseSummary->pending_value ?? 0);

    $receivedPercent = $orderedQty > 0
        ? min(100, ($receivedQty / $orderedQty) * 100)
        : 0;

    $formatMoney = function ($amount) {
        return '₹ ' . number_format((float) $amount, 2);
    };

    $formatQty = function ($qty) {
        return number_format((float) $qty, 3);
    };
@endphp

{{-- HERO --}}
<div class="dashboard-hero-card mb-4">
    <div class="row align-items-center">
        <div class="col-lg-8">
            {{-- <span class="badge border mb-2 px-3 py-2">
                <i class="fa-solid fa-cart-flatbed me-1"></i>
                PURCHASE & PROCUREMENT
            </span> --}}

            <h2 class="hero-title">
                Welcome back, {{ $user->first_name ?? 'Purchase User' }}! 👋
            </h2>

            <p class="hero-subtitle">
                Purchase control panel. Monitor purchase orders, supplier activity,
                material receiving, pending quantities and procurement value.
            </p>
        </div>

        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="#purchase-orders" class="btn btn-sm btn-danger px-3 py-2">
                <i class="fa-solid fa-file-circle-plus me-1"></i>
                Purchase Orders
            </a>
        </div>
    </div>
</div>

{{-- KPI CARDS --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-red">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
                <span class="kpi-trend-badge trend-up">Financial Year</span>
            </div>

            <div class="kpi-title">Total Purchase Value</div>
            <div class="kpi-value">
                {{ $formatMoney($totalPurchase) }}
            </div>
            <div class="small text-muted mt-1">
                Current financial year
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-amber">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <span class="kpi-trend-badge trend-neutral">Pending</span>
            </div>

            <div class="kpi-title">Pending Material Value</div>
            <div class="kpi-value">
                {{ $formatMoney($pendingValue) }}
            </div>
            <div class="small text-muted mt-1">
                {{ $formatQty($pendingQty) }} quantity pending
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-green">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <span class="kpi-trend-badge trend-up">Received</span>
            </div>

            <div class="kpi-title">Material Received</div>
            <div class="kpi-value">
                {{ $formatQty($receivedQty) }}
            </div>
            <div class="small text-muted mt-1">
                Out of {{ $formatQty($orderedQty) }} ordered quantity
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-metric-card">
            <div class="kpi-top-row">
                <div class="kpi-icon-pill kpi-icon-blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <span class="kpi-trend-badge trend-neutral">Suppliers</span>
            </div>

            <div class="kpi-title">Total Suppliers</div>
            <div class="kpi-value">
                {{ number_format($supplierCount) }}
            </div>
            <div class="small text-muted mt-1">
                Registered supplier accounts
            </div>
        </div>
    </div>

</div>

{{-- MONTHLY PURCHASE + PRODUCT MIX --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-xl-8">
        <div class="chart-box-card">
            <div class="card-header-flex mb-3">
                <div>
                    <h3>Purchase Value Trend</h3>
                    <p class="text-muted small mb-0">
                        Monthly purchase order value
                    </p>
                </div>

                <span class="badge border px-3 py-2">
                    FY {{ $fyStartYear }}-{{ substr($fyStartYear + 1, -2) }}
                </span>
            </div>

            <div style="height: 300px; position: relative;">
                <canvas id="purchaseTrendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="chart-box-card">
            <div class="card-header-flex mb-3">
                <div>
                    <h3>Material Purchase Mix</h3>
                    <p class="text-muted small mb-0">
                        Product-wise purchase value
                    </p>
                </div>
            </div>

            <div style="height: 240px; position: relative;">
                <canvas id="purchaseProductChart"></canvas>
            </div>

            <div class="small text-muted text-center mt-3">
                Based on purchase order item values
            </div>
        </div>
    </div>

</div>

{{-- PURCHASE OVERVIEW --}}
<div class="row g-3 mb-4">

    <div class="col-12 col-lg-8">
        <div class="chart-box-card h-100">
            <div class="card-header-flex mb-3">
                <div>
                    <h3>Procurement Overview</h3>
                    <p class="text-muted small mb-0">
                        Current financial year material status
                    </p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: var(--bg-input);">
                        <div class="small text-muted">Total Purchase Orders</div>
                        <div class="fs-3 fw-bold">
                            {{ number_format($totalOrders) }}
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div class="p-3 rounded-3" style="background: var(--bg-input);">
                        <div class="small text-muted">This Month Purchase</div>
                        <div class="fs-4 fw-bold">
                            {{ $formatMoney($monthlyPurchase) }}
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Material Receiving Progress</strong>
                        <strong>{{ number_format($receivedPercent, 1) }}%</strong>
                    </div>

                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-success"
                             role="progressbar"
                             style="width: {{ $receivedPercent }}%">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span>Received: {{ $formatQty($receivedQty) }}</span>
                        <span>Pending: {{ $formatQty($pendingQty) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="chart-box-card h-100">
            <h3 class="mb-3">Purchase Operations</h3>

            <div class="d-grid gap-2">
                <a href="#purchase-orders" class="module-tile">
                    <div class="module-icon-box">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </div>
                    <div>
                        <h4>Purchase Orders</h4>
                        <p>Create and manage supplier purchase orders.</p>
                    </div>
                </a>

                <a href="#recent-purchases" class="module-tile">
                    <div class="module-icon-box">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h4>Material Receiving</h4>
                        <p>Monitor ordered and received material.</p>
                    </div>
                </a>

                <a href="#supplier-summary" class="module-tile">
                    <div class="module-icon-box">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div>
                        <h4>Supplier Summary</h4>
                        <p>View registered supplier information.</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

</div>

{{-- RECENT PURCHASE ORDERS --}}
<div class="plant-table-card mb-4" id="recent-purchases">

    <div class="card-header-flex p-3 px-4 border-bottom mb-0">
        <div>
            <h3 class="fs-6 m-0 fw-bold">
                <i class="fa-solid fa-receipt me-2"></i>
                Recent Purchase Orders
            </h3>
            <p class="text-muted small mb-0">
                Latest supplier purchase orders and receiving status
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Basis</th>
                    <th>Ordered Qty</th>
                    <th>Received Qty</th>
                    <th>Pending Qty</th>
                    <th>Order Value</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($recentPurchases as $purchase)
                    @php
                        $ordered = (float) $purchase->ordered_qty;
                        $received = (float) $purchase->received_qty;
                        $pending = max(0, $ordered - $received);

                        $status = $pending <= 0
                            ? 'Completed'
                            : ($received > 0 ? 'Partial' : 'Pending');
                    @endphp

                    <tr>
                        <td class="fw-bold">
                            PO-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
                        </td>

                        <td class="fw-semibold">
                            {{ $purchase->company_name ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $purchase->created_at
                                ? \Carbon\Carbon::parse($purchase->created_at)->format('d M Y')
                                : 'N/A' }}
                        </td>

                        <td>{{ $purchase->basis ?? 'N/A' }}</td>

                        <td>{{ $formatQty($ordered) }}</td>

                        <td class="text-success fw-semibold">
                            {{ $formatQty($received) }}
                        </td>

                        <td class="text-danger fw-semibold">
                            {{ $formatQty($pending) }}
                        </td>

                        <td class="fw-bold">
                            {{ $formatMoney($purchase->order_value) }}
                        </td>

                        <td>
                            @if($status === 'Completed')
                                <span class="badge-status badge-status-verified">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Completed
                                </span>
                            @elseif($status === 'Partial')
                                <span class="badge-status badge-status-gross">
                                    <i class="fa-solid fa-clock"></i>
                                    Partial
                                </span>
                            @else
                                <span class="badge-status badge-status-inward">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            No purchase orders found for this financial year.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- PENDING PURCHASE ORDERS --}}
<div class="plant-table-card mb-4" id="purchase-orders">

    <div class="card-header-flex p-3 px-4 border-bottom mb-0">
        <div>
            <h3 class="fs-6 m-0 fw-bold">
                <i class="fa-solid fa-hourglass-half me-2"></i>
                Pending Material Receipts
            </h3>
            <p class="text-muted small mb-0">
                Purchase orders with material still pending to receive
            </p>
        </div>

        <span class="badge border px-3 py-2">
            {{ $pendingPurchases->count() }} Pending
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Supplier</th>
                    <th>Ordered Qty</th>
                    <th>Received Qty</th>
                    <th>Balance Qty</th>
                    <th>Order Value</th>
                    <th>Order Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($pendingPurchases as $purchase)
                    @php
                        $balance = max(
                            0,
                            (float) $purchase->ordered_qty -
                            (float) $purchase->received_qty
                        );
                    @endphp

                    <tr>
                        <td class="fw-bold">
                            PO-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
                        </td>

                        <td>{{ $purchase->company_name ?? 'N/A' }}</td>

                        <td>{{ $formatQty($purchase->ordered_qty) }}</td>

                        <td>{{ $formatQty($purchase->received_qty) }}</td>

                        <td class="text-danger fw-bold">
                            {{ $formatQty($balance) }}
                        </td>

                        <td>{{ $formatMoney($purchase->order_value) }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No pending material receipts.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = @json($purchaseChartLabels);
    const values = @json($purchaseChartValues);

    const productLabels = @json(
        $productMix->pluck('product_name')->values()
    );

    const productValues = @json(
        $productMix->pluck('total_value')->map(fn($v) => (float) $v)->values()
    );

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

    const labelColor = isDark ? '#AAB6C5' : '#64748B';
    const gridColor = isDark
        ? 'rgba(255,255,255,.07)'
        : 'rgba(11,18,32,.07)';

    // Purchase trend chart
    const trendCanvas = document.getElementById('purchaseTrendChart');

    if (trendCanvas && typeof Chart !== 'undefined') {
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Purchase Value',
                    data: values,
                    borderColor: '#E52D3F',
                    backgroundColor: 'rgba(229,45,63,.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ₹ ' +
                                    Number(context.raw).toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: labelColor },
                        grid: { color: gridColor }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: labelColor,
                            callback: function(value) {
                                return '₹ ' +
                                    Number(value).toLocaleString('en-IN');
                            }
                        },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    }

    // Product mix chart
    const productCanvas = document.getElementById('purchaseProductChart');

    if (productCanvas && typeof Chart !== 'undefined') {
        new Chart(productCanvas, {
            type: 'doughnut',
            data: {
                labels: productLabels,
                datasets: [{
                    data: productValues,
                    backgroundColor: [
                        '#E52D3F',
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#06B6D4'
                    ],
                    borderWidth: 3,
                    borderColor: isDark ? '#10192A' : '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: labelColor,
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₹ ' +
                                    Number(context.raw).toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>

@endpush