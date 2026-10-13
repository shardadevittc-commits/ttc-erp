@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name']) . ' Dashboard | TTC Robotronics - Production ERP')

@section('content')

{{-- =========================================================
    1. PRODUCTION HERO
========================================================= --}}
<div class="dashboard-hero-card mb-4">

    <div class="row align-items-center">

        <div class="col-12 col-lg-8">

            <div class="d-flex align-items-center gap-2 mb-2">

                <span class="badge border px-2.5 py-1 fs-7"
                      style="
                        background: var(--bg-input);
                        color: var(--text-heading);
                        border-color: var(--border-color) !important;
                      ">

                    <i class="fa-solid fa-industry me-1 text-muted"></i>

                    PRODUCTION & MANUFACTURING

                </span>

                <span class="badge border px-2.5 py-1 fs-7"
                      style="
                        background: var(--accent-green-subtle);
                        color: var(--accent-green);
                        border-color: var(--accent-green-border) !important;
                      ">

                    <i class="fa-solid fa-circle-check me-1"></i>

                    PRODUCTION ONLINE

                </span>

            </div>

            <h2 class="hero-title">

                Welcome back,
                {{ auth()->user()->first_name ?? 'Production User' }}! 👋

            </h2>

            <p class="hero-subtitle">

                Centralized Production & Manufacturing Control Panel.
                Monitor today's production, machine utilization, pending
                production orders, raw material consumption, quality status,
                finished goods and production performance.

            </p>

        </div>

        <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">

            <a href="#new-production"
               class="btn btn-sm text-white px-3 py-2 fw-semibold rounded-3 shadow-sm me-2"
               style="
                    background-color: var(--primary-red);
                    border-color: var(--primary-red);
               ">

                <i class="fa-solid fa-plus me-1"></i>

                New Production

            </a>

            <a href="#production-orders"
               class="btn btn-sm px-3 py-2 fw-semibold rounded-3"
               style="
                    color: var(--text-heading);
                    background: var(--bg-card);
                    border: 1px solid var(--border-color);
               ">

                <i class="fa-solid fa-file-lines me-1"></i>

                Production Orders

            </a>

        </div>

    </div>

</div>


{{-- =========================================================
    2. PRODUCTION KPI CARDS
========================================================= --}}
<div class="row g-3 mb-4">

    {{-- TODAY PRODUCTION --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-red">

                    <i class="fa-solid fa-industry"></i>

                </div>

                <span class="kpi-trend-badge trend-up">

                    <i class="fa-solid fa-arrow-trend-up"></i>

                    +12.8%

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Today's Production
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">

                        214.60

                        <span style="
                            font-size: .95rem;
                            font-weight: 500;
                            color: var(--text-muted);
                        ">
                            MT
                        </span>

                    </div>

                    <svg class="sparkline-svg" viewBox="0 0 90 34">

                        <path
                            d="M2,27 C12,24 18,18 28,20 C39,23 45,9 56,14 C67,18 76,6 88,5"
                            fill="none"
                            stroke="#E52D3F"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />

                    </svg>

                </div>

                <div class="small text-muted mt-1">

                    6 production batches completed today

                </div>

            </div>

        </div>

    </div>


    {{-- PENDING PRODUCTION --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-amber">

                    <i class="fa-solid fa-clock"></i>

                </div>

                <span class="kpi-trend-badge trend-neutral">

                    <i class="fa-solid fa-hourglass-half"></i>

                    Pending

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Pending Production
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">

                        386.40

                        <span style="
                            font-size: .95rem;
                            font-weight: 500;
                            color: var(--text-muted);
                        ">
                            MT
                        </span>

                    </div>

                    <svg class="sparkline-svg" viewBox="0 0 90 34">

                        <rect x="2" y="16" width="7" height="18" rx="2"
                              fill="#F59E0B" opacity=".35"/>

                        <rect x="14" y="10" width="7" height="24" rx="2"
                              fill="#F59E0B" opacity=".45"/>

                        <rect x="26" y="19" width="7" height="15" rx="2"
                              fill="#F59E0B" opacity=".55"/>

                        <rect x="38" y="7" width="7" height="27" rx="2"
                              fill="#F59E0B" opacity=".65"/>

                        <rect x="50" y="13" width="7" height="21" rx="2"
                              fill="#F59E0B" opacity=".75"/>

                        <rect x="62" y="5" width="7" height="29" rx="2"
                              fill="#F59E0B" opacity=".85"/>

                        <rect x="74" y="9" width="7" height="25" rx="2"
                              fill="#F59E0B"/>

                    </svg>

                </div>

                <div class="small text-muted mt-1">

                    19 production orders awaiting processing

                </div>

            </div>

        </div>

    </div>


    {{-- PRODUCTION VALUE --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-green">

                    <i class="fa-solid fa-indian-rupee-sign"></i>

                </div>

                <span class="kpi-trend-badge trend-up">

                    <i class="fa-solid fa-arrow-trend-up"></i>

                    +8.4%

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Today's Production Value
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">

                        ₹ 38.72

                        <span style="
                            font-size: .95rem;
                            font-weight: 500;
                            color: var(--text-muted);
                        ">
                            L
                        </span>

                    </div>

                    <svg class="sparkline-svg" viewBox="0 0 90 34">

                        <path
                            d="M2,25 C13,22 20,15 31,19 C42,22 49,11 59,14 C70,18 78,7 88,4"
                            fill="none"
                            stroke="#10B981"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />

                    </svg>

                </div>

                <div class="small text-muted mt-1">

                    6 production batches valued today

                </div>

            </div>

        </div>

    </div>


    {{-- MACHINE UTILIZATION --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-blue">

                    <i class="fa-solid fa-gears"></i>

                </div>

                <span class="kpi-trend-badge trend-neutral">

                    <i class="fa-solid fa-gauge-high"></i>

                    Running

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Machine Utilization
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">

                        84.6

                        <span style="
                            font-size: .95rem;
                            font-weight: 500;
                            color: var(--text-muted);
                        ">
                            %
                        </span>

                    </div>

                    <svg class="sparkline-svg" viewBox="0 0 90 34">

                        <path
                            d="M2,10 C15,8 22,14 34,11 C47,8 54,20 66,17 C76,14 82,23 88,25"
                            fill="none"
                            stroke="#3B82F6"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />

                    </svg>

                </div>

                <div class="small text-muted mt-1">

                    11 of 13 production lines running

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    3. PRODUCTION GRAPH + PRODUCT MIX
========================================================= --}}
<div class="row g-3 mb-4">

    {{-- PRODUCTION TREND --}}
    <div class="col-12 col-xl-8">

        <div class="chart-box-card">

            <div class="card-header-flex">

                <div>

                    <h3>
                        Production Quantity Trend
                    </h3>

                    <p class="text-muted small mb-0">
                        Daily manufacturing production performance
                    </p>

                </div>

                <div class="chart-filter-pills">

                    <button
                        type="button"
                        class="filter-btn active"
                        data-period="7days">

                        7 Days

                    </button>

                    <button
                        type="button"
                        class="filter-btn"
                        data-period="month">

                        Month

                    </button>

                    <button
                        type="button"
                        class="filter-btn"
                        data-period="year">

                        Financial Year

                    </button>

                </div>

            </div>

            <div style="
                height: 290px;
                position: relative;
            ">

                <canvas id="productionQuantityChart"></canvas>

            </div>

        </div>

    </div>


    {{-- PRODUCT MIX --}}
    <div class="col-12 col-xl-4">

        <div class="chart-box-card">

            <div class="card-header-flex">

                <div>

                    <h3>
                        Production Product Mix
                    </h3>

                    <p class="text-muted small mb-0">
                        Current financial year
                    </p>

                </div>

                <span
                    class="badge border fs-7"
                    style="
                        background: var(--accent-blue-subtle);
                        color: var(--accent-blue);
                        border-color: var(--accent-blue-border) !important;
                    ">

                    FY
                    <span id="financialYearLabel"></span>

                </span>

            </div>

            <div
                style="
                    height: 205px;
                    position: relative;
                "
                class="d-flex align-items-center justify-content-center">

                <canvas id="productionProductChart"></canvas>

            </div>

            <div
                class="d-flex justify-content-around text-center pt-3 border-top mt-2"
                style="
                    border-color: var(--border-color) !important;
                ">

                <div>

                    <div class="small text-muted">
                        ERW Pipes
                    </div>

                    <div
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        46%

                    </div>

                </div>

                <div>

                    <div class="small text-muted">
                        HR Coils
                    </div>

                    <div
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        31%

                    </div>

                </div>

                <div>

                    <div class="small text-muted">
                        MS Products
                    </div>

                    <div
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        23%

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    4. PRODUCTION OPERATIONS + TARGET
========================================================= --}}
<div class="row g-4 mb-4">

    {{-- PRODUCTION MODULES --}}
    <div class="col-12 col-lg-8">

        <div
            class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="fs-5 m-0 fw-bold">

                <i class="fa-solid fa-industry me-2 text-muted"></i>

                Production Operations

            </h3>

            <span class="text-muted small">

                Current FY:

                <strong id="fyText"></strong>

            </span>

        </div>

        <div class="row g-3">

            {{-- NEW PRODUCTION --}}
            <div class="col-12 col-md-6">

                <a
                    href="#new-production"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-plus"></i>

                    </div>

                    <div>

                        <h4>
                            New Production
                        </h4>

                        <p>
                            Create production entries for finished
                            goods, coils, pipes and steel products.
                        </p>

                    </div>

                </a>

            </div>


            {{-- PRODUCTION ORDERS --}}
            <div class="col-12 col-md-6">

                <a
                    href="#production-orders"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-file-lines"></i>

                    </div>

                    <div>

                        <h4>
                            Production Orders
                        </h4>

                        <p>
                            Manage production planning, quantities,
                            priority and scheduled manufacturing.
                        </p>

                    </div>

                </a>

            </div>


            {{-- PRODUCTION PLANNING --}}
            <div class="col-12 col-md-6">

                <a
                    href="#production-planning"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-calendar-days"></i>

                    </div>

                    <div>

                        <h4>
                            Production Planning
                        </h4>

                        <p>
                            Plan daily, weekly and monthly production
                            according to demand and capacity.
                        </p>

                    </div>

                </a>

            </div>


            {{-- MACHINES --}}
            <div class="col-12 col-md-6">

                <a
                    href="#machines"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-gears"></i>

                    </div>

                    <div>

                        <h4>
                            Machines & Lines
                        </h4>

                        <p>
                            Monitor machines, production lines,
                            utilization and downtime.
                        </p>

                    </div>

                </a>

            </div>


            {{-- RAW MATERIAL --}}
            <div class="col-12 col-md-6">

                <a
                    href="#raw-material"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-boxes-stacked"></i>

                    </div>

                    <div>

                        <h4>
                            Raw Material
                        </h4>

                        <p>
                            Track coils, billets, scrap, consumption
                            and material availability.
                        </p>

                    </div>

                </a>

            </div>


            {{-- QUALITY --}}
            <div class="col-12 col-md-6">

                <a
                    href="#quality"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-shield-check"></i>

                    </div>

                    <div>

                        <h4>
                            Quality Control
                        </h4>

                        <p>
                            Monitor inspection, rejected quantity,
                            quality checks and batch approval.
                        </p>

                    </div>

                </a>

            </div>

        </div>

    </div>


    {{-- PRODUCTION TARGET --}}
    <div class="col-12 col-lg-4">

        <div
            class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="fs-5 m-0 fw-bold">

                <i class="fa-solid fa-bullseye me-2 text-muted"></i>

                Production Target

            </h3>

            <span
                class="badge border px-2 py-1 fs-7"
                style="
                    background: var(--accent-green-subtle);
                    color: var(--accent-green);
                    border-color: var(--accent-green-border) !important;
                ">

                FY
                <span id="targetFyLabel"></span>

            </span>

        </div>

        <div class="chart-box-card">

            <div class="text-center py-2">

                <div class="small text-muted mb-2">
                    Financial Year Production Target
                </div>

                <div
                    class="fw-bold"
                    style="
                        font-size: 2rem;
                        color: var(--text-heading);
                    ">

                    5,400 MT

                </div>

                <div class="small text-muted">
                    Target Production
                </div>

            </div>

            <div
                class="progress mt-3"
                style="
                    height: 10px;
                    background: var(--bg-input);
                ">

                <div
                    class="progress-bar"
                    role="progressbar"
                    style="
                        width: 72%;
                        background: var(--primary-red);
                    ">

                </div>

            </div>

            <div
                class="d-flex justify-content-between mt-2">

                <span class="small text-muted">
                    Achieved
                </span>

                <strong style="color: var(--text-heading);">

                    3,888 MT

                </strong>

            </div>

            <div class="row g-2 mt-3">

                <div class="col-6">

                    <div
                        class="p-2 rounded-3"
                        style="
                            background: var(--bg-input);
                        ">

                        <div class="small text-muted">
                            Monthly Target
                        </div>

                        <div
                            class="fw-bold mt-1"
                            style="color: var(--text-heading);">

                            450 MT

                        </div>

                    </div>

                </div>

                <div class="col-6">

                    <div
                        class="p-2 rounded-3"
                        style="
                            background: var(--bg-input);
                        ">

                        <div class="small text-muted">
                            This Month
                        </div>

                        <div
                            class="fw-bold mt-1"
                            style="color: var(--accent-green);">

                            426 MT

                        </div>

                    </div>

                </div>

            </div>

            <div
                class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top"
                style="border-color: var(--border-color) !important;">

                <span class="small text-muted">
                    Target Completion
                </span>

                <span
                    class="badge border"
                    style="
                        background: var(--accent-green-subtle);
                        color: var(--accent-green);
                        border-color: var(--accent-green-border) !important;
                    ">

                    72%

                </span>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    5. RECENT PRODUCTION BATCHES
========================================================= --}}
<div class="plant-table-card mb-4">

    <div
        class="card-header-flex p-3 px-4 border-bottom mb-0"
        style="
            border-color: var(--border-color) !important;
        ">

        <div>

            <h3 class="fs-6 m-0 fw-bold">

                <i class="fa-solid fa-industry me-2 text-muted"></i>

                Recent Production Batches

            </h3>

            <p class="text-muted small mb-0">

                Latest production batches, products and quality status

            </p>

        </div>

        <a
            href="#production"
            class="btn btn-sm btn-outline-secondary px-2.5 py-1"
            style="
                font-size: .75rem;
                color: var(--text-heading);
                border-color: var(--border-color);
            ">

            View All

            <i class="fa-solid fa-arrow-right ms-1"></i>

        </a>

    </div>

    <div class="table-responsive">

        <table class="table table-custom">

            <thead>

                <tr>

                    <th>Batch No.</th>

                    <th>Product</th>

                    <th>Grade</th>

                    <th>Quantity</th>

                    <th>Machine</th>

                    <th>Operator</th>

                    <th>Quality</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                {{-- ROW 1 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #BAT-2026-118

                    </td>

                    <td class="fw-semibold">
                        ERW Round Pipes
                    </td>

                    <td>
                        IS 1239
                    </td>

                    <td>
                        42.80 MT
                    </td>

                    <td>
                        ERW Line-01
                    </td>

                    <td>
                        Rajesh Kumar
                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Passed

                        </span>

                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Completed

                        </span>

                    </td>

                </tr>


                {{-- ROW 2 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #BAT-2026-119

                    </td>

                    <td class="fw-semibold">
                        HR Strip Coil
                    </td>

                    <td>
                        IS 2062
                    </td>

                    <td>
                        58.40 MT
                    </td>

                    <td>
                        Rolling Mill-02
                    </td>

                    <td>
                        Amit Singh
                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Passed

                        </span>

                    </td>

                    <td>

                        <span class="badge-status badge-status-gross">

                            <i class="fa-solid fa-gears"></i>

                            Running

                        </span>

                    </td>

                </tr>


                {{-- ROW 3 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #BAT-2026-120

                    </td>

                    <td class="fw-semibold">
                        MS Billets
                    </td>

                    <td>
                        IS 2830
                    </td>

                    <td>
                        36.75 MT
                    </td>

                    <td>
                        Billet Mill-01
                    </td>

                    <td>
                        Mohit Verma
                    </td>

                    <td>

                        <span class="badge-status badge-status-inward">

                            <i class="fa-solid fa-clock"></i>

                            Inspection

                        </span>

                    </td>

                    <td>

                        <span class="badge-status badge-status-inward">

                            <i class="fa-solid fa-flask"></i>

                            QC Pending

                        </span>

                    </td>

                </tr>


                {{-- ROW 4 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #BAT-2026-121

                    </td>

                    <td class="fw-semibold">
                        ERW Square Pipes
                    </td>

                    <td>
                        IS 4923
                    </td>

                    <td>
                        31.20 MT
                    </td>

                    <td>
                        ERW Line-02
                    </td>

                    <td>
                        Sandeep Yadav
                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Passed

                        </span>

                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Completed

                        </span>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
    6. PENDING PRODUCTION ORDERS
========================================================= --}}
<div
    id="production-orders"
    class="plant-table-card mb-4">

    <div
        class="card-header-flex p-3 px-4 border-bottom mb-0"
        style="
            border-color: var(--border-color) !important;
        ">

        <div>

            <h3 class="fs-6 m-0 fw-bold">

                <i class="fa-solid fa-hourglass-half me-2 text-muted"></i>

                Pending Production Orders

            </h3>

            <p class="text-muted small mb-0">

                Orders requiring production planning, material or machine allocation

            </p>

        </div>

        <span
            class="badge border px-2 py-1"
            style="
                background: var(--accent-amber-subtle);
                color: var(--accent-amber);
                border-color: var(--accent-amber-border) !important;
            ">

            19 Pending Orders

        </span>

    </div>

    <div class="table-responsive">

        <table class="table table-custom">

            <thead>

                <tr>

                    <th>Production Order</th>

                    <th>Customer</th>

                    <th>Product</th>

                    <th>Required</th>

                    <th>Produced</th>

                    <th>Balance</th>

                    <th>Machine</th>

                    <th>Due Date</th>

                </tr>

            </thead>

            <tbody>

                {{-- ORDER 1 --}}
                <tr>

                    <td class="fw-bold">
                        PO-2026-041
                    </td>

                    <td>
                        Tata Projects
                    </td>

                    <td>
                        ERW Pipes
                    </td>

                    <td>
                        120 MT
                    </td>

                    <td>
                        75 MT
                    </td>

                    <td class="fw-bold text-danger">
                        45 MT
                    </td>

                    <td>
                        ERW Line-01
                    </td>

                    <td>
                        04 Oct 2026
                    </td>

                </tr>


                {{-- ORDER 2 --}}
                <tr>

                    <td class="fw-bold">
                        PO-2026-044
                    </td>

                    <td>
                        Shivam Infrastructure
                    </td>

                    <td>
                        HR Coils
                    </td>

                    <td>
                        85 MT
                    </td>

                    <td>
                        30 MT
                    </td>

                    <td class="fw-bold text-danger">
                        55 MT
                    </td>

                    <td>
                        Rolling Mill-02
                    </td>

                    <td>
                        06 Oct 2026
                    </td>

                </tr>


                {{-- ORDER 3 --}}
                <tr>

                    <td class="fw-bold">
                        PO-2026-047
                    </td>

                    <td>
                        Raipur Ferrous Traders
                    </td>

                    <td>
                        MS Billets
                    </td>

                    <td>
                        60 MT
                    </td>

                    <td>
                        0 MT
                    </td>

                    <td class="fw-bold text-danger">
                        60 MT
                    </td>

                    <td>
                        Not Allocated
                    </td>

                    <td>
                        08 Oct 2026
                    </td>

                </tr>


                {{-- ORDER 4 --}}
                <tr>

                    <td class="fw-bold">
                        PO-2026-050
                    </td>

                    <td>
                        ABC Infrastructure Ltd.
                    </td>

                    <td>
                        ERW Square Pipes
                    </td>

                    <td>
                        95 MT
                    </td>

                    <td>
                        40 MT
                    </td>

                    <td class="fw-bold text-danger">
                        55 MT
                    </td>

                    <td>
                        ERW Line-02
                    </td>

                    <td>
                        12 Oct 2026
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>


@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    let productionChart = null;
    let productChart = null;


    /* =========================================================
       FINANCIAL YEAR
       Indian FY:
       April 1 -> March 31
    ========================================================== */

    function getFinancialYear(date = new Date()) {

        const year = date.getFullYear();

        const month = date.getMonth() + 1;

        if (month >= 4) {

            return `${year}-${String(year + 1).slice(-2)}`;

        }

        return `${year - 1}-${String(year).slice(-2)}`;

    }


    const currentFY = getFinancialYear();


    /* =========================================================
       UPDATE FY LABELS
    ========================================================== */

    const fyElements = [

        document.getElementById('financialYearLabel'),

        document.getElementById('fyText'),

        document.getElementById('targetFyLabel')

    ];


    fyElements.forEach(function (element) {

        if (element) {

            element.textContent = currentFY;

        }

    });


    /* =========================================================
       PRODUCTION CHART DATA
    ========================================================== */

    const productionData = {

        sevenDays: {

            labels: [

                '23 Sep',
                '24 Sep',
                '25 Sep',
                '26 Sep',
                '27 Sep',
                '28 Sep',
                '29 Sep'

            ],

            values: [

                168.40,
                182.60,
                195.80,
                204.30,
                188.70,
                226.40,
                214.60

            ]

        },


        month: {

            labels: [

                '01 Sep',
                '05 Sep',
                '10 Sep',
                '15 Sep',
                '20 Sep',
                '25 Sep',
                '29 Sep'

            ],

            values: [

                340,
                720,
                1080,
                1510,
                1940,
                2380,
                2860

            ]

        }

    };


    /* =========================================================
       FINANCIAL YEAR DATA
    ========================================================== */

    function getFinancialYearChartData() {

        const today = new Date();

        const currentYear = today.getFullYear();

        const currentMonth = today.getMonth() + 1;

        const startYear =
            currentMonth >= 4
                ? currentYear
                : currentYear - 1;


        const shortStart =
            String(startYear).slice(-2);

        const shortEnd =
            String(startYear + 1).slice(-2);


        const labels = [

            `Apr ${shortStart}`,
            `May ${shortStart}`,
            `Jun ${shortStart}`,
            `Jul ${shortStart}`,
            `Aug ${shortStart}`,
            `Sep ${shortStart}`,
            `Oct ${shortEnd}`,
            `Nov ${shortEnd}`,
            `Dec ${shortEnd}`,
            `Jan ${shortEnd}`,
            `Feb ${shortEnd}`,
            `Mar ${shortEnd}`

        ];


        /*
         * Demo production values.
         * Replace these values with Laravel/database data.
         */

        const values = [

            720,
            980,
            1260,
            1580,
            1910,
            2280,
            0,
            0,
            0,
            0,
            0,
            0

        ];


        return {

            labels: labels,

            values: values

        };

    }


    /* =========================================================
       INITIALIZE CHARTS
    ========================================================== */

    function initProductionCharts(isDark = false) {

        if (typeof Chart === 'undefined') {

            console.warn(
                'Chart.js is not loaded.'
            );

            return;

        }


        /* Destroy existing charts */

        if (productionChart) {

            productionChart.destroy();

            productionChart = null;

        }


        if (productChart) {

            productChart.destroy();

            productChart = null;

        }


        const productionCanvas =
            document.getElementById(
                'productionQuantityChart'
            );


        const productCanvas =
            document.getElementById(
                'productionProductChart'
            );


        if (!productionCanvas || !productCanvas) {

            return;

        }


        /* =====================================================
           THEME COLORS
        ====================================================== */

        const gridColor =
            isDark
                ? 'rgba(255,255,255,.05)'
                : 'rgba(11,18,32,.06)';


        const labelColor =
            isDark
                ? '#8493A8'
                : '#64748B';


        /* =====================================================
           PRODUCTION LINE CHART
        ====================================================== */

        const ctx =
            productionCanvas.getContext('2d');


        const gradient =
            ctx.createLinearGradient(
                0,
                0,
                0,
                280
            );


        gradient.addColorStop(
            0,
            'rgba(229,45,63,.25)'
        );


        gradient.addColorStop(
            1,
            'rgba(229,45,63,0)'
        );


        productionChart = new Chart(ctx, {

            type: 'line',

            data: {

                labels:
                    productionData
                        .sevenDays
                        .labels,

                datasets: [

                    {

                        label:
                            'Production Quantity',

                        data:
                            productionData
                                .sevenDays
                                .values,

                        borderColor:
                            '#E52D3F',

                        backgroundColor:
                            gradient,

                        borderWidth:
                            2.5,

                        fill:
                            true,

                        tension:
                            .4,

                        pointRadius:
                            4,

                        pointHoverRadius:
                            6,

                        pointBackgroundColor:
                            '#E52D3F',

                        pointBorderWidth:
                            0

                    }

                ]

            },

            options: {

                responsive:
                    true,

                maintainAspectRatio:
                    false,

                interaction: {

                    intersect:
                        false,

                    mode:
                        'index'

                },

                plugins: {

                    legend: {

                        display:
                            false

                    },

                    tooltip: {

                        backgroundColor:
                            isDark
                                ? '#172033'
                                : '#111827',

                        titleColor:
                            '#FFFFFF',

                        bodyColor:
                            '#FFFFFF',

                        padding:
                            12,

                        displayColors:
                            false,

                        callbacks: {

                            label:
                                function (context) {

                                    return ' ' +
                                        Number(
                                            context.raw
                                        ).toFixed(2) +
                                        ' MT';

                                }

                        }

                    }

                },

                scales: {

                    x: {

                        grid: {

                            color:
                                gridColor,

                            drawBorder:
                                false

                        },

                        ticks: {

                            color:
                                labelColor,

                            font: {

                                family:
                                    'Plus Jakarta Sans',

                                size:
                                    11

                            }

                        }

                    },

                    y: {

                        beginAtZero:
                            true,

                        grid: {

                            color:
                                gridColor,

                            drawBorder:
                                false

                        },

                        ticks: {

                            color:
                                labelColor,

                            font: {

                                family:
                                    'Plus Jakarta Sans',

                                size:
                                    11

                            },

                            callback:
                                function (value) {

                                    return value +
                                        ' MT';

                                }

                        }

                    }

                }

            }

        });


        /* =====================================================
           PRODUCT MIX DOUGHNUT
        ====================================================== */

        const productCtx =
            productCanvas.getContext('2d');


        productChart = new Chart(
            productCtx,
            {

                type:
                    'doughnut',

                data: {

                    labels: [

                        'ERW Pipes',

                        'HR Coils',

                        'MS Products'

                    ],

                    datasets: [

                        {

                            data: [

                                46,
                                31,
                                23

                            ],

                            backgroundColor: [

                                '#E52D3F',
                                '#3B82F6',
                                '#10B981'

                            ],

                            borderColor:
                                isDark
                                    ? '#10192A'
                                    : '#FFFFFF',

                            borderWidth:
                                3

                        }

                    ]

                },

                options: {

                    responsive:
                        true,

                    maintainAspectRatio:
                        false,

                    cutout:
                        '72%',

                    plugins: {

                        legend: {

                            display:
                                false

                        },

                        tooltip: {

                            callbacks: {

                                label:
                                    function (context) {

                                        return context.label +
                                            ': ' +
                                            context.raw +
                                            '%';

                                    }

                            }

                        }

                    }

                }

            }
        );

    }


    /* =========================================================
       FILTER BUTTONS
    ========================================================== */

    document
        .querySelectorAll(
            '.chart-filter-pills .filter-btn'
        )
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    document
                        .querySelectorAll(
                            '.chart-filter-pills .filter-btn'
                        )
                        .forEach(
                            function (btn) {

                                btn.classList.remove(
                                    'active'
                                );

                            }
                        );


                    this.classList.add(
                        'active'
                    );


                    const period =
                        this.dataset.period;


                    if (!productionChart) {

                        return;

                    }


                    /* 7 DAYS */

                    if (
                        period === '7days'
                    ) {

                        productionChart
                            .data
                            .labels =
                            productionData
                                .sevenDays
                                .labels;


                        productionChart
                            .data
                            .datasets[0]
                            .data =
                            productionData
                                .sevenDays
                                .values;

                    }


                    /* MONTH */

                    if (
                        period === 'month'
                    ) {

                        productionChart
                            .data
                            .labels =
                            productionData
                                .month
                                .labels;


                        productionChart
                            .data
                            .datasets[0]
                            .data =
                            productionData
                                .month
                                .values;

                    }


                    /* FINANCIAL YEAR */

                    if (
                        period === 'year'
                    ) {

                        const fyData =
                            getFinancialYearChartData();


                        productionChart
                            .data
                            .labels =
                            fyData.labels;


                        productionChart
                            .data
                            .datasets[0]
                            .data =
                            fyData.values;

                    }


                    productionChart.update();

                }
            );

        });


    /* =========================================================
       INITIAL THEME
    ========================================================== */

    const currentTheme =
        document.documentElement
            .getAttribute(
                'data-theme'
            ) ||
        'light';


    initProductionCharts(
        currentTheme === 'dark'
    );


    /* =========================================================
       THEME SWITCH SUPPORT
    ========================================================== */

    window.updateProductionChartsTheme =
        function (theme) {

            initProductionCharts(
                theme === 'dark'
            );

        };


    /* =========================================================
       AUTO FY REFRESH
    ========================================================== */

    setInterval(
        function () {

            const latestFY =
                getFinancialYear();


            const elements = [

                document.getElementById(
                    'financialYearLabel'
                ),

                document.getElementById(
                    'fyText'
                ),

                document.getElementById(
                    'targetFyLabel'
                )

            ];


            elements.forEach(
                function (element) {

                    if (element) {

                        element.textContent =
                            latestFY;

                    }

                }
            );

        },
        60000
    );

});

</script>

@endpush
