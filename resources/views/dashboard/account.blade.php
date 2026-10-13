@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name']) . ' Dashboard | TTC Robotronics - Accounts & Finance ERP')

@section('content')

    {{-- =========================================================
        1. ACCOUNTS HERO
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
                        <i class="fa-solid fa-calculator me-1 text-muted"></i>
                        ACCOUNTS & FINANCE
                    </span>

                    <span class="badge border px-2.5 py-1 fs-7"
                          style="
                            background: var(--accent-green-subtle);
                            color: var(--accent-green);
                            border-color: var(--accent-green-border) !important;
                          ">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        BOOKS UP TO DATE
                    </span>

                </div>

                <h2 class="hero-title">
                    Welcome back, {{ auth()->user()->first_name ?? 'Accounts User' }}! 👋
                </h2>

                <p class="hero-subtitle">
                    Centralized Accounts & Finance Control Panel. Monitor cash flow,
                    receivables, payables, bank balances, expenses, GST/TDS liabilities
                    and financial-year profitability from one place.
                </p>

            </div>


            <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">

                <a href="#new-voucher"
                   class="btn btn-sm text-white px-3 py-2 fw-semibold rounded-3 shadow-sm me-2"
                   style="
                        background-color: var(--primary-red);
                        border-color: var(--primary-red);
                   ">
                    <i class="fa-solid fa-plus me-1"></i>
                    New Voucher
                </a>

                <a href="#accounts-reports"
                   class="btn btn-sm px-3 py-2 fw-semibold rounded-3"
                   style="
                        color: var(--text-heading);
                        background: var(--bg-card);
                        border: 1px solid var(--border-color);
                   ">
                    <i class="fa-solid fa-file-lines me-1"></i>
                    Reports
                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
        2. FINANCIAL KPI CARDS
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- CASH & BANK --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-green">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>

                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        +6.4%
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Cash & Bank Balance
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 1.86
                            <span style="
                                font-size:.95rem;
                                font-weight:500;
                                color:var(--text-muted);
                            ">
                                Cr
                            </span>
                        </div>

                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <path
                                d="M2,27 C14,25 19,18 30,21 C42,24 48,12 58,16 C69,20 77,7 88,5"
                                fill="none"
                                stroke="#10B981"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>

                    <div class="small text-muted mt-1">
                        8 active bank & cash accounts
                    </div>

                </div>

            </div>

        </div>


        {{-- RECEIVABLE --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-blue">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>

                    <span class="kpi-trend-badge trend-neutral">
                        <i class="fa-solid fa-clock"></i>
                        Receivable
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Trade Receivables
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 3.84
                            <span style="
                                font-size:.95rem;
                                font-weight:500;
                                color:var(--text-muted);
                            ">
                                Cr
                            </span>
                        </div>

                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <path
                                d="M2,8 C14,10 20,15 31,12 C44,9 52,19 63,17 C74,15 80,23 88,27"
                                fill="none"
                                stroke="#3B82F6"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>

                    <div class="small text-muted mt-1">
                        46 customers with outstanding
                    </div>

                </div>

            </div>

        </div>


        {{-- PAYABLE --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-amber">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>

                    <span class="kpi-trend-badge trend-neutral">
                        <i class="fa-solid fa-hourglass-half"></i>
                        Payable
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Trade Payables
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 2.17
                            <span style="
                                font-size:.95rem;
                                font-weight:500;
                                color:var(--text-muted);
                            ">
                                Cr
                            </span>
                        </div>

                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <path
                                d="M2,25 C15,22 21,19 31,21 C42,24 48,13 59,15 C70,18 78,9 88,8"
                                fill="none"
                                stroke="#F59E0B"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>

                    <div class="small text-muted mt-1">
                        31 supplier accounts pending
                    </div>

                </div>

            </div>

        </div>


        {{-- NET PROFIT --}}
        <div class="col-12 col-sm-6 col-xl-3">

            <div class="kpi-metric-card">

                <div class="kpi-top-row">

                    <div class="kpi-icon-pill kpi-icon-red">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>

                    <span class="kpi-trend-badge trend-up">
                        <i class="fa-solid fa-arrow-trend-up"></i>
                        +9.2%
                    </span>

                </div>

                <div>

                    <div class="kpi-title">
                        Net Profit
                    </div>

                    <div class="kpi-value-row">

                        <div class="kpi-value">
                            ₹ 6.42
                            <span style="
                                font-size:.95rem;
                                font-weight:500;
                                color:var(--text-muted);
                            ">
                                Cr
                            </span>
                        </div>

                        <svg class="sparkline-svg" viewBox="0 0 90 34">

                            <path
                                d="M2,28 C13,25 20,23 31,20 C43,18 49,14 59,15 C70,16 77,7 88,4"
                                fill="none"
                                stroke="#E52D3F"
                                stroke-width="2.5"
                                stroke-linecap="round"
                            />

                        </svg>

                    </div>

                    <div class="small text-muted mt-1">
                        Current financial year
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        3. FINANCIAL TREND + PROFIT & LOSS
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- CASH FLOW --}}
        <div class="col-12 col-xl-8">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Financial Performance
                        </h3>

                        <p class="text-muted small mb-0">
                            Revenue, expenses and net profit
                        </p>

                    </div>


                    <div class="chart-filter-pills">

                        <button
                            type="button"
                            class="filter-btn active">
                            Monthly
                        </button>

                        <button
                            type="button"
                            class="filter-btn">
                            Quarterly
                        </button>

                        <button
                            type="button"
                            class="filter-btn">
                            FY
                        </button>

                    </div>

                </div>


                <div style="
                    height:290px;
                    position:relative;
                ">

                    <canvas id="accountsPerformanceChart"></canvas>

                </div>

            </div>

        </div>


        {{-- P&L --}}
        <div class="col-12 col-xl-4">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            P&L Snapshot
                        </h3>

                        <p class="text-muted small mb-0">
                            Current financial year
                        </p>

                    </div>

                    <span
                        class="badge border fs-7"
                        style="
                            background:var(--accent-green-subtle);
                            color:var(--accent-green);
                            border-color:var(--accent-green-border) !important;
                        ">
                        FY 2026-27
                    </span>

                </div>


                <div class="py-3">

                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted small">
                            Revenue
                        </span>

                        <strong style="color:var(--text-heading);">
                            ₹ 28.56 Cr
                        </strong>

                    </div>


                    <div class="progress mb-3"
                         style="
                            height:7px;
                            background:var(--bg-input);
                         ">

                        <div
                            class="progress-bar"
                            style="
                                width:82%;
                                background:#3B82F6;
                            ">
                        </div>

                    </div>


                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted small">
                            Cost of Goods
                        </span>

                        <strong style="color:var(--text-heading);">
                            ₹ 18.72 Cr
                        </strong>

                    </div>


                    <div class="progress mb-3"
                         style="
                            height:7px;
                            background:var(--bg-input);
                         ">

                        <div
                            class="progress-bar"
                            style="
                                width:65%;
                                background:#F59E0B;
                            ">
                        </div>

                    </div>


                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted small">
                            Operating Expenses
                        </span>

                        <strong style="color:var(--text-heading);">
                            ₹ 3.42 Cr
                        </strong>

                    </div>


                    <div class="progress mb-3"
                         style="
                            height:7px;
                            background:var(--bg-input);
                         ">

                        <div
                            class="progress-bar"
                            style="
                                width:30%;
                                background:#8B5CF6;
                            ">
                        </div>

                    </div>


                    <div
                        class="p-3 rounded-3 mt-3"
                        style="
                            background:var(--accent-green-subtle);
                            border:1px solid var(--accent-green-border);
                        ">

                        <div class="small text-muted">
                            Net Profit
                        </div>

                        <div
                            class="fw-bold fs-4 mt-1"
                            style="color:var(--accent-green);">
                            ₹ 6.42 Cr
                        </div>

                        <div class="small mt-1"
                             style="color:var(--accent-green);">
                            Profit Margin: 22.5%
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        4. ACCOUNTING OPERATIONS
    ========================================================== --}}
    <div class="row g-4 mb-4">

        <div class="col-12 col-lg-8">

            <div
                class="d-flex justify-content-between align-items-center mb-3">

                <h3 class="fs-5 m-0 fw-bold">

                    <i class="fa-solid fa-calculator me-2 text-muted"></i>

                    Accounting Operations

                </h3>

                <span class="text-muted small">
                    Current FY:
                    <strong>2026-27</strong>
                </span>

            </div>


            <div class="row g-3">


                {{-- VOUCHERS --}}
                <div class="col-12 col-md-6">

                    <a href="#new-voucher"
                       class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>

                        <div>

                            <h4>
                                Accounting Vouchers
                            </h4>

                            <p>
                                Create journal, payment, receipt,
                                contra and adjustment vouchers.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- RECEIVABLE --}}
                <div class="col-12 col-md-6">

                    <a href="#receivables"
                       class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>

                        <div>

                            <h4>
                                Receivables
                            </h4>

                            <p>
                                Monitor customer dues, ageing,
                                collections and payment follow-up.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- PAYABLE --}}
                <div class="col-12 col-md-6">

                    <a href="#payables"
                       class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>

                        <div>

                            <h4>
                                Payables
                            </h4>

                            <p>
                                Manage supplier bills, due dates,
                                payment schedules and outstanding.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- BANK --}}
                <div class="col-12 col-md-6">

                    <a href="#banking"
                       class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>

                        <div>

                            <h4>
                                Bank & Cash
                            </h4>

                            <p>
                                Bank accounts, cash book,
                                reconciliation and transfers.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- TAX --}}
                <div class="col-12 col-md-6">

                    <a href="#taxation"
                       class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>

                        <div>

                            <h4>
                                GST & TDS
                            </h4>

                            <p>
                                Track GST input/output, TDS,
                                tax payable and compliance data.
                            </p>

                        </div>

                    </a>

                </div>


                {{-- REPORTS --}}
                <div class="col-12 col-md-6">

                    <a href="#accounts-reports"
                       class="module-tile">

                        <div class="module-icon-box">
                            <i class="fa-solid fa-chart-column"></i>
                        </div>

                        <div>

                            <h4>
                                Financial Reports
                            </h4>

                            <p>
                                Balance sheet, P&L, trial balance,
                                ledgers and financial analysis.
                            </p>

                        </div>

                    </a>

                </div>

            </div>

        </div>


        {{-- TAX / DUE SUMMARY --}}
        <div class="col-12 col-lg-4">

            <div
                class="d-flex justify-content-between align-items-center mb-3">

                <h3 class="fs-5 m-0 fw-bold">

                    <i class="fa-solid fa-calendar-check me-2 text-muted"></i>

                    Upcoming Dues

                </h3>

                <span
                    class="badge border px-2 py-1 fs-7"
                    style="
                        background:var(--accent-amber-subtle);
                        color:var(--accent-amber);
                        border-color:var(--accent-amber-border) !important;
                    ">
                    6 Pending
                </span>

            </div>


            <div class="chart-box-card">

                <div
                    class="d-flex justify-content-between align-items-center
                           p-3 rounded-3 mb-2"
                    style="background:var(--bg-input);">

                    <div>

                        <div class="small text-muted">
                            GST Payable
                        </div>

                        <div class="fw-bold mt-1">
                            ₹ 18.42 L
                        </div>

                    </div>

                    <span
                        class="badge"
                        style="
                            background:var(--accent-amber-subtle);
                            color:var(--accent-amber);
                        ">
                        10 Oct
                    </span>

                </div>


                <div
                    class="d-flex justify-content-between align-items-center
                           p-3 rounded-3 mb-2"
                    style="background:var(--bg-input);">

                    <div>

                        <div class="small text-muted">
                            TDS Payable
                        </div>

                        <div class="fw-bold mt-1">
                            ₹ 4.86 L
                        </div>

                    </div>

                    <span
                        class="badge"
                        style="
                            background:var(--accent-red-subtle);
                            color:var(--primary-red);
                        ">
                        07 Oct
                    </span>

                </div>


                <div
                    class="d-flex justify-content-between align-items-center
                           p-3 rounded-3"
                    style="background:var(--bg-input);">

                    <div>

                        <div class="small text-muted">
                            Supplier Payments
                        </div>

                        <div class="fw-bold mt-1">
                            ₹ 31.70 L
                        </div>

                    </div>

                    <span
                        class="badge"
                        style="
                            background:var(--accent-blue-subtle);
                            color:var(--accent-blue);
                        ">
                        12 Oct
                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        5. RECEIVABLE / PAYABLE AGEING
    ========================================================== --}}
    <div class="row g-3 mb-4">

        {{-- RECEIVABLE AGEING --}}
        <div class="col-12 col-xl-6">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Receivable Ageing
                        </h3>

                        <p class="text-muted small mb-0">
                            Customer outstanding ageing
                        </p>

                    </div>

                    <span
                        class="badge border fs-7"
                        style="
                            background:var(--accent-blue-subtle);
                            color:var(--accent-blue);
                            border-color:var(--accent-blue-border) !important;
                        ">
                        ₹ 3.84 Cr
                    </span>

                </div>


                <div class="mt-3">

                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                0 - 30 Days
                            </span>

                            <strong>
                                ₹ 1.82 Cr
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:47%;
                                    background:#10B981;
                                ">
                            </div>

                        </div>

                    </div>


                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                31 - 60 Days
                            </span>

                            <strong>
                                ₹ 1.04 Cr
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:27%;
                                    background:#3B82F6;
                                ">
                            </div>

                        </div>

                    </div>


                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                61 - 90 Days
                            </span>

                            <strong>
                                ₹ 62.00 L
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:16%;
                                    background:#F59E0B;
                                ">
                            </div>

                        </div>

                    </div>


                    <div>

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                90+ Days
                            </span>

                            <strong>
                                ₹ 36.00 L
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:10%;
                                    background:#E52D3F;
                                ">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- PAYABLE AGEING --}}
        <div class="col-12 col-xl-6">

            <div class="chart-box-card">

                <div class="card-header-flex">

                    <div>

                        <h3>
                            Payable Ageing
                        </h3>

                        <p class="text-muted small mb-0">
                            Supplier outstanding ageing
                        </p>

                    </div>

                    <span
                        class="badge border fs-7"
                        style="
                            background:var(--accent-amber-subtle);
                            color:var(--accent-amber);
                            border-color:var(--accent-amber-border) !important;
                        ">
                        ₹ 2.17 Cr
                    </span>

                </div>


                <div class="mt-3">

                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                0 - 30 Days
                            </span>

                            <strong>
                                ₹ 1.12 Cr
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:52%;
                                    background:#10B981;
                                ">
                            </div>

                        </div>

                    </div>


                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                31 - 60 Days
                            </span>

                            <strong>
                                ₹ 58.00 L
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:27%;
                                    background:#3B82F6;
                                ">
                            </div>

                        </div>

                    </div>


                    <div class="mb-3">

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                61 - 90 Days
                            </span>

                            <strong>
                                ₹ 31.00 L
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:14%;
                                    background:#F59E0B;
                                ">
                            </div>

                        </div>

                    </div>


                    <div>

                        <div class="d-flex justify-content-between mb-1">

                            <span class="small text-muted">
                                90+ Days
                            </span>

                            <strong>
                                ₹ 16.00 L
                            </strong>

                        </div>

                        <div class="progress"
                             style="height:8px;background:var(--bg-input);">

                            <div
                                class="progress-bar"
                                style="
                                    width:7%;
                                    background:#E52D3F;
                                ">
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        6. RECENT ACCOUNT TRANSACTIONS
    ========================================================== --}}
    <div class="plant-table-card mb-4">

        <div
            class="card-header-flex p-3 px-4 border-bottom mb-0"
            style="border-color:var(--border-color) !important;">

            <div>

                <h3 class="fs-6 m-0 fw-bold">

                    <i class="fa-solid fa-receipt me-2 text-muted"></i>

                    Recent Account Transactions

                </h3>

                <p class="text-muted small mb-0">
                    Latest receipts, payments, journal and bank transactions
                </p>

            </div>


            <a
                href="#transactions"
                class="btn btn-sm btn-outline-secondary px-2.5 py-1"
                style="
                    font-size:.75rem;
                    color:var(--text-heading);
                    border-color:var(--border-color);
                ">

                View All

                <i class="fa-solid fa-arrow-right ms-1"></i>

            </a>

        </div>


        <div class="table-responsive">

            <table class="table table-custom">

                <thead>

                    <tr>

                        <th>Voucher</th>

                        <th>Date</th>

                        <th>Account</th>

                        <th>Type</th>

                        <th>Reference</th>

                        <th>Amount</th>

                        <th>Mode</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>


                    <tr>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            #RV-2026-089
                        </td>

                        <td>
                            29 Sep 2026
                        </td>

                        <td class="fw-semibold">
                            Tata Projects
                        </td>

                        <td>
                            Receipt
                        </td>

                        <td>
                            INV-2026-089
                        </td>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            ₹ 18.42 L
                        </td>

                        <td>
                            Bank
                        </td>

                        <td>

                            <span class="badge-status badge-status-verified">

                                <i class="fa-solid fa-circle-check"></i>

                                Posted

                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            #PV-2026-112
                        </td>

                        <td>
                            29 Sep 2026
                        </td>

                        <td class="fw-semibold">
                            JSW Steel Ltd.
                        </td>

                        <td>
                            Payment
                        </td>

                        <td>
                            BILL-4582
                        </td>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            ₹ 24.80 L
                        </td>

                        <td>
                            Bank
                        </td>

                        <td>

                            <span class="badge-status badge-status-verified">

                                <i class="fa-solid fa-circle-check"></i>

                                Posted

                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            #JV-2026-074
                        </td>

                        <td>
                            28 Sep 2026
                        </td>

                        <td class="fw-semibold">
                            Freight Expenses
                        </td>

                        <td>
                            Journal
                        </td>

                        <td>
                            EXP-0926
                        </td>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            ₹ 4.72 L
                        </td>

                        <td>
                            Journal
                        </td>

                        <td>

                            <span class="badge-status badge-status-gross">

                                <i class="fa-solid fa-clock"></i>

                                Review

                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            #BR-2026-031
                        </td>

                        <td>
                            28 Sep 2026
                        </td>

                        <td class="fw-semibold">
                            HDFC Bank
                        </td>

                        <td>
                            Bank Receipt
                        </td>

                        <td>
                            NEFT-92871
                        </td>

                        <td class="fw-bold"
                            style="color:var(--text-heading);">
                            ₹ 12.50 L
                        </td>

                        <td>
                            NEFT
                        </td>

                        <td>

                            <span class="badge-status badge-status-verified">

                                <i class="fa-solid fa-circle-check"></i>

                                Reconciled

                            </span>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>

    </div>


    {{-- =========================================================
        7. PENDING ACCOUNTING ITEMS
    ========================================================== --}}
    <div
        id="accounts-reports"
        class="plant-table-card mb-4">

        <div
            class="card-header-flex p-3 px-4 border-bottom mb-0"
            style="border-color:var(--border-color) !important;">

            <div>

                <h3 class="fs-6 m-0 fw-bold">

                    <i class="fa-solid fa-list-check me-2 text-muted"></i>

                    Pending Accounting Items

                </h3>

                <p class="text-muted small mb-0">
                    Items requiring review, approval or reconciliation
                </p>

            </div>


            <span
                class="badge border px-2 py-1"
                style="
                    background:var(--accent-amber-subtle);
                    color:var(--accent-amber);
                    border-color:var(--accent-amber-border) !important;
                ">

                14 Pending

            </span>

        </div>


        <div class="table-responsive">

            <table class="table table-custom">

                <thead>

                    <tr>

                        <th>Reference</th>

                        <th>Party / Account</th>

                        <th>Category</th>

                        <th>Amount</th>

                        <th>Due Date</th>

                        <th>Assigned To</th>

                        <th>Priority</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>


                    <tr>

                        <td class="fw-bold">
                            BILL-2026-4582
                        </td>

                        <td>
                            JSW Steel Ltd.
                        </td>

                        <td>
                            Supplier Bill
                        </td>

                        <td class="fw-bold">
                            ₹ 24.80 L
                        </td>

                        <td>
                            02 Oct 2026
                        </td>

                        <td>
                            Accounts Payable
                        </td>

                        <td>

                            <span
                                class="badge"
                                style="
                                    background:var(--accent-red-subtle);
                                    color:var(--primary-red);
                                ">
                                High
                            </span>

                        </td>

                        <td>

                            <span class="badge-status badge-status-gross">
                                <i class="fa-solid fa-clock"></i>
                                Pending
                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold">
                            REC-2026-118
                        </td>

                        <td>
                            Shivam Infrastructure
                        </td>

                        <td>
                            Customer Receipt
                        </td>

                        <td class="fw-bold">
                            ₹ 8.40 L
                        </td>

                        <td>
                            01 Oct 2026
                        </td>

                        <td>
                            Accounts Receivable
                        </td>

                        <td>

                            <span
                                class="badge"
                                style="
                                    background:var(--accent-amber-subtle);
                                    color:var(--accent-amber);
                                ">
                                Medium
                            </span>

                        </td>

                        <td>

                            <span class="badge-status badge-status-inward">
                                <i class="fa-solid fa-hourglass-half"></i>
                                Follow-up
                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold">
                            BANK-0926
                        </td>

                        <td>
                            HDFC Bank
                        </td>

                        <td>
                            Bank Reconciliation
                        </td>

                        <td class="fw-bold">
                            ₹ 3.18 L
                        </td>

                        <td>
                            30 Sep 2026
                        </td>

                        <td>
                            Finance Team
                        </td>

                        <td>

                            <span
                                class="badge"
                                style="
                                    background:var(--accent-blue-subtle);
                                    color:var(--accent-blue);
                                ">
                                Normal
                            </span>

                        </td>

                        <td>

                            <span class="badge-status badge-status-gross">
                                <i class="fa-solid fa-clock"></i>
                                Reconcile
                            </span>

                        </td>

                    </tr>


                    <tr>

                        <td class="fw-bold">
                            GST-0926
                        </td>

                        <td>
                            GST Output Liability
                        </td>

                        <td>
                            GST
                        </td>

                        <td class="fw-bold">
                            ₹ 18.42 L
                        </td>

                        <td>
                            10 Oct 2026
                        </td>

                        <td>
                            Taxation
                        </td>

                        <td>

                            <span
                                class="badge"
                                style="
                                    background:var(--accent-red-subtle);
                                    color:var(--primary-red);
                                ">
                                High
                            </span>

                        </td>

                        <td>

                            <span class="badge-status badge-status-inward">
                                <i class="fa-solid fa-hourglass-half"></i>
                                Due
                            </span>

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

    let accountsChart = null;


    /* =========================================================
       FINANCIAL YEAR
    ========================================================== */

    function getFinancialYear(date = new Date()) {

        const year = date.getFullYear();

        const month = date.getMonth() + 1;

        if (month >= 4) {

            return `${year}-${String(year + 1).slice(-2)}`;

        }

        return `${year - 1}-${String(year).slice(-2)}`;

    }


    /* =========================================================
       ACCOUNTS PERFORMANCE CHART
    ========================================================== */

    function initAccountsChart(isDark = false) {

        if (typeof Chart === 'undefined') {

            console.warn('Chart.js is not loaded.');

            return;

        }


        if (accountsChart) {

            accountsChart.destroy();

            accountsChart = null;

        }


        const canvas =
            document.getElementById(
                'accountsPerformanceChart'
            );


        if (!canvas) {

            return;

        }


        const ctx =
            canvas.getContext('2d');


        const gridColor = isDark
            ? 'rgba(255,255,255,.05)'
            : 'rgba(11,18,32,.06)';


        const labelColor = isDark
            ? '#8493A8'
            : '#64748B';


        const profitGradient =
            ctx.createLinearGradient(
                0,
                0,
                0,
                280
            );


        profitGradient.addColorStop(
            0,
            'rgba(16,185,129,.20)'
        );


        profitGradient.addColorStop(
            1,
            'rgba(16,185,129,0)'
        );


        accountsChart = new Chart(ctx, {

            type: 'line',

            data: {

                labels: [

                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep'

                ],

                datasets: [

                    {

                        label: 'Revenue',

                        data: [

                            4.20,
                            4.65,
                            5.10,
                            4.82,
                            5.20,
                            4.59

                        ],

                        borderColor: '#3B82F6',

                        backgroundColor: 'transparent',

                        borderWidth: 2.5,

                        tension: .4,

                        pointRadius: 3,

                        pointBackgroundColor: '#3B82F6'

                    },


                    {

                        label: 'Expenses',

                        data: [

                            3.10,
                            3.28,
                            3.46,
                            3.12,
                            3.40,
                            3.21

                        ],

                        borderColor: '#F59E0B',

                        backgroundColor: 'transparent',

                        borderWidth: 2.5,

                        tension: .4,

                        pointRadius: 3,

                        pointBackgroundColor: '#F59E0B'

                    },


                    {

                        label: 'Net Profit',

                        data: [

                            1.10,
                            1.37,
                            1.64,
                            1.70,
                            1.80,
                            1.38

                        ],

                        borderColor: '#10B981',

                        backgroundColor: profitGradient,

                        borderWidth: 2.5,

                        fill: true,

                        tension: .4,

                        pointRadius: 3,

                        pointBackgroundColor: '#10B981'

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

                        position: 'bottom',

                        labels: {

                            color: labelColor,

                            usePointStyle: true,

                            padding: 18,

                            font: {

                                family:
                                    'Plus Jakarta Sans',

                                size: 11

                            }

                        }

                    },


                    tooltip: {

                        backgroundColor:
                            isDark
                                ? '#172033'
                                : '#111827',

                        titleColor: '#FFFFFF',

                        bodyColor: '#FFFFFF',

                        padding: 12,

                        callbacks: {

                            label: function (context) {

                                return ' ' +
                                    context.dataset.label +
                                    ': ₹ ' +
                                    Number(context.raw)
                                        .toFixed(2) +
                                    ' Cr';

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

                            callback: function (value) {

                                return '₹ ' +
                                    value +
                                    ' Cr';

                            },

                            font: {

                                family:
                                    'Plus Jakarta Sans',

                                size: 11

                            }

                        }

                    }

                }

            }

        });

    }


    /* =========================================================
       INITIALIZE
    ========================================================== */

    const currentTheme =
        document.documentElement
            .getAttribute('data-theme') ||
        'light';


    initAccountsChart(
        currentTheme === 'dark'
    );


    /* =========================================================
       THEME SWITCH SUPPORT
    ========================================================== */

    window.updateAccountsChartsTheme =
        function (theme) {

            initAccountsChart(
                theme === 'dark'
            );

        };


    /* =========================================================
       FY AUTO REFRESH
    ========================================================== */

    setInterval(function () {

        const latestFY =
            getFinancialYear();

        console.log(
            'Current Financial Year:',
            latestFY
        );

    }, 60000);


});

</script>

@endpush
