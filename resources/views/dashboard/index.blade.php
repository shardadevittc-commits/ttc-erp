
@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role->short_name ?? 'User') . ' Dashboard | TTC Robotronics - Steel Industry ERP')

@section('content')

@php
    $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    $userInitial = strtoupper(substr($user->first_name ?? 'U', 0, 1));
@endphp

<style>
    .dashboard-hero-card {
        background: linear-gradient(120deg, #172554, #1e40af);
        color: #fff;
        border-radius: 16px;
        padding: 28px;
        position: relative;
        overflow: hidden;
    }

    .dashboard-hero-card::after {
        content: "";
        position: absolute;
        width: 220px;
        height: 220px;
        right: -55px;
        top: -90px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }

    .dashboard-hero-card h3 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .dashboard-hero-card p {
        color: rgba(255,255,255,.75);
        margin-bottom: 0;
    }

    .dashboard-stat-card {
        border: 1px solid #edf0f5;
        border-radius: 14px;
        background: #fff;
        padding: 20px;
        height: 100%;
        transition: .2s ease;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(15,23,42,.07);
    }

    .stat-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 21px;
    }

    .stat-label {
        color: #64748b;
        font-size: 13px;
        margin-bottom: 8px;
    }

    .stat-value {
        color: #172554;
        font-size: 25px;
        font-weight: 700;
        line-height: 1.3;
    }

    .dashboard-panel {
        background: #fff;
        border: 1px solid #edf0f5;
        border-radius: 14px;
        padding: 22px;
        height: 100%;
    }

    .dashboard-panel-title {
        font-size: 16px;
        font-weight: 700;
        color: #172554;
        margin-bottom: 4px;
    }

    .dashboard-panel-subtitle {
        font-size: 12px;
        color: #94a3b8;
    }

    .dashboard-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        background: #f8fafc;
        padding: 13px 12px;
    }

    .dashboard-table td {
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
        padding: 13px 12px;
    }

    .dashboard-avatar {
        width: 58px;
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 22px;
        font-weight: 700;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-unloaded {
        background: #dcfce7;
        color: #15803d;
    }

    .status-weighed {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-in-weighing {
        background: #fef3c7;
        color: #b45309;
    }

    .status-pending {
        background: #fee2e2;
        color: #b91c1c;
    }

    .stat-blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .stat-green {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .stat-purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .stat-cyan {
        background: #cffafe;
        color: #0891b2;
    }

    .stat-pink {
        background: #fce7f3;
        color: #db2777;
    }

    @media(max-width: 767px) {
        .dashboard-hero-card {
            padding: 22px;
        }

        .stat-value {
            font-size: 21px;
        }

        .dashboard-panel {
            padding: 16px;
        }
    }
</style>

<div class="container-fluid">

    {{-- ==========================================
        1. HERO SECTION
    =========================================== --}}
    <div class="dashboard-hero-card mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-light text-primary">
                        <i class="fas fa-industry me-1"></i>
                        TTC Robotronics ERP
                    </span>

                    <span class="badge bg-success">
                        <i class="fas fa-circle me-1" style="font-size:7px;"></i>
                        System Active
                    </span>
                </div>

                <h3>
                    Welcome back, {{ $userName ?: ($user->username ?? 'User') }}!
                </h3>

                <p>
                    Manage your steel industry operations, production,
                    material movement and business activities from one place.
                </p>

                <div class="mt-3">
                    <span class="me-3">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ now()->format('l, d F Y') }}
                    </span>

                    <span>
                        <i class="far fa-clock me-1"></i>
                        {{ now()->format('h:i A') }}
                    </span>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                <div class="text-end position-relative" style="z-index:1;">
                    <i class="fas fa-industry"
                       style="font-size:90px;color:rgba(255,255,255,.18);"></i>
                    <div class="mt-2 fw-bold">
                        Steel Industry Management
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ==========================================
        2. MAIN KPI CARDS
    =========================================== --}}
    <div class="row g-3 mb-4">

        {{-- Total Users --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-value">
                            {{ number_format($stats['total_users'] ?? 0) }}
                        </div>
                        <small class="text-muted">Registered users</small>
                    </div>

                    <div class="stat-icon stat-blue">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Active Users</div>
                        <div class="stat-value">
                            {{ number_format($stats['active_users'] ?? 0) }}
                        </div>
                        <small class="text-muted">Currently active</small>
                    </div>

                    <div class="stat-icon stat-green">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Roles --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Total Roles</div>
                        <div class="stat-value">
                            {{ number_format($stats['total_roles'] ?? 0) }}
                        </div>
                        <small class="text-muted">System roles</small>
                    </div>

                    <div class="stat-icon stat-purple">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Today's Weighbridge Slips --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Today's Weighbridge Slips</div>
                        <div class="stat-value">
                            {{ number_format($stats['today_weighbridge_slips'] ?? 0) }}
                        </div>
                        <small class="text-muted">Today's transactions</small>
                    </div>

                    <div class="stat-icon stat-orange">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- ==========================================
        3. MATERIAL MOVEMENT CARDS
    =========================================== --}}
    <div class="row g-3 mb-4">

        {{-- Material Sent Today --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Material Sent Today</div>
                        <div class="stat-value">
                            {{ number_format((float)($stats['today_material_sent'] ?? 0), 3) }}
                        </div>
                        <small class="text-muted">
                            Sum of material_send_for_work
                        </small>
                    </div>

                    <div class="stat-icon stat-orange">
                        <i class="fas fa-dolly"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Material Received Today --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Material Received Today</div>
                        <div class="stat-value">
                            {{ number_format((float)($stats['today_received_qty'] ?? 0), 3) }}
                        </div>
                        <small class="text-muted">
                            Sum of received_qty
                        </small>
                    </div>

                    <div class="stat-icon stat-green">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Invoice Amount --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Monthly Invoice Amount</div>
                        <div class="stat-value">
                            ₹{{ number_format((float)($stats['monthly_invoice_amount'] ?? 0), 2) }}
                        </div>
                        <small class="text-muted">Current month</small>
                    </div>

                    <div class="stat-icon stat-purple">
                        <i class="fas fa-indian-rupee-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Invoice Count --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="dashboard-stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label">Monthly Invoices</div>
                        <div class="stat-value">
                            {{ number_format($stats['monthly_invoice_count'] ?? 0) }}
                        </div>
                        <small class="text-muted">Invoices this month</small>
                    </div>

                    <div class="stat-icon stat-cyan">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- ==========================================
        4. CHART SECTION
    =========================================== --}}
    <div class="row g-3 mb-4">

        {{-- Material & Production Chart --}}
        <div class="col-12 col-xl-8">
            <div class="dashboard-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="dashboard-panel-title">
                            Material & Production Overview
                        </div>
                        <div class="dashboard-panel-subtitle">
                            Last 7 days performance
                        </div>
                    </div>

                    <span class="badge bg-light text-primary">
                        Last 7 Days
                    </span>
                </div>

                <div style="height:310px;position:relative;">
                    <canvas id="materialProductionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- User Profile --}}
        <div class="col-12 col-xl-4">
            <div class="dashboard-panel">
                <div class="dashboard-panel-title mb-3">
                    My Profile
                </div>

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="dashboard-avatar">
                        {{ $userInitial }}
                    </div>

                    <div>
                        <h6 class="mb-1 fw-bold">
                            {{ $userName ?: ($user->username ?? 'User') }}
                        </h6>

                        <div class="text-muted small">
                            {{ $user->role->name ?? $user->role->short_name ?? 'User' }}
                        </div>

                        <span class="badge bg-success mt-1">
                            {{ ($user->status ?? 0) == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Username</small>
                    <div class="fw-semibold">
                        {{ $user->username ?? 'N/A' }}
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Email Address</small>
                    <div class="fw-semibold text-break">
                        {{ $user->email ?? 'N/A' }}
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Phone Number</small>
                    <div class="fw-semibold">
                        {{ $user->phone_number ?? 'N/A' }}
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Address</small>
                    <div class="fw-semibold">
                        {{ $user->address ?? 'N/A' }}
                    </div>
                </div>

                @if(Route::has('profile.edit'))
                    <a href="{{ route('profile.edit') }}"
                       class="btn btn-outline-primary btn-sm w-100 mt-2">
                        <i class="fas fa-user-edit me-1"></i>
                        Edit Profile
                    </a>
                @endif
            </div>
        </div>

    </div>


    {{-- ==========================================
        5. RECENT WEIGHBRIDGE TRANSACTIONS
    =========================================== --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="dashboard-panel">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div>
                        <div class="dashboard-panel-title">
                            Recent Weighbridge Transactions
                        </div>

                        <div class="dashboard-panel-subtitle">
                            Latest 10 weighbridge records
                        </div>
                    </div>

                    @if(Route::has('weights.index'))
                        <a href="{{ route('weights.index') }}"
                           class="btn btn-sm btn-outline-primary mt-2 mt-md-0">
                            View All
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table dashboard-table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Token No.</th>
                                <th>Vehicle No.</th>
                                <th>Customer</th>
                                <th>First Weight</th>
                                <th>Second Weight</th>
                                <th>Material Weight</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentTransactions as $index => $transaction)

                                @php
                                    $status = $transaction->transaction_status ?? 'Pending';

                                    $statusClass = match($status) {
                                        'Unloaded' => 'status-unloaded',
                                        'Weighed' => 'status-weighed',
                                        'In Weighing' => 'status-in-weighing',
                                        default => 'status-pending',
                                    };
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        {{ $transaction->created_at
                                            ? \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, h:i A')
                                            : 'N/A' }}
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $transaction->tokenno ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="fw-semibold">
                                            {{ $transaction->truckno ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $transaction->company_name ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $transaction->first_weight ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ $transaction->second_weight ?? 'N/A' }}
                                    </td>

                                    <td>
                                        {{ is_numeric($transaction->material_weight ?? null)
                                            ? number_format((float)$transaction->material_weight, 3)
                                            : ($transaction->material_weight ?? 'N/A') }}
                                    </td>

                                    <td>
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No recent transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    {{-- ==========================================
        6. FOOTER
    =========================================== --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center py-3">
        <small class="text-muted">
            © {{ date('Y') }} TTC Robotronics. All rights reserved.
        </small>

        <small class="text-muted">
            Steel Industry ERP Dashboard
        </small>
    </div>

</div>

@endsection


{{-- ==========================================
    CHART JS
=========================================== --}}
@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const chartElement = document.getElementById('materialProductionChart');

    if (chartElement) {

        const labels = @json($chartLabels ?? []);
        const materialSent = @json($chartMaterialSent ?? []);
        const finishedProduction = @json($chartFinishedProduction ?? []);

        new Chart(chartElement, {
            type: 'bar',

            data: {
                labels: labels,

                datasets: [
                    {
                        label: 'Material Sent',
                        data: materialSent,
                        backgroundColor: 'rgba(37, 99, 235, 0.75)',
                        borderColor: '#2563eb',
                        borderWidth: 1,
                        borderRadius: 5,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Finished Production',
                        data: finishedProduction,
                        type: 'line',
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.12)',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    mode: 'index',
                    intersect: false
                },

                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end'
                    },

                    tooltip: {
                        enabled: true
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },

                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Material Sent'
                        }
                    },

                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Finished Production'
                        }
                    }
                }
            }
        });
    }

});
</script>

@endpush
