
@extends('layouts.app')

@section('title', 'Sales Dashboard | TTC Robotronics')

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-danger-subtle text-danger mb-2">
                        SALES & DISPATCH
                    </span>

                    <h3 class="fw-bold mb-2">
                        Welcome back, {{ $user->first_name ?? 'Sales User' }}! 👋
                    </h3>

                    <p class="text-muted mb-0">
                        Sales overview, invoices, customer outstanding
                        and dispatch performance.
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-success-subtle text-success p-2">
                        Financial Year: {{ $financialYear }}
                    </span>
                </div>
            </div>
        </div>
    </div>


    {{-- KPI CARDS --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Today's Sales</div>

                    <h3 class="fw-bold text-danger mt-2">
                        ₹ {{ number_format($todaySales / 100000, 2) }} L
                    </h3>

                    <small class="text-muted">
                        {{ $todayInvoices }} invoices generated today
                    </small>
                </div>
            </div>
        </div>


        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pending Orders</div>

                    <h3 class="fw-bold text-warning mt-2">
                        {{ number_format($pendingOrderCount) }}
                    </h3>

                    <small class="text-muted">
                        ₹ {{ number_format($pendingOrderValue / 100000, 2) }} L
                        order value
                    </small>
                </div>
            </div>
        </div>


        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Today's Advance</div>

                    <h3 class="fw-bold text-success mt-2">
                        ₹ {{ number_format($todayCollection / 100000, 2) }} L
                    </h3>

                    <small class="text-muted">
                        Advance recorded on today's invoices
                    </small>
                </div>
            </div>
        </div>


        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Customer Outstanding*</div>

                    <h3 class="fw-bold text-primary mt-2">
                        ₹ {{ number_format($outstanding / 100000, 2) }} L
                    </h3>

                    <small class="text-muted">
                        Invoice amount less recorded advance
                    </small>
                </div>
            </div>
        </div>

    </div>


    {{-- FY SALES SUMMARY --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Financial Year Sales
                    </div>

                    <h3 class="fw-bold mt-2">
                        ₹ {{ number_format($fySales / 100000, 2) }} L
                    </h3>

                    <small class="text-muted">
                        {{ $totalInvoices }} invoices in FY
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Total Sales Orders
                    </div>

                    <h3 class="fw-bold mt-2">
                        {{ number_format($totalOrders) }}
                    </h3>

                    <small class="text-muted">
                        ₹ {{ number_format($totalOrderValue / 100000, 2) }} L
                        total order value
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Pending Order Value
                    </div>

                    <h3 class="fw-bold text-warning mt-2">
                        ₹ {{ number_format($pendingOrderValue / 100000, 2) }} L
                    </h3>

                    <small class="text-muted">
                        Based on pending quantities and item rates
                    </small>
                </div>
            </div>
        </div>

    </div>


    {{-- CHARTS --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-8 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-3">
                    <h5 class="fw-bold mb-1">Sales Performance</h5>
                    <small class="text-muted">
                        Monthly invoice sales – {{ $financialYear }}
                    </small>
                </div>

                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-4 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-3">
                    <h5 class="fw-bold mb-1">Product Wise Orders</h5>
                    <small class="text-muted">
                        Financial-year ordered quantity
                    </small>
                </div>

                <div class="card-body">
                    <div style="height:260px;">
                        <canvas id="productChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- RECENT INVOICES --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white p-3">
            <h5 class="fw-bold mb-1">Recent Sales Invoices</h5>
            <small class="text-muted">
                Latest invoices from sale_invoices table
            </small>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Invoice Amount</th>
                            <th>Advance</th>
                            <th>Dispatch Qty</th>
                            <th>Payment Status</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentInvoices as $invoice)

                            @php
                                $advance = (float) $invoice->advance_payment;
                                $total = (float) $invoice->total_amount;

                                if ($advance >= $total && $total > 0) {
                                    $paymentStatus = 'Received';
                                    $statusClass = 'success';
                                } elseif ($advance > 0) {
                                    $paymentStatus = 'Partial';
                                    $statusClass = 'warning';
                                } else {
                                    $paymentStatus = 'Pending';
                                    $statusClass = 'danger';
                                }
                            @endphp

                            <tr>
                                <td class="fw-semibold">
                                    {{ $invoice->invoice_no }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}
                                </td>

                                <td>
                                    {{ $invoice->company_name ?? 'N/A' }}
                                </td>

                                <td class="fw-bold">
                                    ₹ {{ number_format($total, 2) }}
                                </td>

                                <td class="text-success">
                                    ₹ {{ number_format($advance, 2) }}
                                </td>

                                <td>
                                    {{ number_format((float) $invoice->dispatched_qty, 3) }}
                                </td>

                                <td>
                                    <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }}">
                                        {{ $paymentStatus }}
                                    </span>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No sales invoices found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>
    </div>


    {{-- PENDING SALES ORDERS --}}
    <div class="card border-0 shadow-sm mb-4" id="sales-orders">

        <div class="card-header bg-white p-3">
            <h5 class="fw-bold mb-1">Pending Sales Orders</h5>
            <small class="text-muted">
                Orders with pending dispatch quantities
            </small>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Order No.</th>
                            <th>Customer</th>
                            <th>Order Date</th>
                            <th>Ordered Qty</th>
                            <th>Dispatched Qty</th>
                            <th>Pending Qty</th>
                            <th>Order Value</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($recentPendingOrders as $order)

                            <tr>
                                <td class="fw-bold">
                                    SO-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                </td>

                                <td>
                                    {{ $order->company_name ?? 'N/A' }}
                                </td>

                                <td>
                                    {{ $order->created_at
                                        ? \Carbon\Carbon::parse($order->created_at)->format('d M Y')
                                        : '-' }}
                                </td>

                                <td>
                                    {{ number_format((float) $order->ordered_qty, 3) }}
                                </td>

                                <td class="text-success">
                                    {{ number_format((float) $order->dispatched_qty, 3) }}
                                </td>

                                <td class="text-danger fw-bold">
                                    {{ number_format((float) $order->pending_qty, 3) }}
                                </td>

                                <td class="fw-semibold">
                                    ₹ {{ number_format((float) $order->order_value, 2) }}
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    No pending sales orders found.
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

    // SALES PERFORMANCE CHART

    const salesCanvas = document.getElementById('salesChart');

    if (salesCanvas) {

        new Chart(salesCanvas, {

            type: 'line',

            data: {
                labels: @json($monthLabels),

                datasets: [{
                    label: 'Sales (₹ Lakhs)',

                    data: @json($monthSales),

                    borderColor: '#E52D3F',

                    backgroundColor: 'rgba(229,45,63,0.12)',

                    borderWidth: 3,

                    fill: true,

                    tension: 0.4,

                    pointRadius: 4,

                    pointBackgroundColor: '#E52D3F'
                }]
            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function(value) {
                                return '₹ ' + value + ' L';
                            }
                        }
                    }
                }

            }

        });

    }


    // PRODUCT MIX CHART

    const productCanvas = document.getElementById('productChart');

    if (productCanvas) {

        new Chart(productCanvas, {

            type: 'doughnut',

            data: {

                labels: @json($productLabels),

                datasets: [{

                    data: @json($productValues),

                    backgroundColor: [
                        '#E52D3F',
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#06B6D4',
                        '#64748B'
                    ],

                    borderWidth: 2

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }

            }

        });

    }

});

</script>

@endpush
