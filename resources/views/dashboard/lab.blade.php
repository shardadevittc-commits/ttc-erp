@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name'] ?? 'Lab') . ' Dashboard | TTC Robotronics - Laboratory ERP')

@section('content')

{{-- =========================================================
    LAB DASHBOARD
    TTC ROBOTRONICS - QUALITY CONTROL / LABORATORY
========================================================= --}}

{{-- =========================================================
    1. LAB HERO
========================================================== --}}

<div class="dashboard-hero-card mb-4">

    <div class="row align-items-center">

        <div class="col-12 col-lg-8">

            <div class="d-flex align-items-center gap-2 mb-2">

                <span class="badge border px-2 py-1 fs-7"
                      style="
                        background: var(--bg-input);
                        color: var(--text-heading);
                        border-color: var(--border-color) !important;
                      ">

                    <i class="fa-solid fa-flask me-1 text-muted"></i>

                    LABORATORY & QUALITY CONTROL

                </span>

                <span class="badge border px-2 py-1 fs-7"
                      style="
                        background: var(--accent-green-subtle);
                        color: var(--accent-green);
                        border-color: var(--accent-green-border) !important;
                      ">

                    <i class="fa-solid fa-circle-check me-1"></i>

                    LAB ONLINE

                </span>

            </div>

            <h2 class="hero-title">

                Welcome back,
                {{ auth()->user()->first_name ?? 'Lab User' }}! 👋

            </h2>

            <p class="hero-subtitle">

                Centralized Laboratory & Quality Control Control Panel.
                Monitor samples, testing activities, material quality,
                test results, pending approvals, certificates and
                laboratory performance.

            </p>

        </div>

        <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">

            <a href="#new-sample"
               class="btn btn-sm text-white px-3 py-2 fw-semibold rounded-3 shadow-sm me-2"
               style="
                    background-color: var(--primary-red);
                    border-color: var(--primary-red);
               ">

                <i class="fa-solid fa-plus me-1"></i>

                New Sample

            </a>

            <a href="#lab-tests"
               class="btn btn-sm px-3 py-2 fw-semibold rounded-3"
               style="
                    color: var(--text-heading);
                    background: var(--bg-card);
                    border: 1px solid var(--border-color);
               ">

                <i class="fa-solid fa-vials me-1"></i>

                Lab Tests

            </a>

        </div>

    </div>

</div>


{{-- =========================================================
    2. LAB KPI CARDS
========================================================== --}}

<div class="row g-3 mb-4">

    {{-- TODAY SAMPLES --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-red">

                    <i class="fa-solid fa-vial"></i>

                </div>

                <span class="kpi-trend-badge trend-up">

                    <i class="fa-solid fa-arrow-trend-up"></i>

                    +12.8%

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Today's Samples
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">
                        42
                    </div>

                    <svg class="sparkline-svg" viewBox="0 0 90 34">

                        <path
                            d="M2,27 C12,25 18,16 29,20 C40,24 45,12 57,15 C68,18 75,7 88,6"
                            fill="none"
                            stroke="#E52D3F"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />

                    </svg>

                </div>

                <div class="small text-muted mt-1">

                    42 samples received today

                </div>

            </div>

        </div>

    </div>


    {{-- TESTS IN PROGRESS --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-amber">

                    <i class="fa-solid fa-microscope"></i>

                </div>

                <span class="kpi-trend-badge trend-neutral">

                    <i class="fa-solid fa-hourglass-half"></i>

                    Running

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Tests In Progress
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">
                        68
                    </div>

                    <svg class="sparkline-svg" viewBox="0 0 90 34">

                        <rect x="2" y="15" width="7" height="19"
                              rx="2" fill="#F59E0B" opacity=".35"/>

                        <rect x="14" y="10" width="7" height="24"
                              rx="2" fill="#F59E0B" opacity=".45"/>

                        <rect x="26" y="18" width="7" height="16"
                              rx="2" fill="#F59E0B" opacity=".55"/>

                        <rect x="38" y="7" width="7" height="27"
                              rx="2" fill="#F59E0B" opacity=".65"/>

                        <rect x="50" y="13" width="7" height="21"
                              rx="2" fill="#F59E0B" opacity=".7"/>

                        <rect x="62" y="5" width="7" height="29"
                              rx="2" fill="#F59E0B" opacity=".85"/>

                        <rect x="74" y="9" width="7" height="25"
                              rx="2" fill="#F59E0B"/>

                    </svg>

                </div>

                <div class="small text-muted mt-1">

                    68 laboratory tests currently running

                </div>

            </div>

        </div>

    </div>


    {{-- COMPLETED TESTS --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-green">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

                <span class="kpi-trend-badge trend-up">

                    <i class="fa-solid fa-arrow-trend-up"></i>

                    +18.4%

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Completed Tests
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">
                        186
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

                    Tests completed this month

                </div>

            </div>

        </div>

    </div>


    {{-- PENDING APPROVAL --}}
    <div class="col-12 col-sm-6 col-xl-3">

        <div class="kpi-metric-card">

            <div class="kpi-top-row">

                <div class="kpi-icon-pill kpi-icon-blue">

                    <i class="fa-solid fa-file-circle-check"></i>

                </div>

                <span class="kpi-trend-badge trend-neutral">

                    <i class="fa-solid fa-clock"></i>

                    Pending

                </span>

            </div>

            <div>

                <div class="kpi-title">
                    Reports Pending
                </div>

                <div class="kpi-value-row">

                    <div class="kpi-value">
                        14
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

                    Reports awaiting QC approval

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    3. TESTING TREND + RESULT MIX
========================================================== --}}

<div class="row g-3 mb-4">

    {{-- TEST TREND --}}
    <div class="col-12 col-xl-8">

        <div class="chart-box-card">

            <div class="card-header-flex">

                <div>

                    <h3>
                        Laboratory Testing Trend
                    </h3>

                    <p class="text-muted small mb-0">
                        Samples and tests processed by laboratory
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

                <canvas id="labTestingChart"></canvas>

            </div>

        </div>

    </div>


    {{-- RESULT MIX --}}
    <div class="col-12 col-xl-4">

        <div class="chart-box-card">

            <div class="card-header-flex">

                <div>

                    <h3>
                        Test Result Mix
                    </h3>

                    <p class="text-muted small mb-0">
                        Current laboratory results
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

                <canvas id="labResultChart"></canvas>

            </div>

            <div
                class="d-flex justify-content-around text-center pt-3 border-top mt-2"
                style="
                    border-color: var(--border-color) !important;
                ">

                <div>

                    <div class="small text-muted">
                        Passed
                    </div>

                    <div
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        82%

                    </div>

                </div>

                <div>

                    <div class="small text-muted">
                        Hold
                    </div>

                    <div
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        11%

                    </div>

                </div>

                <div>

                    <div class="small text-muted">
                        Failed
                    </div>

                    <div
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        7%

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    4. LAB OPERATIONS + QUALITY TARGET
========================================================== --}}

<div class="row g-4 mb-4">

    {{-- LAB MODULES --}}
    <div class="col-12 col-lg-8">

        <div
            class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="fs-5 m-0 fw-bold">

                <i class="fa-solid fa-flask-vial me-2 text-muted"></i>

                Laboratory Operations

            </h3>

            <span class="text-muted small">

                Current FY:

                <strong id="fyText"></strong>

            </span>

        </div>


        <div class="row g-3">

            {{-- NEW SAMPLE --}}
            <div class="col-12 col-md-6">

                <a
                    href="#new-sample"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-vial"></i>

                    </div>

                    <div>

                        <h4>
                            New Sample
                        </h4>

                        <p>
                            Register raw material, finished product
                            and production samples for laboratory testing.
                        </p>

                    </div>

                </a>

            </div>


            {{-- SAMPLE RECEIVING --}}
            <div class="col-12 col-md-6">

                <a
                    href="#sample-receiving"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-box-open"></i>

                    </div>

                    <div>

                        <h4>
                            Sample Receiving
                        </h4>

                        <p>
                            Receive, identify and record samples
                            submitted by production and stores.
                        </p>

                    </div>

                </a>

            </div>


            {{-- TEST REQUESTS --}}
            <div class="col-12 col-md-6">

                <a
                    href="#test-requests"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-file-circle-plus"></i>

                    </div>

                    <div>

                        <h4>
                            Test Requests
                        </h4>

                        <p>
                            Manage pending laboratory test requests,
                            priorities and assigned analysts.
                        </p>

                    </div>

                </a>

            </div>


            {{-- MATERIAL TESTING --}}
            <div class="col-12 col-md-6">

                <a
                    href="#material-testing"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-microscope"></i>

                    </div>

                    <div>

                        <h4>
                            Material Testing
                        </h4>

                        <p>
                            Record chemical, physical and mechanical
                            test observations and measurements.
                        </p>

                    </div>

                </a>

            </div>


            {{-- TEST REPORTS --}}
            <div class="col-12 col-md-6">

                <a
                    href="#test-reports"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-file-invoice"></i>

                    </div>

                    <div>

                        <h4>
                            Test Reports
                        </h4>

                        <p>
                            Generate laboratory test reports,
                            certificates and quality documents.
                        </p>

                    </div>

                </a>

            </div>


            {{-- QC APPROVAL --}}
            <div class="col-12 col-md-6">

                <a
                    href="#qc-approval"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                    <div>

                        <h4>
                            QC Approval
                        </h4>

                        <p>
                            Review laboratory results and approve
                            or hold material batches.
                        </p>

                    </div>

                </a>

            </div>


            {{-- LAB INVENTORY --}}
            <div class="col-12 col-md-6">

                <a
                    href="#lab-inventory"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-flask"></i>

                    </div>

                    <div>

                        <h4>
                            Lab Inventory
                        </h4>

                        <p>
                            Manage chemicals, reagents, consumables,
                            standards and laboratory stock.
                        </p>

                    </div>

                </a>

            </div>


            {{-- LAB REPORTS --}}
            <div class="col-12 col-md-6">

                <a
                    href="#lab-reports"
                    class="module-tile">

                    <div class="module-icon-box">

                        <i class="fa-solid fa-chart-column"></i>

                    </div>

                    <div>

                        <h4>
                            Lab Reports
                        </h4>

                        <p>
                            Analyse product quality, test volumes,
                            failures and laboratory performance.
                        </p>

                    </div>

                </a>

            </div>

        </div>

    </div>


    {{-- QUALITY TARGET --}}
    <div class="col-12 col-lg-4">

        <div
            class="d-flex justify-content-between align-items-center mb-3">

            <h3 class="fs-5 m-0 fw-bold">

                <i class="fa-solid fa-bullseye me-2 text-muted"></i>

                Quality Target

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
                    Laboratory Test Completion Target
                </div>

                <div
                    class="fw-bold"
                    style="
                        font-size: 2rem;
                        color: var(--text-heading);
                    ">

                    2,400 Tests

                </div>

                <div class="small text-muted">
                    Annual Testing Target
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
                        width: 76%;
                        background: var(--primary-red);
                    ">

                </div>

            </div>


            <div
                class="d-flex justify-content-between mt-2">

                <span class="small text-muted">
                    Completed
                </span>

                <strong style="color: var(--text-heading);">

                    1,824 Tests

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

                            210 Tests

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

                            196 Tests

                        </div>

                    </div>

                </div>

            </div>


            <div class="mt-3">

                <div class="d-flex justify-content-between mb-1">

                    <span class="small text-muted">
                        Pass Rate
                    </span>

                    <strong
                        class="small"
                        style="color: var(--accent-green);">

                        94.2%

                    </strong>

                </div>

                <div
                    class="progress"
                    style="
                        height: 6px;
                        background: var(--bg-input);
                    ">

                    <div
                        class="progress-bar"
                        style="
                            width: 94.2%;
                            background: var(--accent-green);
                        ">
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
    5. RECENT LAB TEST TRANSACTIONS
========================================================== --}}

<div class="plant-table-card mb-4">

    <div
        class="card-header-flex p-3 px-4 border-bottom mb-0"
        style="
            border-color: var(--border-color) !important;
        ">

        <div>

            <h3 class="fs-6 m-0 fw-bold">

                <i class="fa-solid fa-microscope me-2 text-muted"></i>

                Recent Laboratory Tests

            </h3>

            <p class="text-muted small mb-0">

                Latest samples, tests and quality results

            </p>

        </div>


        <a
            href="#lab-tests"
            class="btn btn-sm btn-outline-secondary px-2 py-1"
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

                    <th>Test No.</th>

                    <th>Sample No.</th>

                    <th>Product</th>

                    <th>Heat / Batch</th>

                    <th>Test</th>

                    <th>Analyst</th>

                    <th>Result</th>

                    <th>Status</th>

                </tr>

            </thead>


            <tbody>

                {{-- ROW 1 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #LAB-2026-089

                    </td>

                    <td>
                        SMP-2026-142
                    </td>

                    <td>
                        ERW Round Pipes
                    </td>

                    <td>
                        HT-78452
                    </td>

                    <td>
                        Tensile Strength
                    </td>

                    <td>
                        Rakesh Kumar
                    </td>

                    <td class="fw-semibold">
                        458 MPa
                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Passed

                        </span>

                    </td>

                </tr>


                {{-- ROW 2 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #LAB-2026-090

                    </td>

                    <td>
                        SMP-2026-143
                    </td>

                    <td>
                        HR Strip Coil
                    </td>

                    <td>
                        HT-78461
                    </td>

                    <td>
                        Chemical Analysis
                    </td>

                    <td>
                        Priya Sharma
                    </td>

                    <td class="fw-semibold">
                        C 0.18%
                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Passed

                        </span>

                    </td>

                </tr>


                {{-- ROW 3 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #LAB-2026-091

                    </td>

                    <td>
                        SMP-2026-144
                    </td>

                    <td>
                        MS Billets
                    </td>

                    <td>
                        HT-78472
                    </td>

                    <td>
                        Hardness
                    </td>

                    <td>
                        Amit Verma
                    </td>

                    <td class="fw-semibold">
                        Pending
                    </td>

                    <td>

                        <span class="badge-status badge-status-inward">

                            <i class="fa-solid fa-clock"></i>

                            Testing

                        </span>

                    </td>

                </tr>


                {{-- ROW 4 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #LAB-2026-092

                    </td>

                    <td>
                        SMP-2026-145
                    </td>

                    <td>
                        ERW Square Pipes
                    </td>

                    <td>
                        HT-78480
                    </td>

                    <td>
                        Yield Strength
                    </td>

                    <td>
                        Rakesh Kumar
                    </td>

                    <td class="fw-semibold">
                        342 MPa
                    </td>

                    <td>

                        <span class="badge-status badge-status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Passed

                        </span>

                    </td>

                </tr>


                {{-- ROW 5 --}}
                <tr>

                    <td
                        class="fw-bold"
                        style="color: var(--text-heading);">

                        #LAB-2026-093

                    </td>

                    <td>
                        SMP-2026-146
                    </td>

                    <td>
                        MS Plates
                    </td>

                    <td>
                        HT-78486
                    </td>

                    <td>
                        Bend Test
                    </td>

                    <td>
                        Neha Singh
                    </td>

                    <td class="fw-semibold">
                        Hold
                    </td>

                    <td>

                        <span class="badge-status badge-status-gross">

                            <i class="fa-solid fa-pause"></i>

                            QC Hold

                        </span>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
    6. PENDING LAB TEST REQUESTS
========================================================== --}}

<div
    id="lab-tests"
    class="plant-table-card mb-4">

    <div
        class="card-header-flex p-3 px-4 border-bottom mb-0"
        style="
            border-color: var(--border-color) !important;
        ">

        <div>

            <h3 class="fs-6 m-0 fw-bold">

                <i class="fa-solid fa-hourglass-half me-2 text-muted"></i>

                Pending Laboratory Test Requests

            </h3>

            <p class="text-muted small mb-0">

                Samples requiring testing, analysis or QC approval

            </p>

        </div>


        <span
            class="badge border px-2 py-1"
            style="
                background: var(--accent-amber-subtle);
                color: var(--accent-amber);
                border-color: var(--accent-amber-border) !important;
            ">

            14 Pending Tests

        </span>

    </div>


    <div class="table-responsive">

        <table class="table table-custom">

            <thead>

                <tr>

                    <th>Request No.</th>

                    <th>Sample</th>

                    <th>Product</th>

                    <th>Heat / Batch</th>

                    <th>Required Test</th>

                    <th>Priority</th>

                    <th>Analyst</th>

                    <th>Due Date</th>

                </tr>

            </thead>


            <tbody>

                <tr>

                    <td class="fw-bold">
                        TR-2026-041
                    </td>

                    <td>
                        SMP-2026-147
                    </td>

                    <td>
                        ERW Pipes
                    </td>

                    <td>
                        HT-78502
                    </td>

                    <td>
                        Tensile + Bend
                    </td>

                    <td>

                        <span
                            class="badge border px-2 py-1"
                            style="
                                background: var(--accent-red-subtle);
                                color: var(--primary-red);
                                border-color: var(--accent-red-border) !important;
                            ">

                            High

                        </span>

                    </td>

                    <td>
                        Not Assigned
                    </td>

                    <td>
                        03 Oct 2026
                    </td>

                </tr>


                <tr>

                    <td class="fw-bold">
                        TR-2026-044
                    </td>

                    <td>
                        SMP-2026-148
                    </td>

                    <td>
                        HR Coils
                    </td>

                    <td>
                        HT-78510
                    </td>

                    <td>
                        Chemical Analysis
                    </td>

                    <td>

                        <span
                            class="badge border px-2 py-1"
                            style="
                                background: var(--accent-amber-subtle);
                                color: var(--accent-amber);
                                border-color: var(--accent-amber-border) !important;
                            ">

                            Medium

                        </span>

                    </td>

                    <td>
                        Priya Sharma
                    </td>

                    <td>
                        04 Oct 2026
                    </td>

                </tr>


                <tr>

                    <td class="fw-bold">
                        TR-2026-047
                    </td>

                    <td>
                        SMP-2026-149
                    </td>

                    <td>
                        MS Billets
                    </td>

                    <td>
                        HT-78516
                    </td>

                    <td>
                        Hardness Test
                    </td>

                    <td>

                        <span
                            class="badge border px-2 py-1"
                            style="
                                background: var(--accent-blue-subtle);
                                color: var(--accent-blue);
                                border-color: var(--accent-blue-border) !important;
                            ">

                            Normal

                        </span>

                    </td>

                    <td>
                        Amit Verma
                    </td>

                    <td>
                        05 Oct 2026
                    </td>

                </tr>


                <tr>

                    <td class="fw-bold">
                        TR-2026-050
                    </td>

                    <td>
                        SMP-2026-150
                    </td>

                    <td>
                        ERW Square Pipes
                    </td>

                    <td>
                        HT-78521
                    </td>

                    <td>
                        Dimensional Inspection
                    </td>

                    <td>

                        <span
                            class="badge border px-2 py-1"
                            style="
                                background: var(--accent-amber-subtle);
                                color: var(--accent-amber);
                                border-color: var(--accent-amber-border) !important;
                            ">

                            Medium

                        </span>

                    </td>

                    <td>
                        Not Assigned
                    </td>

                    <td>
                        06 Oct 2026
                    </td>

                </tr>


                <tr>

                    <td class="fw-bold">
                        TR-2026-053
                    </td>

                    <td>
                        SMP-2026-151
                    </td>

                    <td>
                        MS Plates
                    </td>

                    <td>
                        HT-78529
                    </td>

                    <td>
                        Impact Test
                    </td>

                    <td>

                        <span
                            class="badge border px-2 py-1"
                            style="
                                background: var(--accent-red-subtle);
                                color: var(--primary-red);
                                border-color: var(--accent-red-border) !important;
                            ">

                            High

                        </span>

                    </td>

                    <td>
                        Neha Singh
                    </td>

                    <td>
                        07 Oct 2026
                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
    7. LAB QUALITY SUMMARY
========================================================== --}}

<div class="row g-3 mb-4">

    {{-- PASS RATE --}}
    <div class="col-12 col-md-4">

        <div class="chart-box-card h-100">

            <div class="d-flex align-items-center gap-3">

                <div
                    class="kpi-icon-pill kpi-icon-green">

                    <i class="fa-solid fa-check-double"></i>

                </div>

                <div>

                    <div class="small text-muted">
                        Overall Pass Rate
                    </div>

                    <div
                        class="fw-bold fs-4"
                        style="color: var(--text-heading);">

                        94.2%

                    </div>

                </div>

            </div>

            <div
                class="progress mt-3"
                style="
                    height: 7px;
                    background: var(--bg-input);
                ">

                <div
                    class="progress-bar"
                    style="
                        width: 94.2%;
                        background: var(--accent-green);
                    ">
                </div>

            </div>

        </div>

    </div>


    {{-- TURNAROUND --}}
    <div class="col-12 col-md-4">

        <div class="chart-box-card h-100">

            <div class="d-flex align-items-center gap-3">

                <div
                    class="kpi-icon-pill kpi-icon-blue">

                    <i class="fa-solid fa-stopwatch"></i>

                </div>

                <div>

                    <div class="small text-muted">
                        Average Turnaround
                    </div>

                    <div
                        class="fw-bold fs-4"
                        style="color: var(--text-heading);">

                        4.8 Hrs

                    </div>

                </div>

            </div>

            <div class="small text-muted mt-3">

                Target turnaround:
                <strong>6 hours</strong>

            </div>

        </div>

    </div>


    {{-- FAILED --}}
    <div class="col-12 col-md-4">

        <div class="chart-box-card h-100">

            <div class="d-flex align-items-center gap-3">

                <div
                    class="kpi-icon-pill kpi-icon-red">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <div class="small text-muted">
                        Failed / Rejected
                    </div>

                    <div
                        class="fw-bold fs-4"
                        style="color: var(--text-heading);">

                        7%

                    </div>

                </div>

            </div>

            <div class="small text-muted mt-3">

                17 samples require review

            </div>

        </div>

    </div>

</div>


@endsection


@push('scripts')

{{-- =========================================================
    CHART.JS FALLBACK
========================================================== --}}

@if (!class_exists('App\\Providers\\AppServiceProvider'))
    {{-- Layout should normally load Chart.js --}}
@endif

<script>

document.addEventListener('DOMContentLoaded', function () {

    let labTestingChart = null;
    let labResultChart = null;


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
       GET THEME
    ========================================================== */

    function isDarkTheme() {

        return (
            document.documentElement.getAttribute('data-theme') === 'dark'
        );

    }


    /* =========================================================
       INITIALIZE CHARTS
    ========================================================== */

    function initLabCharts(isDark = false) {

        if (typeof Chart === 'undefined') {

            console.warn(
                'Chart.js is not loaded.'
            );

            return;

        }


        /* Destroy old charts */

        if (labTestingChart) {

            labTestingChart.destroy();

            labTestingChart = null;

        }


        if (labResultChart) {

            labResultChart.destroy();

            labResultChart = null;

        }


        const testingCanvas =
            document.getElementById(
                'labTestingChart'
            );


        const resultCanvas =
            document.getElementById(
                'labResultChart'
            );


        if (!testingCanvas || !resultCanvas) {

            return;

        }


        /* =====================================================
           COLORS
        ====================================================== */

        const gridColor = isDark
            ? 'rgba(255,255,255,.05)'
            : 'rgba(11,18,32,.06)';


        const labelColor = isDark
            ? '#8493A8'
            : '#64748B';


        const borderColor = isDark
            ? '#10192A'
            : '#FFFFFF';


        /* =====================================================
           TESTING TREND CHART
        ====================================================== */

        const testingCtx =
            testingCanvas.getContext('2d');


        const gradient =
            testingCtx.createLinearGradient(
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


        labTestingChart = new Chart(

            testingCtx,

            {

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
                                'Tests Completed',

                            data: [

                                118,
                                136,
                                129,
                                152,
                                147,
                                174,
                                186

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
                                            context.raw +
                                            ' Tests';

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
                                            ' Tests';

                                    }

                            }

                        }

                    }

                }

            }

        );


        /* =====================================================
           RESULT DOUGHNUT
        ====================================================== */

        const resultCtx =
            resultCanvas.getContext('2d');


        labResultChart = new Chart(

            resultCtx,

            {

                type: 'doughnut',

                data: {

                    labels: [

                        'Passed',
                        'QC Hold',
                        'Failed'

                    ],

                    datasets: [

                        {

                            data: [

                                82,
                                11,
                                7

                            ],

                            backgroundColor: [

                                '#10B981',
                                '#F59E0B',
                                '#E52D3F'

                            ],

                            borderColor:
                                borderColor,

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

    const labData = {

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

                118,
                136,
                129,
                152,
                147,
                174,
                186

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

                220,
                468,
                732,
                1018,
                1286,
                1598,
                1824

            ]

        }

    };


    /* =========================================================
       FINANCIAL YEAR DATA
    ========================================================== */

    function getFinancialYearChartData() {

        const today = new Date();

        const currentYear =
            today.getFullYear();

        const currentMonth =
            today.getMonth() + 1;

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


        const values = [

            210,
            420,
            640,
            820,
            1080,
            1824,
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


                    if (!labTestingChart) {

                        return;

                    }


                    if (period === '7days') {

                        labTestingChart.data.labels =
                            labData.sevenDays.labels;

                        labTestingChart
                            .data
                            .datasets[0]
                            .data =
                            labData.sevenDays.values;

                    }


                    if (period === 'month') {

                        labTestingChart.data.labels =
                            labData.month.labels;

                        labTestingChart
                            .data
                            .datasets[0]
                            .data =
                            labData.month.values;

                    }


                    if (period === 'year') {

                        const fyData =
                            getFinancialYearChartData();


                        labTestingChart.data.labels =
                            fyData.labels;

                        labTestingChart
                            .data
                            .datasets[0]
                            .data =
                            fyData.values;

                    }


                    labTestingChart.update();

                }
            );

        });


    /* =========================================================
       INITIALIZE
    ========================================================== */

    initLabCharts(
        isDarkTheme()
    );


    /* =========================================================
       GLOBAL THEME UPDATE
       Call this from your theme switcher.
    ========================================================== */

    window.updateLabChartsTheme =
        function (theme) {

            initLabCharts(
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
