@extends('layouts.app')

@section('title', 'Dashboard | TTC Robotronics - Steel Industry ERP')

@section('content')
    <!-- 1. Hero Banner Card -->
    <div class="dashboard-hero-card mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge border px-2.5 py-1 fs-7" style="background: var(--bg-input); color: var(--text-heading); border-color: var(--border-color) !important;">
                        <i class="fa-solid fa-industry me-1 text-muted"></i> DIVINE BRIGHT STEELS
                    </span>
                    <span class="badge border px-2.5 py-1 fs-7" style="background: var(--accent-green-subtle); color: var(--accent-green); border-color: var(--accent-green-border) !important;">
                        <i class="fa-solid fa-check-double me-1"></i> MILL ONLINE
                    </span>
                </div>
                <h2 class="hero-title">Welcome back, {{ $user->first_name }}! 👋</h2>
                <p class="hero-subtitle">
                    Centralized Steel Manufacturing & Plant Operations Control Panel. Monitor real-time raw scrap arrivals, weighbridge gross receipts, laboratory heat certificates, and ERW pipe production.
                </p>
            </div>
            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="#weighbridge" class="btn btn-sm text-white px-3 py-2 fw-semibold rounded-3 shadow-sm me-2" style="background-color: var(--primary-red); border-color: var(--primary-red);">
                    <i class="fa-solid fa-plus me-1"></i> New Weighing Slip
                </a>
                <a href="#dispatch" class="btn btn-sm px-3 py-2 fw-semibold rounded-3" style="color: var(--text-heading); background: var(--bg-card); border: 1px solid var(--border-color);">
                    <i class="fa-solid fa-file-lines me-1"></i> Challans
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Dash UI Signature Metric KPI Cards with Embedded Sparklines -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Total Users -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-red">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i> +12.4%
                    </span>
                </div>
                <div>
                    <div class="kpi-title">Total ERP Users</div>
                    <div class="kpi-value-row">
                        <div class="kpi-value">{{ $stats['total_users'] }}</div>
                        <!-- Mini SVG Sparkline Bars -->
                        <svg class="sparkline-svg" viewBox="0 0 90 34">
                            <rect x="2" y="18" width="6" height="16" rx="2" fill="#E52D3F" opacity="0.35"/>
                            <rect x="13" y="14" width="6" height="20" rx="2" fill="#E52D3F" opacity="0.45"/>
                            <rect x="24" y="10" width="6" height="24" rx="2" fill="#E52D3F" opacity="0.6"/>
                            <rect x="35" y="16" width="6" height="18" rx="2" fill="#E52D3F" opacity="0.4"/>
                            <rect x="46" y="8" width="6" height="26" rx="2" fill="#E52D3F" opacity="0.75"/>
                            <rect x="57" y="12" width="6" height="22" rx="2" fill="#E52D3F" opacity="0.6"/>
                            <rect x="68" y="5" width="6" height="29" rx="2" fill="#E52D3F" opacity="0.9"/>
                            <rect x="79" y="2" width="6" height="32" rx="2" fill="#E52D3F"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Today's Production -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-blue">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i> +8.2%
                    </span>
                </div>
                <div>
                    <div class="kpi-title">Production (Today)</div>
                    <div class="kpi-value-row">
                        <div class="kpi-value">1,480 <span style="font-size: 0.95rem; font-weight: 500; color: var(--text-muted);">MT</span></div>
                        <!-- Mini SVG Sparkline Wave -->
                        <svg class="sparkline-svg" viewBox="0 0 90 34">
                            <path d="M2,28 C15,26 22,12 36,18 C50,24 60,6 74,10 C80,12 85,4 88,4" fill="none" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Weighbridge Inward/Outward -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-green">
                        <i class="fa-solid fa-truck-moving"></i>
                    </div>
                    <span class="kpi-trend-badge trend-neutral">
                        <i class="fa-solid fa-arrows-spin"></i> Active
                    </span>
                </div>
                <div>
                    <div class="kpi-title">Weighbridge Slips</div>
                    <div class="kpi-value-row">
                        <div class="kpi-value">42 <span style="font-size: 0.95rem; font-weight: 500; color: var(--text-muted);">Trucks</span></div>
                        <!-- Mini SVG Sparkline Bars -->
                        <svg class="sparkline-svg" viewBox="0 0 90 34">
                            <rect x="2" y="10" width="6" height="24" rx="2" fill="#10B981" opacity="0.4"/>
                            <rect x="13" y="16" width="6" height="18" rx="2" fill="#10B981" opacity="0.4"/>
                            <rect x="24" y="6" width="6" height="28" rx="2" fill="#10B981" opacity="0.7"/>
                            <rect x="35" y="14" width="6" height="20" rx="2" fill="#10B981" opacity="0.5"/>
                            <rect x="46" y="8" width="6" height="26" rx="2" fill="#10B981" opacity="0.8"/>
                            <rect x="57" y="12" width="6" height="22" rx="2" fill="#10B981" opacity="0.6"/>
                            <rect x="68" y="4" width="6" height="30" rx="2" fill="#10B981"/>
                            <rect x="79" y="8" width="6" height="26" rx="2" fill="#10B981" opacity="0.85"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Monthly Dispatch / Revenue -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-metric-card">
                <div class="kpi-top-row">
                    <div class="kpi-icon-pill kpi-icon-amber">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i> +16.9%
                    </span>
                </div>
                <div>
                    <div class="kpi-title">Monthly Dispatch</div>
                    <div class="kpi-value-row">
                        <div class="kpi-value">₹ 4.85 <span style="font-size: 0.95rem; font-weight: 500; color: var(--text-muted);">Cr</span></div>
                        <!-- Mini SVG Sparkline Wave -->
                        <svg class="sparkline-svg" viewBox="0 0 90 34">
                            <path d="M2,24 C14,22 22,8 36,12 C48,16 58,4 72,6 C80,8 84,2 88,2" fill="none" stroke="#F59E0B" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Visual Charts & Analytics Section (Like Dash UI CRM) -->
    <div class="row g-3 mb-4">
        <!-- Left: Production & Dispatch Monthly Trend -->
        <div class="col-12 col-xl-8">
            <div class="chart-box-card">
                <div class="card-header-flex">
                    <div>
                        <h3>Plant Inward vs Production Curve (MT)</h3>
                        <p class="text-muted small mb-0">Daily Scrap Inward vs ERW Finished Pipes Output</p>
                    </div>
                    <div class="chart-filter-pills">
                        <button type="button" class="filter-btn active">7 Days</button>
                        <button type="button" class="filter-btn">Month</button>
                        <button type="button" class="filter-btn">Quarter</button>
                    </div>
                </div>
                <div style="height: 275px; position: relative;">
                    <canvas id="productionTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right: Steel Material Inventory Share (Doughnut Chart) -->
        <div class="col-12 col-xl-4">
            <div class="chart-box-card">
                <div class="card-header-flex">
                    <div>
                        <h3>Yard Stock Breakdown</h3>
                        <p class="text-muted small mb-0">Raw Material & Pipe Inventory</p>
                    </div>
                    <span class="badge border fs-7" style="background: var(--accent-blue-subtle); color: var(--accent-blue); border-color: var(--accent-blue-border) !important;">
                        84% Capacity
                    </span>
                </div>
                <div style="height: 205px; position: relative;" class="d-flex align-items-center justify-content-center">
                    <canvas id="inventoryDoughnutChart"></canvas>
                </div>
                <div class="d-flex justify-content-around text-center pt-3 border-top mt-2" style="border-color: var(--border-color) !important;">
                    <div>
                        <div class="small text-muted">Scrap Billets</div>
                        <div class="fw-bold" style="color: var(--text-heading);">42%</div>
                    </div>
                    <div>
                        <div class="small text-muted">HR Strip Coils</div>
                        <div class="fw-bold" style="color: var(--text-heading);">33%</div>
                    </div>
                    <div>
                        <div class="small text-muted">ERW Pipes</div>
                        <div class="fw-bold" style="color: var(--text-heading);">25%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Steel Operations Modules Grid + User Profile Widget -->
    <div class="row g-4 mb-4">
        <!-- Left: Operational Modules -->
        <div class="col-12 col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-5 m-0 fw-bold"><i class="fa-solid fa-cubes me-2 text-muted"></i>Steel Plant Operations</h3>
                <span class="text-muted small">Permissions assigned to: <strong>{{ $user->role->name ?? 'Admin' }}</strong></span>
            </div>

            <div class="row g-3">
                <!-- Module 1: Users & Roles -->
                <div class="col-12 col-md-6">
                    <a href="{{ route('users.index') }}" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <h4>Users & Role Access</h4>
                            <p>Manage system users, assigned roles, credentials, and module permissions.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 2: Gate Entry -->
                <div class="col-12 col-md-6">
                    <a href="#gate" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <div>
                            <h4>Gate Entry & Passes</h4>
                            <p>Inward/outward truck registration, driver verification, and security passes.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 3: Weighbridge -->
                <div class="col-12 col-md-6">
                    <a href="#weighbridge" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div>
                            <h4>Weighbridge System</h4>
                            <p>Gross weight capture, tare deduction, net steel weight calculation & receipts.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 4: Raw Material / Scrap Yard -->
                <div class="col-12 col-md-6">
                    <a href="#unloader" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-dolly"></i>
                        </div>
                        <div>
                            <h4>Material & Scrap Yard</h4>
                            <p>Scrap yard receipt, visual physical check, moisture and dust deduction slips.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 5: Quality & Lab -->
                <div class="col-12 col-md-6">
                    <a href="#lab" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-flask-vial"></i>
                        </div>
                        <div>
                            <h4>Quality & Chemical Lab</h4>
                            <p>Spectrometer chemical test, tensile test, bend test, and heat certificate generation.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 6: Production & ERW Pipes -->
                <div class="col-12 col-md-6">
                    <a href="#production" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-industry"></i>
                        </div>
                        <div>
                            <h4>Mill & ERW Production</h4>
                            <p>Furnace billets, strip rolling, ERW high-frequency induction tube welding & cutting.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 7: Finished Goods Dispatch -->
                <div class="col-12 col-md-6">
                    <a href="#dispatch" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-truck-ramp-box"></i>
                        </div>
                        <div>
                            <h4>Finished Goods Dispatch</h4>
                            <p>Bundle barcode tagging, truck loading inspection, dispatch challan & e-way bills.</p>
                        </div>
                    </a>
                </div>

                <!-- Module 8: Accounts & Billing -->
                <div class="col-12 col-md-6">
                    <a href="#invoices" class="module-tile">
                        <div class="module-icon-box">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h4>Invoicing & GST E-Way</h4>
                            <p>Tax invoices, sales/purchase ledger, payment vouchers, and GST compliance.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Polished User Profile Card -->
        <div class="col-12 col-lg-4 align-self-start">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fs-5 m-0 fw-bold"><i class="fa-solid fa-id-badge me-2 text-muted"></i>User Profile</h3>
                <span class="badge border px-2 py-1 fs-7" style="background: var(--accent-green-subtle); color: var(--accent-green); border-color: var(--accent-green-border) !important;">
                    <i class="fa-solid fa-circle-check me-1"></i> Active Session
                </span>
            </div>

            <div class="chart-box-card h-auto" style="height: max-content !important;">
                <!-- Hero Avatar Card -->
                <div class="profile-hero-badge">
                    <div class="profile-avatar-large">
                        {{ strtoupper(substr($user->first_name ?? 'A', 0, 1)) }}
                    </div>
                    <h4 class="mb-1" style="font-size: 1.12rem;">{{ $user->name }}</h4>
                    <span class="badge text-white px-3 py-1.5 rounded-pill fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px; background-color: var(--primary-red);">
                        ROLE: {{ strtoupper($user->role->name ?? 'ADMIN') }}
                    </span>
                </div>

                <!-- Profile Details Rows -->
                <div class="profile-info-row">
                    <span class="label"><i class="fa-solid fa-user"></i> Username</span>
                    <span class="val">{{ $user->username }}</span>
                </div>
                <div class="profile-info-row">
                    <span class="label"><i class="fa-solid fa-envelope"></i> Email</span>
                    <span class="val">{{ $user->email }}</span>
                </div>
                <div class="profile-info-row">
                    <span class="label"><i class="fa-solid fa-phone"></i> Phone</span>
                    <span class="val">{{ $user->phone_number ?? '+91-9876543210' }}</span>
                </div>
                <div class="profile-info-row">
                    <span class="label"><i class="fa-solid fa-location-dot"></i> Location</span>
                    <span class="val">{{ $user->address ?? 'Plant Head Office' }}</span>
                </div>
                <div class="profile-info-row">
                    <span class="label"><i class="fa-solid fa-clock-rotate-left"></i> Last Login</span>
                    <span class="val text-muted small">{{ $user->logs['last_login_at'] ?? now()->format('d M Y, h:i A') }}</span>
                </div>

                <!-- Security Note -->
                <div class="mt-3 p-2.5 rounded-3" style="background: var(--bg-input); border: 1px dashed var(--border-color); font-size: 0.75rem; color: var(--text-muted);">
                    <i class="fa-solid fa-shield-halved text-success me-1"></i> Logged in via <strong>{{ $user->logs['last_login_ip'] ?? '127.0.0.1' }}</strong>. Audit trail active.
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Recent Live Plant Gate & Weighbridge Transactions Table -->
    <div class="plant-table-card mb-4">
        <div class="card-header-flex p-3 px-4 border-bottom mb-0" style="border-color: var(--border-color) !important;">
            <div>
                <h3 class="fs-6 m-0 fw-bold"><i class="fa-solid fa-list-check me-2 text-muted"></i>Recent Live Plant Vehicle Transactions</h3>
                <p class="text-muted small mb-0">Real-time gate inward, weighbridge gross/tare and unloading log</p>
            </div>
            <a href="#weighbridge" class="btn btn-sm btn-outline-secondary px-2.5 py-1" style="font-size: 0.75rem; color: var(--text-heading); border-color: var(--border-color);">
                View All <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Slip ID</th>
                        <th>Vehicle Number</th>
                        <th>Material / Grade</th>
                        <th>Party / Supplier</th>
                        <th>Gross Weight</th>
                        <th>Tare Weight</th>
                        <th>Net Weight</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold" style="color: var(--text-heading);">#WB-2026-089</td>
                        <td class="fw-bold" style="color: var(--text-heading);">CG-04-MB-4812</td>
                        <td>Heavy Melting Scrap (HMS 1&2)</td>
                        <td>Raipur Ferrous Traders</td>
                        <td>42,850 kg</td>
                        <td>14,120 kg</td>
                        <td class="fw-bold" style="color: var(--text-heading);">28,730 kg</td>
                        <td><span class="badge-status badge-status-verified"><i class="fa-solid fa-circle-check"></i> Unloaded</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="color: var(--text-heading);">#WB-2026-090</td>
                        <td class="fw-bold" style="color: var(--text-heading);">MH-12-PQ-9934</td>
                        <td>HR Strip Coil (IS 2062)</td>
                        <td>Bhushan Steel Coil Yard</td>
                        <td>54,200 kg</td>
                        <td>16,450 kg</td>
                        <td class="fw-bold" style="color: var(--text-heading);">37,750 kg</td>
                        <td><span class="badge-status badge-status-gross"><i class="fa-solid fa-clock"></i> In Weighing</span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold" style="color: var(--text-heading);">#WB-2026-091</td>
                        <td class="fw-bold" style="color: var(--text-heading);">OD-14-K-2219</td>
                        <td>ERW Round Pipes (Class B)</td>
                        <td>Tata Projects Infrastructure</td>
                        <td>36,100 kg</td>
                        <td>12,300 kg</td>
                        <td class="fw-bold" style="color: var(--text-heading);">23,800 kg</td>
                        <td><span class="badge-status badge-status-inward"><i class="fa-solid fa-truck-ramp-box"></i> Loading Out</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let trendChart = null;
            let doughnutChart = null;

            function initCharts(isDark) {
                if (typeof Chart === 'undefined') {
                    return;
                }

                try {
                    const trendEl = document.getElementById('productionTrendChart');
                    const doughnutEl = document.getElementById('inventoryDoughnutChart');

                    if (trendEl) {
                        const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(11, 18, 32, 0.06)';
                        const labelColor = isDark ? '#8493A8' : '#64748B';

                        const ctx1 = trendEl.getContext('2d');
                        const gradientRed = ctx1.createLinearGradient(0, 0, 0, 260);
                        gradientRed.addColorStop(0, 'rgba(229, 45, 63, 0.25)');
                        gradientRed.addColorStop(1, 'rgba(229, 45, 63, 0.0)');

                        const gradientBlue = ctx1.createLinearGradient(0, 0, 0, 260);
                        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
                        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                        trendChart = new Chart(ctx1, {
                            type: 'line',
                            data: {
                                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                                datasets: [
                                    {
                                        label: 'Scrap Inward (MT)',
                                        data: [1120, 1340, 1280, 1540, 1490, 1680, 1480],
                                        borderColor: '#E52D3F',
                                        backgroundColor: gradientRed,
                                        borderWidth: 2.5,
                                        fill: true,
                                        tension: 0.4,
                                        pointBackgroundColor: '#E52D3F',
                                        pointRadius: 4,
                                    },
                                    {
                                        label: 'ERW Pipes Output (MT)',
                                        data: [980, 1190, 1150, 1380, 1320, 1510, 1390],
                                        borderColor: '#3B82F6',
                                        backgroundColor: gradientBlue,
                                        borderWidth: 2.5,
                                        fill: true,
                                        tension: 0.4,
                                        pointBackgroundColor: '#3B82F6',
                                        pointRadius: 4,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        labels: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 11 } }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: { color: gridColor },
                                        ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 11 } }
                                    },
                                    y: {
                                        grid: { color: gridColor },
                                        ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 11 } }
                                    }
                                }
                            }
                        });
                    }

                    if (doughnutEl) {
                        const ctx2 = doughnutEl.getContext('2d');
                        doughnutChart = new Chart(ctx2, {
                            type: 'doughnut',
                            data: {
                                labels: ['Scrap Billets', 'HR Coils', 'ERW Pipes'],
                                datasets: [{
                                    data: [42, 33, 25],
                                    backgroundColor: ['#E52D3F', '#3B82F6', '#10B981'],
                                    borderColor: isDark ? '#10192A' : '#FFFFFF',
                                    borderWidth: 3,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                cutout: '72%'
                            }
                        });
                    }
                } catch (chartErr) {
                    console.warn('Chart render warning:', chartErr);
                }
            }

            window.updateChartsTheme = function (theme) {
                try {
                    if (trendChart && typeof trendChart.destroy === 'function') {
                        trendChart.destroy();
                        trendChart = null;
                    }
                    if (doughnutChart && typeof doughnutChart.destroy === 'function') {
                        doughnutChart.destroy();
                        doughnutChart = null;
                    }
                    initCharts(theme === 'dark');
                } catch (e) {
                    console.warn('Chart theme update warning:', e);
                }
            };

            // Initialize charts with current active theme
            const currentActiveTheme = document.documentElement.getAttribute('data-theme') || 'light';
            initCharts(currentActiveTheme === 'dark');
        });
    </script>
@endpush
