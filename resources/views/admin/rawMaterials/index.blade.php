@extends('layouts.app')

@section('title', 'Manage Raw Materials')

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Manage Raw Materials</h6>
            </div>
        </div>
        <div class="page-header__actions">
            @include('admin.rawMaterials.filters')
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
                <h5 class="mb-0">Raw Materials</h5>
                <div class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="search" class="form-control listing-search" placeholder="Search Raw Materials..." value="{{ request('search', '') }}" aria-label="Search Raw Materials">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading Raw Materials...
                </div>
                <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                <div class="table-responsive">
                    <table class="table listing-table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO's</th>
                                <th>Party Name</th>
                                <th style="width: 5%;">Product</th>
                                <th width="10%">Brand</th>
                                <th>Size/Dia(mm)</th>
                                <th width="10%">Length(Meter)</th>
                                <th width="10%">Grade</th>
                                <th width="10%">Received On</th>
                                <th>Received Qty (Tons)</th>
                                <th>Used Qty (Tons)</th>
                                <th>Avail. Qty (Tons)</th>
                                <th>Generate TC</th>
                            </tr>
                        </thead>
                        <tbody class="list table_listing_body">
                            @include('admin.rawMaterials.listingLoop')
                        </tbody>
                    </table>
                </div>
                {{-- <div class="listing-pagination px-3 py-2">
                    {{ $listing->links('pagination::bootstrap-5') }}
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection
