@extends('layouts.app')

@section('title', 'Self Production Raw Materials Listings')

@section('content')
<div class="page-header">
    <div class="page-header__inner">
        <div class="page-header__content">
            <div class="page-header__title">
                <h6>Self Production Raw Materials Listings</h6>
            </div>
        </div>
    </div>
</div>

<div class="content_area">
    <div class="flex-grow-1 container-p-y">
        <div class="card listing-block">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">Self Production Raw Materials Listings</h5>
                <form method="GET" action="{{ route('productions.unlinked-listings') }}" class="input-group input-group-merge" style="max-width: 320px;">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="search" name="search" class="form-control" placeholder="Search here..." value="{{ request('search', '') }}" aria-label="Search unlinked listings">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table listing-table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>SR. NO</th>
                                <th>PO Item ID</th>
                                <th>PO No.</th>
                                <th>Party Name</th>
                                <th>Product</th>
                                <th>Grade</th>
                                <th>Size</th>
                                <th>Qty.</th>
                                <th>Rate</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody class="list table_listing_body">
                            @forelse ($listing as $key => $item)
                                <tr>
                                    <td>{{ $listing->firstItem() + $key }}</td>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->purchase_order_id ?: '-' }}</td>
                                    <td>{{ $item->customer?->company_name ?: $item->purchaseOrder?->customer?->company_name ?: '-' }}</td>
                                    <td>{{ $item->product?->product_name ?: '-' }}</td>
                                    <td>{{ $item->grade?->grade_name ?: '-' }}</td>
                                    <td>
                                        {{ $item->size?->size_name ?: '-' }}
                                        {{ $item->sizeUnit?->unit_name }}
                                    </td>
                                    <td>{{ $item->qty !== null ? _rz($item->qty) : '-' }} {{ $item->qty_unit }}</td>
                                    <td>{{ $item->rate !== null ? _rz($item->rate) : '-' }}</td>
                                    <td>{{ $item->created_at?->format('d-m-Y') ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-3">
                                        {{ request()->filled('search') ? 'No matching unlinked listings found.' : 'No unlinked listings found.' }}
                                    </td>
                                </tr>
                            @endforelse
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
