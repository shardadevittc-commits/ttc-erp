
@extends('layouts.app')

@section('title', 'Manage Sale Orders')

@section('content')

<style>
    /* Page layout */
    .sale-order-page {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    .sale-order-page .card {
        width: 100%;
        overflow: hidden;
    }

    /* Table wrapper */
    .sale-order-page .table-responsive {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #edf1f4;
        background: #ffffff;
        scrollbar-width: thin;
        scrollbar-color: #b7dfe9 #f7fafb;
    }

    /* Table */
    .sale-order-table {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0 !important;
        font-size: 14px;
        color: #344054;
    }

    /* Header and body alignment */
    .sale-order-table th,
    .sale-order-table td {
        box-sizing: border-box;
        padding: 12px 9px !important;
        vertical-align: middle !important;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .sale-order-table thead th {
        background: linear-gradient(135deg, #08b5e5 0%, #079ed0 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 13px;
        line-height: 1.35;
        letter-spacing: 0.25px;
        text-align: center;
        padding: 15px 8px !important;
        border: 0;
        border-right: 1px solid rgba(255, 255, 255, 0.22);
        border-bottom: 1px solid #078ebd;
        box-shadow: 0 2px 5px rgba(8, 181, 229, 0.12);
    }

    .sale-order-table thead th:last-child {
        border-right: 0;
    }

    .sale-order-table tbody td {
        height: 62px;
        font-size: 14px;
        line-height: 1.55;
        background: #ffffff;
        border: 0;
        border-right: 1px solid #eef2f5;
        border-bottom: 1px solid #e8eef2;
        transition: background-color 0.18s ease, color 0.18s ease;
    }

    .sale-order-table tbody td:last-child {
        border-right: 0;
    }

    .sale-order-table tbody tr:nth-child(even) td {
        background: #fafcfd;
    }

    .sale-order-table tbody tr:hover td {
        background: #edf9fd;
    }

    .sale-order-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .sale-order-table tbody tr td:first-child {
        color: #087da7;
        font-weight: 700;
    }

    .sale-order-table tbody td a.fw-medium {
        color: #087da7;
        text-decoration: none;
    }

    .sale-order-table tbody td a.fw-medium:hover {
        color: #08a2cf;
        text-decoration: underline;
    }

    /* Column widths */
    .sale-order-table th:nth-child(1),
    .sale-order-table td:nth-child(1) {
        width: 5%;
        text-align: center;
    }

    .sale-order-table th:nth-child(2),
    .sale-order-table td:nth-child(2) {
        width: 8%;
        text-align: center;
    }

    .sale-order-table th:nth-child(3),
    .sale-order-table td:nth-child(3) {
        width: 10%;
    }

    .sale-order-table th:nth-child(4),
    .sale-order-table td:nth-child(4) {
        width: 7%;
        text-align: center;
    }

    .sale-order-table th:nth-child(5),
    .sale-order-table td:nth-child(5) {
        width: 7%;
        text-align: center;
    }

    .sale-order-table th:nth-child(6),
    .sale-order-table td:nth-child(6) {
        width: 22%;
    }

    .sale-order-table th:nth-child(7),
    .sale-order-table td:nth-child(7) {
        width: 7%;
        text-align: center;
    }

    .sale-order-table th:nth-child(8),
    .sale-order-table td:nth-child(8) {
        width: 9%;
        text-align: center;
    }

    .sale-order-table th:nth-child(9),
    .sale-order-table td:nth-child(9) {
        width: 7%;
        text-align: center;
    }

    .sale-order-table th:nth-child(10),
    .sale-order-table td:nth-child(10) {
        width: 11%;
    }

    .sale-order-table th:nth-child(11),
    .sale-order-table td:nth-child(11) {
        width: 7%;
        text-align: center;
    }

    /* Action buttons */
    .sale-order-table td:last-child {
        white-space: nowrap !important;
    }

    .sale-order-table td:last-child .d-flex {
        justify-content: center;
        gap: 4px !important;
    }

    .sale-order-table td:last-child a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        margin: 0;
        border: 1px solid #e7edf1;
        border-radius: 7px;
        background: #ffffff;
        text-decoration: none;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }

    .sale-order-table td:last-child a:hover {
        transform: translateY(-1px);
        border-color: #b9dce7;
        box-shadow: 0 4px 8px rgba(16, 24, 40, 0.10);
    }

    .sale-order-table td:last-child i {
        font-size: 12px;
    }

    /* Header spacing */
    .sale-order-page .card-header {
        padding: 12px 15px;
    }

    .sale-order-page .container-p-y {
        padding-top: 12px !important;
        padding-bottom: 12px !important;
    }

    /* Tablet/mobile */
    @media (max-width: 768px) {
        .sale-order-page .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sale-order-table {
            min-width: 1180px;
            font-size: 11px;
        }

        .sale-order-table th,
        .sale-order-table td {
            padding: 10px 8px !important;
        }

        .sale-order-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .sale-order-table thead th {
            font-size: 12px;
        }

        .sale-order-table tbody td {
            height: 56px;
            font-size: 13px;
        }
    }
</style>

<div class="page-header">
    <div class="page-header__inner">

        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Sale Orders</h6>
            </div>
        </div>

        <div class="page-header__actions">
            <a href="{{ route('saleOrders.add') }}"
               class="header-btn header-btn--primary">
                <i class="fas fa-plus"></i> New Sale Order
            </a>
        </div>

    </div>
</div>

<div class="content_area sale-order-page">

    <div class="flex-grow-1 container-p-y">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card listing-block ajax-listing-loop">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0">Sale Orders</h5>

                <div class="input-group input-group-merge"
                     style="max-width: 320px;">

                    <span class="input-group-text">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>

                    <input type="search"
                           class="form-control listing-search"
                           placeholder="Search sale orders..."
                           value="{{ request('search', '') }}"
                           aria-label="Search sale orders">

                </div>

            </div>

            <div class="card-body p-0">

                <div class="listing-loading d-none px-3 py-2"
                     role="status"
                     aria-live="polite">

                    <span class="spinner-border spinner-border-sm"
                          aria-hidden="true"></span>

                    Loading sale orders...

                </div>

                <div class="listing-error d-none alert alert-danger m-3"
                     role="alert">
                </div>

                <div class="table-responsive">

                    <table class="table sale-order-table">

                        <thead>
                            <tr>
                                <th>OR. NO.</th>
                                <th>Order Date</th>
                                <th>Party Name</th>
                                <th>Deal Qty. (Tons)</th>
                                <th>Pending Deal (Tons)</th>
                                <th>Sale Rate - Product - Sizes - Grade</th>
                                <th>Ordered Qty.</th>
                                <th>Dispatched Qty.</th>
                                <th>Remaining Qty.</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody class="list table_listing_body">
                            @include('admin.saleOrders.listingLoop')
                        </tbody>

                    </table>

                </div>

                <div class="listing-pagination px-3 py-2">
                    {{ $listing->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>

    </div>
</div>

@endsection
