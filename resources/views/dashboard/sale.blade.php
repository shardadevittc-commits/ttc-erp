@extends('layouts.app')

@section('title', 'Sales Dashboard | TTC Robotronics - Steel Industry ERP')

@section('content')

    {{-- =========================================================
        1. SALES HERO
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
                        <i class="fa-solid fa-chart-line me-1 text-muted"></i>
                        SALES & DISPATCH
                    </span>

                    <span class="badge border px-2.5 py-1 fs-7"
                          style="
                            background: var(--accent-green-subtle);
                            color: var(--accent-green);
                            border-color: var(--accent-green-border) !important;
                          ">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        SALES ONLINE
                    </span>

                </div>

                <h2 class="hero-title">
                    Welcome back, {{ $user->first_name ?? 'Sales User' }}! 👋
                </h2>

                <p class="hero-subtitle">
                    Centralized Sales & Dispatch Control Panel. Monitor today's sales,
                    pending orders, collections, customer outstanding, dispatch value
                    and financial-year sales performance.
                </p>

            </div>


            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">

                <a href="#new-sale"
                   class="btn btn-sm text-white px-3 py-2 fw-semibold rounded-3 shadow-sm me-2"
                   style="
                        background-color: var(--primary-red);
                        border-color: var(--primary-red);
                   ">
                    <i class="fa-solid fa-plus me-1"></i>
                    New Sale
                </a>

                <a href="#sales-orders"
                   class="btn btn-sm px-3 py-2 fw-semibold rounded-3"
                   style="
                        color: var(--text-heading);
                        background: var(--bg-card);
                        border: 1px solid var(--border-color);
                   ">
                    <i class="fa-solid fa-file-lines me-1"></i>
                    Sales Orders
                </a>

            </div>

        </div>
    </div>


    {{-- =========================================================
        2. SALES KPI CARDS
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- TODAY SALE --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-red">
                        <i class="fa-solid fa-indian-rupee-sign"></i>
                    </div>

                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        +12.8%
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Today's Sale
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 28.65
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
                                d="M2,28 C14,25 18,17 30,20 C42,23 45,8 58,13 C70,18 74,4 88,5"
                                fill="none"
                                stroke="#E52D3F"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />
                        </svg>

                    </div>

                    <div class="small text-muted mt-1">
                        18 invoices generated today
                    </div>

                </div>

            </div>

        </div>


        {{-- PENDING SALE --}}
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
                        Pending Sale
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 74.20
                            <span style="
                                font-size: .95rem;
                                font-weight: 500;
                                color: var(--text-muted);
                            ">
                                L
                            </span>
                        </div>

                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <rect x="2" y="16" width="7" height="18" rx="2"
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
                        27 sales orders awaiting dispatch
                    </div>

                </div>

            </div>

        </div>


        {{-- TODAY COLLECTION --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-green">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>

                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        +8.4%
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Today's Collection
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 19.80
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
                        12 customer payments received
                    </div>

                </div>

            </div>

        </div>


        {{-- OUTSTANDING --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-blue">
                        <i class="fa-solid fa-wallet"></i>
                    </div>

                    <span class="kpi-trend-badge trend-neutral">
                        <i class="fa-solid fa-wallet"></i>
                        Receivable
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Customer Outstanding
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 3.84
                            <span style="
                                font-size: .95rem;
                                font-weight: 500;
                                color: var(--text-muted);
                            ">
                                Cr
                            </span>
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
                        46 customers outstanding
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        3. SALES GRAPH + PRODUCT MIX
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- SALES AMOUNT GRAPH --}}
        <div class="col-12 col-xl-8">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Sales Amount Trend
                        </h3>

                        <p class="text-muted small mb-0">
                            Steel sales revenue performance
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
                    <canvas id="salesAmountChart"></canvas>
                </div>

            </div>

        </div>


        {{-- PRODUCT MIX --}}
        <div class="col-12 col-xl-4">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Sales Product Mix
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

                    <canvas id="salesProductChart"></canvas>

                </div>


                <div
                    class="d-flex justify-content-around text-center pt-3 border-top mt-2"
                    style="border-color: var(--border-color) !important;">

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
        4. SALES OPERATIONS + TARGET
    ========================================================== --}}
    <div class="row g-4 mb-4">


        {{-- SALES MODULES --}}
        <div class="col-12 col-lg-8">

            <div
                class="d-flex justify-content-between align-items-center mb-3">

                <h3 class="fs-5 m-0 fw-bold">

                    <i class="fa-solid fa-cart-shopping me-2 text-muted"></i>

                    Sales Operations

                </h3>

                <span class="text-muted small">

                    Current FY:

                    <strong id="fyText"></strong>

                </span>

            </div>


            <div class="row g-3">


                {{-- NEW SALE --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#new-sale"
                        class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </div>

                        <div>

                            <h4>
                                New Sale Invoice
                            </h4>

                            <p>
                                Create sales invoices for ERW pipes,
                                coils, billets and finished steel products.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- SALES ORDERS --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#sales-orders"
                        class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>

                        <div>

                            <h4>
                                Sales Orders
                            </h4>

                            <p>
                                Manage customer orders, pending quantities,
                                rates and delivery schedules.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- CUSTOMERS --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#customers"
                        class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-users"></i>
                        </div>

                        <div>

                            <h4>
                                Customers
                            </h4>

                            <p>
                                Customer master, GST details,
                                credit limits and payment history.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- DISPATCH --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#dispatch"
                        class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>

                        <div>

                            <h4>
                                Dispatch & Challan
                            </h4>

                            <p>
                                Manage loading, delivery challans,
                                e-way bills and vehicle dispatch.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- RECEIVABLE --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#receivable"
                        class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-money-check-dollar"></i>
                        </div>

                        <div>

                            <h4>
                                Receivables
                            </h4>

                            <p>
                                Track customer outstanding,
                                due dates and collections.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- SALES REPORTS --}}
                <div class="col-12 col-md-6">

                    <a
                        href="#sales-reports"
                        class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-chart-column"></i>
                        </div>

                        <div>

                            <h4>
                                Sales Reports
                            </h4>

                            <p>
                                Product-wise, customer-wise,
                                monthly and yearly sales analysis.
                            </p>

                        </div>

                    </a>

                </div>

            </div>

        </div>


        {{-- SALES TARGET --}}
        <div class="col-12 col-lg-4">

            <div
                class="d-flex justify-content-between align-items-center mb-3">

                <h3 class="fs-5 m-0 fw-bold">

                    <i class="fa-solid fa-bullseye me-2 text-muted"></i>

                    Sales Target

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
                        Financial Year Target
                    </div>

                    <div
                        class="fw-bold"
                        style="
                            font-size: 2rem;
                            color: var(--text-heading);
                        ">
                        ₹ 42.00 Cr
                    </div>

                    <div class="small text-muted">
                        Target Sales
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
                        ₹ 28.56 Cr
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
                                ₹ 3.50 Cr
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
                                ₹ 2.85 Cr
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        5. RECENT SALES TRANSACTIONS
    ========================================================== --}}
    <div class="plant-table-card mb-4">

        <div
            class="card-header-flex p-3 px-4 border-bottom mb-0"
            style="border-color: var(--border-color) !important;">

            <div>

                <h3 class="fs-6 m-0 fw-bold">

                    <i class="fa-solid fa-receipt me-2 text-muted"></i>

                    Recent Sales Transactions

                </h3>

                <p class="text-muted small mb-0">
                    Latest customer invoices, dispatch and payment status
                </p>

            </div>


            <a
                href="#sales"
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

                        <th>Invoice</th>

                        <th>Customer</th>

                        <th>Product</th>

                        <th>Quantity</th>

                        <th>Invoice Value</th>

                        <th>Dispatch</th>

                        <th>Payment</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>


                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            #INV-2026-089
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

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            ₹ 18.42 L
                        </td>

                        <td>
                            100%
                        </td>

                        <td>
                            Received
                        </td>

                        <td>

                            <span class="badge-status badge-status-verified">

                                <i class="fa-solid fa-circle-check"></i>

                                Completed

                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            #INV-2026-090
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

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            ₹ 24.85 L
                        </td>

                        <td>
                            60%
                        </td>

                        <td>
                            Pending
                        </td>

                        <td>

                            <span class="badge-status badge-status-gross">

                                <i class="fa-solid fa-clock"></i>

                                Partial

                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            #INV-2026-091
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

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            ₹ 16.74 L
                        </td>

                        <td>
                            0%
                        </td>

                        <td>
                            Pending
                        </td>

                        <td>

                            <span class="badge-status badge-status-inward">

                                <i class="fa-solid fa-hourglass-half"></i>

                                Awaiting Dispatch

                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            #INV-2026-092
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

                        <td
                            class="fw-bold"
                            style="color: var(--text-heading);">
                            ₹ 12.90 L
                        </td>

                        <td>
                            100%
                        </td>

                        <td>
                            Received
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
        6. PENDING SALES ORDERS
    ========================================================== --}}
    <div
        id="sales-orders"
        class="plant-table-card mb-4">

        <div
            class="card-header-flex p-3 px-4 border-bottom mb-0"
            style="border-color: var(--border-color) !important;">

            <div>

                <h3 class="fs-6 m-0 fw-bold">

                    <i class="fa-solid fa-hourglass-half me-2 text-muted"></i>

                    Pending Sales Orders

                </h3>

                <p class="text-muted small mb-0">
                    Orders requiring production, allocation or dispatch
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

                        <th>Order Value</th>

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
                            ₹ 86.40 L
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
                            ₹ 54.20 L
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
                            ₹ 38.70 L
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
                            ₹ 67.35 L
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

    let salesChart = null;
    let productChart = null;


    /* =========================================================
       FINANCIAL YEAR

       Indian Financial Year:
       April 1 -> March 31

       Example:
       Jan 2026 = FY 2025-26
       Apr 2026 = FY 2026-27
       ========================================================= */

    function getFinancialYear(date = new Date()) {

        const year = date.getFullYear();

        const month = date.getMonth() + 1;

        if (month >= 4) {

            return `${year}-${String(year + 1).slice(-2)}`;

        }

        return `${year - 1}-${String(year).slice(-2)}`;

    }


    const currentFY = getFinancialYear();


    /* Update FY everywhere */

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
       CHART INIT
       ========================================================= */

    function initSalesCharts(isDark = false) {

        if (typeof Chart === 'undefined') {

            console.warn(
                'Chart.js is not loaded.'
            );

            return;

        }


        /* Destroy old charts */

        if (salesChart) {

            salesChart.destroy();

            salesChart = null;

        }


        if (productChart) {

            productChart.destroy();

            productChart = null;

        }


        const salesCanvas =
            document.getElementById('salesAmountChart');


        const productCanvas =
            document.getElementById('salesProductChart');


        if (!salesCanvas || !productCanvas) {

            return;

        }


        /* Theme colors */

        const gridColor = isDark
            ? 'rgba(255,255,255,.05)'
            : 'rgba(11,18,32,.06)';


        const labelColor = isDark
            ? '#8493A8'
            : '#64748B';


        /* =====================================================
           SALES GRAPH
           ===================================================== */

        const ctx =
            salesCanvas.getContext('2d');


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


        salesChart = new Chart(ctx, {

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

                        label: 'Sales Amount',

                        data: [

                            18.40,
                            22.80,
                            19.50,
                            27.30,
                            24.70,
                            31.20,
                            28.65

                        ],

                        borderColor: '#E52D3F',

                        backgroundColor: gradient,

                        borderWidth: 2.5,

                        fill: true,

                        tension: .4,

                        pointRadius: 4,

                        pointHoverRadius: 6,

                        pointBackgroundColor: '#E52D3F',

                        pointBorderWidth: 0

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,


                interaction: {

                    intersect: false,

                    mode: 'index'

                },


                plugins: {

                    legend: {

                        display: false

                    },


                    tooltip: {

                        backgroundColor:
                            isDark
                                ? '#172033'
                                : '#111827',

                        titleColor: '#FFFFFF',

                        bodyColor: '#FFFFFF',

                        padding: 12,

                        displayColors: false,


                        callbacks: {

                            label: function (context) {

                                return ' ₹ ' +
                                    Number(context.raw)
                                        .toFixed(2) +
                                    ' L';

                            }

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

                            color: labelColor,

                            font: {

                                family:
                                    'Plus Jakarta Sans',

                                size: 11

                            }

                        }

                    },


                    y: {

                        beginAtZero: true,


                        grid: {

                            color: gridColor,

                            drawBorder: false

                        },


                        ticks: {

                            color: labelColor,

                            font: {

                                family:
                                    'Plus Jakarta Sans',

                                size: 11

                            },


                            callback: function (value) {

                                return '₹ ' +
                                    value +
                                    'L';

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


        productChart = new Chart(productCtx, {

            type: 'doughnut',


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


                        borderWidth: 3

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '72%',


                plugins: {

                    legend: {

                        display: false

                    },


                    tooltip: {

                        callbacks: {

                            label: function (context) {

                                return context.label +
                                    ': ' +
                                    context.raw +
                                    '%';

                            }

                        }

                    }

                }

            }

        });

    }


    /* =========================================================
       GRAPH DATA
       ========================================================= */

    const salesData = {

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

                18.40,
                22.80,
                19.50,
                27.30,
                24.70,
                31.20,
                28.65

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

                32,
                78,
                125,
                176,
                219,
                260,
                285

            ]

        }

    };


    /* =========================================================
       FINANCIAL YEAR DATA

       Automatically generates:
       Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, Dec,
       Jan, Feb, Mar

       Future months are zero in this static demo.
       ========================================================= */

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


        const labels = [

            `Apr ${String(startYear).slice(-2)}`,

            `May ${String(startYear).slice(-2)}`,

            `Jun ${String(startYear).slice(-2)}`,

            `Jul ${String(startYear).slice(-2)}`,

            `Aug ${String(startYear).slice(-2)}`,

            `Sep ${String(startYear).slice(-2)}`,

            `Oct ${String(startYear + 1).slice(-2)}`,

            `Nov ${String(startYear + 1).slice(-2)}`,

            `Dec ${String(startYear + 1).slice(-2)}`,

            `Jan ${String(startYear + 1).slice(-2)}`,

            `Feb ${String(startYear + 1).slice(-2)}`,

            `Mar ${String(startYear + 1).slice(-2)}`

        ];


        /*
         * Static demo values.
         *
         * In production these should come from Laravel.
         */

        const values = [

            180,
            215,
            245,
            290,
            320,
            356,
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
       FILTER BUTTONS
       ========================================================= */

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
                        .forEach(function (btn) {

                            btn.classList.remove(
                                'active'
                            );

                        });


                    this.classList.add(
                        'active'
                    );


                    const period =
                        this.dataset.period;


                    if (!salesChart) {

                        return;

                    }


                    /* 7 DAYS */

                    if (period === '7days') {

                        salesChart.data.labels =
                            salesData.sevenDays.labels;


                        salesChart.data.datasets[0].data =
                            salesData.sevenDays.values;

                    }


                    /* MONTH */

                    if (period === 'month') {

                        salesChart.data.labels =
                            salesData.month.labels;


                        salesChart.data.datasets[0].data =
                            salesData.month.values;

                    }


                    /* FINANCIAL YEAR */

                    if (period === 'year') {

                        const fyData =
                            getFinancialYearChartData();


                        salesChart.data.labels =
                            fyData.labels;


                        salesChart.data.datasets[0].data =
                            fyData.values;

                    }


                    salesChart.update();

                }
            );

        });


    /* =========================================================
       INITIALIZE CHARTS
       ========================================================= */

    const currentTheme =
        document.documentElement
            .getAttribute('data-theme') ||
        'light';


    initSalesCharts(
        currentTheme === 'dark'
    );


    /* =========================================================
       THEME SWITCH SUPPORT
       ========================================================= */

    window.updateSalesChartsTheme =
        function (theme) {

            initSalesCharts(
                theme === 'dark'
            );

        };


    /* =========================================================
       OPTIONAL AUTO REFRESH

       Recalculate FY label after midnight/page refresh.
       ========================================================= */

    setInterval(function () {

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


        elements.forEach(function (element) {

            if (element) {

                element.textContent =
                    latestFY;

            }

        });

    }, 60000);


});

</script>

@endpush
