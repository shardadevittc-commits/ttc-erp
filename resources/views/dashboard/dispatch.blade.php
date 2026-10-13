@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name']) . ' Dashboard | TTC Robotronics - Dispatch ERP')

@section('content')

    {{-- =========================================================
        1. DISPATCH HERO
    ========================================================== --}}
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

                        <i class="fa-solid fa-truck-fast me-1 text-muted"></i>

                        DISPATCH & LOGISTICS

                    </span>


                    <span class="badge border px-2.5 py-1 fs-7"
                          style="
                            background: var(--accent-green-subtle);
                            color: var(--accent-green);
                            border-color: var(--accent-green-border) !important;
                          ">

                        <i class="fa-solid fa-circle-check me-1"></i>

                        DISPATCH ONLINE

                    </span>

                </div>


                <h2 class="hero-title">

                    Welcome back,
                    {{ auth()->user()->first_name ?? 'Dispatch User' }}! 👋

                </h2>


                <p class="hero-subtitle">

                    Centralized Dispatch & Logistics Control Panel.
                    Monitor today's dispatch, pending orders, vehicles in transit,
                    delivery challans, e-way bills and dispatch performance.

                </p>

            </div>


            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">

                <a href="#new-dispatch"
                   class="btn btn-sm text-white px-3 py-2 fw-semibold rounded-3 shadow-sm me-2"
                   style="
                        background-color: var(--primary-red);
                        border-color: var(--primary-red);
                   ">

                    <i class="fa-solid fa-plus me-1"></i>

                    New Dispatch

                </a>


                <a href="#dispatch-orders"
                   class="btn btn-sm px-3 py-2 fw-semibold rounded-3"
                   style="
                        color: var(--text-heading);
                        background: var(--bg-card);
                        border: 1px solid var(--border-color);
                   ">

                    <i class="fa-solid fa-file-lines me-1"></i>

                    Dispatch Orders

                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
        2. DISPATCH KPI CARDS
    ========================================================== --}}
    <div class="row g-3 mb-4">


        {{-- TODAY DISPATCH --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-red">

                        <i class="fa-solid fa-truck-ramp-box"></i>

                    </div>


                    <span class="kpi-trend-badge trend-up">

                        <i class="fa-solid fa-arrow-trend-up"></i>

                        +14.6%

                    </span>

                </div>


                <div>

                    <div class="kpi-title">
                        Today's Dispatch
                    </div>


                    <div class="kpi-value-row">

                        <div class="kpi-value">

                            186.40

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
                                d="M2,27 C12,24 18,17 29,20 C40,23 45,10 57,14 C68,18 75,5 88,6"
                                fill="none"
                                stroke="#E52D3F"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>


                    <div class="small text-muted mt-1">

                        18 vehicles dispatched today

                    </div>

                </div>

            </div>

        </div>


        {{-- PENDING DISPATCH --}}
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
                        Pending Dispatch
                    </div>


                    <div class="kpi-value-row">

                        <div class="kpi-value">

                            428.75

                            <span style="
                                font-size: .95rem;
                                font-weight: 500;
                                color: var(--text-muted);
                            ">
                                MT
                            </span>

                        </div>


                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <rect x="2" y="15" width="7" height="19" rx="2"
                                  fill="#F59E0B" opacity=".35"/>

                            <rect x="14" y="10" width="7" height="24" rx="2"
                                  fill="#F59E0B" opacity=".45"/>

                            <rect x="26" y="18" width="7" height="16" rx="2"
                                  fill="#F59E0B" opacity=".55"/>

                            <rect x="38" y="7" width="7" height="27" rx="2"
                                  fill="#F59E0B" opacity=".65"/>

                            <rect x="50" y="13" width="7" height="21" rx="2"
                                  fill="#F59E0B" opacity=".7"/>

                            <rect x="62" y="5" width="7" height="29" rx="2"
                                  fill="#F59E0B" opacity=".85"/>

                            <rect x="74" y="9" width="7" height="25" rx="2"
                                  fill="#F59E0B"/>

                        </svg>

                    </div>


                    <div class="small text-muted mt-1">

                        27 orders awaiting dispatch

                    </div>

                </div>

            </div>

        </div>


        {{-- DISPATCH VALUE --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-green">

                        <i class="fa-solid fa-indian-rupee-sign"></i>

                    </div>


                    <span class="kpi-trend-badge trend-up">

                        <i class="fa-solid fa-arrow-trend-up"></i>

                        +9.8%

                    </span>

                </div>


                <div>

                    <div class="kpi-title">
                        Today's Dispatch Value
                    </div>


                    <div class="kpi-value-row">

                        <div class="kpi-value">

                            ₹ 42.85

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
                                d="M2,25 C14,23 20,16 31,18 C43,21 49,11 59,14 C69,17 77,5 88,4"
                                fill="none"
                                stroke="#10B981"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>


                    <div class="small text-muted mt-1">

                        18 dispatch invoices generated

                    </div>

                </div>

            </div>

        </div>


        {{-- VEHICLES --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-blue">

                        <i class="fa-solid fa-truck"></i>

                    </div>


                    <span class="kpi-trend-badge trend-neutral">

                        <i class="fa-solid fa-road"></i>

                        In Transit

                    </span>

                </div>


                <div>

                    <div class="kpi-title">
                        Vehicles In Transit
                    </div>


                    <div class="kpi-value-row">

                        <div class="kpi-value">

                            12

                        </div>


                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <path
                                d="M2,8 C15,10 22,15 34,12 C48,9 55,20 66,18 C76,16 82,25 88,27"
                                fill="none"
                                stroke="#3B82F6"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>


                    <div class="small text-muted mt-1">

                        12 vehicles currently on route

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        3. DISPATCH GRAPH + PRODUCT MIX
    ========================================================== --}}
    <div class="row g-3 mb-4">


        {{-- DISPATCH TREND --}}
        <div class="col-12 col-xl-8">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Dispatch Quantity Trend
                        </h3>

                        <p class="text-muted small mb-0">
                            Steel dispatch quantity performance
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

                    <canvas id="dispatchQuantityChart"></canvas>

                </div>

            </div>

        </div>


        {{-- PRODUCT MIX --}}
        <div class="col-12 col-xl-4">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Dispatch Product Mix
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

                    <canvas id="dispatchProductChart"></canvas>

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

                            48%

                        </div>

                    </div>


                    <div>

                        <div class="small text-muted">
                            HR Coils
                        </div>

                        <div
                            class="fw-bold"
                            style="color: var(--text-heading);">

                            29%

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
        4. DISPATCH OPERATIONS + TARGET
    ========================================================== --}}
    <div class="row g-4 mb-4">


        {{-- DISPATCH MODULES --}}
        <div class="col-12 col-lg-8">

            <div
                class="d-flex justify-content-between align-items-center mb-3">

                <h3 class="fs-5 m-0 fw-bold">

                    <i class="fa-solid fa-truck-fast me-2 text-muted"></i>

                    Dispatch Operations

                </h3>


                <span class="text-muted small">

                    Current FY:

                    <strong id="fyText"></strong>

                </span>

            </div>


            <div class="row g-3">


                {{-- NEW DISPATCH --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#new-dispatch"
                        class="module-tile">

                        <div class="module-icon-box">

                            <i class="fa-solid fa-truck-ramp-box"></i>

                        </div>


                        <div>

                            <h4>
                                New Dispatch
                            </h4>

                            <p>
                                Create dispatch entries for customer
                                orders and ready steel material.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- PENDING DISPATCH --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#dispatch-orders"
                        class="module-tile">

                        <div class="module-icon-box">

                            <i class="fa-solid fa-hourglass-half"></i>

                        </div>


                        <div>

                            <h4>
                                Pending Dispatch
                            </h4>

                            <p>
                                Manage pending orders, balance quantity
                                and scheduled delivery.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- DELIVERY CHALLAN --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#delivery-challan"
                        class="module-tile">

                        <div class="module-icon-box">

                            <i class="fa-solid fa-file-invoice"></i>

                        </div>


                        <div>

                            <h4>
                                Delivery Challan
                            </h4>

                            <p>
                                Generate and manage delivery challans
                                for every vehicle dispatch.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- E-WAY BILL --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#eway-bill"
                        class="module-tile">

                        <div class="module-icon-box">

                            <i class="fa-solid fa-receipt"></i>

                        </div>


                        <div>

                            <h4>
                                E-Way Bills
                            </h4>

                            <p>
                                Track e-way bill numbers, validity,
                                generation and expiry status.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- VEHICLES --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#vehicles"
                        class="module-tile">

                        <div class="module-icon-box">

                            <i class="fa-solid fa-truck"></i>

                        </div>


                        <div>

                            <h4>
                                Vehicles
                            </h4>

                            <p>
                                Manage vehicle numbers, drivers,
                                transporters and dispatch routes.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- DISPATCH REPORTS --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#dispatch-reports"
                        class="module-tile">

                        <div class="module-icon-box">

                            <i class="fa-solid fa-chart-column"></i>

                        </div>


                        <div>

                            <h4>
                                Dispatch Reports
                            </h4>

                            <p>
                                Analyse customer-wise, product-wise,
                                vehicle-wise and monthly dispatch.
                            </p>

                        </div>

                    </a>

                </div>

            </div>

        </div>


        {{-- DISPATCH TARGET --}}
        <div class="col-12 col-lg-4">

            <div
                class="d-flex justify-content-between align-items-center mb-3">

                <h3 class="fs-5 m-0 fw-bold">

                    <i class="fa-solid fa-bullseye me-2 text-muted"></i>

                    Dispatch Target

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
                        Financial Year Dispatch Target
                    </div>


                    <div
                        class="fw-bold"
                        style="
                            font-size: 2rem;
                            color: var(--text-heading);
                        ">

                        4,800 MT

                    </div>


                    <div class="small text-muted">
                        Target Dispatch
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
                            width: 68%;
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

                        3,264 MT

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

                                420 MT

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

                                386 MT

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        5. RECENT DISPATCH TRANSACTIONS
    ========================================================== --}}
    <div class="plant-table-card mb-4">

        <div
            class="card-header-flex p-3 px-4 border-bottom mb-0"
            style="
                border-color: var(--border-color) !important;
            ">


            <div>

                <h3 class="fs-6 m-0 fw-bold">

                    <i class="fa-solid fa-truck-fast me-2 text-muted"></i>

                    Recent Dispatch Transactions

                </h3>


                <p class="text-muted small mb-0">

                    Latest vehicle dispatch, challan and delivery status

                </p>

            </div>


            <a
                href="#dispatch"
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

                        <th>Dispatch No.</th>

                        <th>Customer</th>

                        <th>Product</th>

                        <th>Quantity</th>

                        <th>Vehicle</th>

                        <th>Challan</th>

                        <th>E-Way Bill</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>


                    {{-- ROW 1 --}}
                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">

                            #DSP-2026-089

                        </td>


                        <td class="fw-semibold">
                            Tata Projects
                        </td>


                        <td>
                            ERW Round Pipes
                        </td>


                        <td>
                            23.80 MT
                        </td>


                        <td>
                            PB-13-AB-4521
                        </td>


                        <td>
                            DC-089
                        </td>


                        <td>
                            181234567890
                        </td>


                        <td>

                            <span class="badge-status badge-status-verified">

                                <i class="fa-solid fa-circle-check"></i>

                                Delivered

                            </span>

                        </td>

                    </tr>


                    {{-- ROW 2 --}}
                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">

                            #DSP-2026-090

                        </td>


                        <td class="fw-semibold">
                            Bhushan Steel
                        </td>


                        <td>
                            HR Strip Coil
                        </td>


                        <td>
                            37.75 MT
                        </td>


                        <td>
                            CG-04-MN-7821
                        </td>


                        <td>
                            DC-090
                        </td>


                        <td>
                            181234567891
                        </td>


                        <td>

                            <span class="badge-status badge-status-gross">

                                <i class="fa-solid fa-truck"></i>

                                In Transit

                            </span>

                        </td>

                    </tr>


                    {{-- ROW 3 --}}
                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">

                            #DSP-2026-091

                        </td>


                        <td class="fw-semibold">
                            Raipur Ferrous Traders
                        </td>


                        <td>
                            MS Billets
                        </td>


                        <td>
                            28.73 MT
                        </td>


                        <td>
                            CG-04-KL-2248
                        </td>


                        <td>
                            DC-091
                        </td>


                        <td>
                            Pending
                        </td>


                        <td>

                            <span class="badge-status badge-status-inward">

                                <i class="fa-solid fa-clock"></i>

                                E-Way Pending

                            </span>

                        </td>

                    </tr>


                    {{-- ROW 4 --}}
                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">

                            #DSP-2026-092

                        </td>


                        <td class="fw-semibold">
                            Shivam Infrastructure
                        </td>


                        <td>
                            ERW Square Pipes
                        </td>


                        <td>
                            18.20 MT
                        </td>


                        <td>
                            PB-10-CD-9981
                        </td>


                        <td>
                            DC-092
                        </td>


                        <td>
                            181234567893
                        </td>


                        <td>

                            <span class="badge-status badge-status-verified">

                                <i class="fa-solid fa-circle-check"></i>

                                Delivered

                            </span>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
        6. PENDING DISPATCH ORDERS
    ========================================================== --}}
    <div
        id="dispatch-orders"
        class="plant-table-card mb-4">


        <div
            class="card-header-flex p-3 px-4 border-bottom mb-0"
            style="
                border-color: var(--border-color) !important;
            ">


            <div>

                <h3 class="fs-6 m-0 fw-bold">

                    <i class="fa-solid fa-hourglass-half me-2 text-muted"></i>

                    Pending Dispatch Orders

                </h3>


                <p class="text-muted small mb-0">

                    Orders requiring vehicle allocation,
                    loading or dispatch

                </p>

            </div>


            <span
                class="badge border px-2 py-1"
                style="
                    background: var(--accent-amber-subtle);
                    color: var(--accent-amber);
                    border-color: var(--accent-amber-border) !important;
                ">

                27 Pending Orders

            </span>

        </div>


        <div class="table-responsive">

            <table class="table table-custom">

                <thead>

                    <tr>

                        <th>Order No.</th>

                        <th>Customer</th>

                        <th>Product</th>

                        <th>Ordered</th>

                        <th>Dispatched</th>

                        <th>Balance</th>

                        <th>Vehicle</th>

                        <th>Due</th>

                    </tr>

                </thead>


                <tbody>


                    <tr>

                        <td class="fw-bold">
                            SO-2026-041
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
                            Not Allocated
                        </td>


                        <td>
                            02 Oct 2026
                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold">
                            SO-2026-044
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
                            CG-04-MN-7821
                        </td>


                        <td>
                            05 Oct 2026
                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold">
                            SO-2026-047
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
                            07 Oct 2026
                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold">
                            SO-2026-050
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
                            PB-10-CD-9981
                        </td>


                        <td>
                            10 Oct 2026
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


    let dispatchChart = null;

    let productChart = null;


    /* =========================================================
       FINANCIAL YEAR
       Indian FY:
       April 1 -> March 31
       ========================================================== */

    function getFinancialYear(date = new Date()) {

        const year =
            date.getFullYear();

        const month =
            date.getMonth() + 1;


        if (month >= 4) {

            return `${year}-${String(year + 1).slice(-2)}`;

        }


        return `${year - 1}-${String(year).slice(-2)}`;

    }


    const currentFY =
        getFinancialYear();


    /* =========================================================
       UPDATE FY LABELS
       ========================================================== */

    const fyElements = [

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


    fyElements.forEach(function (element) {

        if (element) {

            element.textContent =
                currentFY;

        }

    });


    /* =========================================================
       INITIALIZE CHARTS
       ========================================================== */

    function initDispatchCharts(isDark = false) {


        if (typeof Chart === 'undefined') {

            console.warn(
                'Chart.js is not loaded.'
            );

            return;

        }


        /* Destroy previous charts */

        if (dispatchChart) {

            dispatchChart.destroy();

            dispatchChart = null;

        }


        if (productChart) {

            productChart.destroy();

            productChart = null;

        }


        const dispatchCanvas =
            document.getElementById(
                'dispatchQuantityChart'
            );


        const productCanvas =
            document.getElementById(
                'dispatchProductChart'
            );


        if (!dispatchCanvas || !productCanvas) {

            return;

        }


        /* =====================================================
           THEME COLORS
           ===================================================== */

        const gridColor =
            isDark
                ? 'rgba(255,255,255,.05)'
                : 'rgba(11,18,32,.06)';


        const labelColor =
            isDark
                ? '#8493A8'
                : '#64748B';


        /* =====================================================
           DISPATCH LINE CHART
           ===================================================== */

        const ctx =
            dispatchCanvas.getContext('2d');


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


        dispatchChart = new Chart(ctx, {


            type: 'line',


            data: {

                labels: [

                    '23 Sep',
                    '24 Sep',
                    '25 Sep',
                    '26 Sep',
                    '27 Sep',
                    '28 Sep',
                    '29 Sep'

                ],


                datasets: [

                    {

                        label:
                            'Dispatch Quantity',

                        data: [

                            132.40,
                            158.20,
                            146.80,
                            172.50,
                            164.30,
                            198.70,
                            186.40

                        ],


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
           ===================================================== */

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

                                48,
                                29,
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
       CHART DATA
       ========================================================== */

    const dispatchData = {


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

                132.40,
                158.20,
                146.80,
                172.50,
                164.30,
                198.70,
                186.40

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

                280,
                620,
                980,
                1420,
                1880,
                2310,
                2685

            ]

        }

    };


    /* =========================================================
       FINANCIAL YEAR CHART DATA
       ========================================================== */

    function getFinancialYearChartData() {


        const today =
            new Date();


        const currentYear =
            today.getFullYear();


        const currentMonth =
            today.getMonth() + 1;


        const startYear =
            currentMonth >= 4
                ? currentYear
                : currentYear - 1;


        const shortStart =
            String(startYear)
                .slice(-2);


        const shortEnd =
            String(startYear + 1)
                .slice(-2);


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
         * Static demo values.
         * Replace with Laravel/database data later.
         */

        const values = [

            680,
            920,
            1180,
            1460,
            1750,
            2045,
            0,
            0,
            0,
            0,
            0,
            0

        ];


        return {

            labels:
                labels,

            values:
                values

        };

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


                    if (!dispatchChart) {

                        return;

                    }


                    /* 7 DAYS */

                    if (
                        period === '7days'
                    ) {

                        dispatchChart
                            .data
                            .labels =
                            dispatchData
                                .sevenDays
                                .labels;


                        dispatchChart
                            .data
                            .datasets[0]
                            .data =
                            dispatchData
                                .sevenDays
                                .values;

                    }


                    /* MONTH */

                    if (
                        period === 'month'
                    ) {

                        dispatchChart
                            .data
                            .labels =
                            dispatchData
                                .month
                                .labels;


                        dispatchChart
                            .data
                            .datasets[0]
                            .data =
                            dispatchData
                                .month
                                .values;

                    }


                    /* FINANCIAL YEAR */

                    if (
                        period === 'year'
                    ) {

                        const fyData =
                            getFinancialYearChartData();


                        dispatchChart
                            .data
                            .labels =
                            fyData.labels;


                        dispatchChart
                            .data
                            .datasets[0]
                            .data =
                            fyData.values;

                    }


                    dispatchChart.update();

                }
            );

        });


    /* =========================================================
       INITIALIZE
       ========================================================== */

    const currentTheme =
        document.documentElement
            .getAttribute(
                'data-theme'
            ) ||
        'light';


    initDispatchCharts(
        currentTheme === 'dark'
    );


    /* =========================================================
       THEME SWITCH SUPPORT
       ========================================================== */

    window.updateDispatchChartsTheme =
        function (theme) {

            initDispatchCharts(
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
