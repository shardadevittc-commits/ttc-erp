@extends('layouts.app')

@section('title', 'Manage Sale Orders')

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Sale Orders</h6>
            </div>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('saleOrders.add') }}" class="header-btn header-btn--primary">
                <i class="fas fa-plus"></i> New Sale Order
            </a>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card listing-block ajax-listing-loop">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sale Orders</h5>
                <div class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="search" class="form-control listing-search" placeholder="Search sale orders..." value="{{ request('search', '') }}" aria-label="Search sale orders">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading sale orders...
                </div>
                <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                <div class="table-responsive">
                    <table class="table listing-table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Customer P.O. No.</th>
                                <th>Items</th>
                                <th>Freight</th>
                                <th>Created</th>
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
