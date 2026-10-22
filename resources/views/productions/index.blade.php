@extends('layouts.app')

@section('title', 'Production Listing')

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Production Listing</h6>
            </div>
        </div>
        <div class="page-header__actions">
            @include('productions.filters')
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">
        <div class="alert alert-success d-none" id="productionSuccessMessage" role="status"></div>
        <div class="card listing-block ajax-listing-loop">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Sale Order Items</h5>
                <div class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="search" class="form-control listing-search" placeholder="Search production..." value="{{ request('search', '') }}" aria-label="Search production">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading production...
                </div>
                <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                <div class="table-responsive">
                    <table class="table listing-table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SR. NO</th>
                                <th>WO. No./Date</th>
                                <th>Party Name</th>
                                <th>SO No - Product - Sizes - Rate - Qty</th>
                                <th>Grade</th>
                                <th>Size</th>
                                <th>Order Qty.</th>
                                <th>Status</th>
                                <th>Action</th>
                                {{-- <th>Dispatch Qty.</th>
                                <th>Dispatched Qty.</th>
                                <th>Pending Qty.</th>
                                <th>Completion</th> --}}
                            </tr>
                        </thead>
                        <tbody class="list table_listing_body">
                            @include('productions.listingLoop')
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

<div class="modal fade" id="workOrderModal" tabindex="-1" aria-labelledby="workOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="workOrderModalLabel">Send Material for Work</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="workOrderModalBody">
                @include('productions.workOrderDetails')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/production-work-orders.js') }}" defer></script>
@endpush
