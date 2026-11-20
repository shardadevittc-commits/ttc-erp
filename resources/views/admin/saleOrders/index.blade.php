
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
        overflow-x: hidden;
    }

    /* Table */
    .sale-order-table {
        width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse;
        margin-bottom: 0 !important;
        font-size: 11px;
    }

    /* Header and body alignment */
    .sale-order-table th,
    .sale-order-table td {
        box-sizing: border-box;
        padding: 7px 4px !important;
        vertical-align: middle !important;
        white-space: normal !important;
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .sale-order-table thead th {
        background-color: #08b5e5 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 11px;
        text-align: center;
        vertical-align: middle !important;
        padding: 12px 5px !important;
        border: 1px solid ##08b5e5 !important;
    }


    .sale-order-table thead th:hover {
        background-color: ##08b5e5 !important;
    }

    /* Header bottom border */
    .sale-order-table thead {
        border-bottom: 2px solid ##08b5e5;
    }

    .sale-order-table td {
        font-size: 11px;
        line-height: 1.5;
    }

    /* Column widths */
    .sale-order-table th:nth-child(1),
    .sale-order-table td:nth-child(1) {
        width: 5%;
    }

    .sale-order-table th:nth-child(2),
    .sale-order-table td:nth-child(2) {
        width: 8%;
    }

    .sale-order-table th:nth-child(3),
    .sale-order-table td:nth-child(3) {
        width: 10%;
    }

    .sale-order-table th:nth-child(4),
    .sale-order-table td:nth-child(4) {
        width: 7%;
    }

    .sale-order-table th:nth-child(5),
    .sale-order-table td:nth-child(5) {
        width: 7%;
    }

    .sale-order-table th:nth-child(6),
    .sale-order-table td:nth-child(6) {
        width: 22%;
    }

    .sale-order-table th:nth-child(7),
    .sale-order-table td:nth-child(7) {
        width: 7%;
    }

    .sale-order-table th:nth-child(8),
    .sale-order-table td:nth-child(8) {
        width: 9%;
    }

    .sale-order-table th:nth-child(9),
    .sale-order-table td:nth-child(9) {
        width: 7%;
    }

    .sale-order-table th:nth-child(10),
    .sale-order-table td:nth-child(10) {
        width: 11%;
    }

    .sale-order-table th:nth-child(11),
    .sale-order-table td:nth-child(11) {
        width: 7%;
    }

    /* Action buttons */
    .sale-order-table td:last-child {
        white-space: nowrap !important;
    }

    .sale-order-table td:last-child a {
        display: inline-block;
        margin: 0 2px;
    }

    .sale-order-table td:last-child i {
        font-size: 13px;
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
        }

        .sale-order-table {
            min-width: 1100px;
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
                                <th>Order Qty. (Tons)</th>
                                <th>Dispatched Qty. (Tons)</th>
                                <th>Pending Qty. (Tons)</th>
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
