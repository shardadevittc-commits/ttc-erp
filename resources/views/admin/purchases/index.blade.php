@extends('layouts.app')

@section('title', 'Manage Purchase Orders | TTC Robotronics - Steel Industry ERP')

@push('styles')
    <style>
        .purchase-products {
            white-space: normal;
            min-width: 220px;
        }

        .purchase-product-item {
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }

        .purchase-product-item:last-child {
            border-bottom: none;
        }

        .listing-table th,
        .listing-table td {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')

    <div class="page-header">
        <div class="page-header__inner">

            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Purchase Orders</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('purchase.add') }}" class="header-btn header-btn--primary"><i class="fas fa-plus"></i>New
                    Purchase Order</a>
            </div>

        </div>
    </div>

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-12">

                    <div class="card listing-block ajax-listing-loop">

                        {{-- Card Header --}}
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">

                            <div class="heading">
                                <h5 class="mb-0">Here Is Your Purchase Orders Listing!</h5>
                            </div>

                            {{-- Search --}}
                            <div class="actions">
                                <form method="GET" action="{{ route('purchase') }}">

                                    <div class="input-group input-group-merge"><span class="input-group-text"><i
                                                class="fa-solid fa-magnifying-glass"></i></span>

                                        <input type="search" name="search" class="form-control" placeholder="Search..."
                                            value="{{ request('search', '') }}" aria-label="Search purchase orders">

                                        <button type="submit" class="btn btn-primary"> Search </button>

                                        @if (request()->filled('search'))
                                            <a href="{{ route('purchase') }}" class="btn btn-outline-secondary"> Reset </a>
                                        @endif
                                    </div>

                                </form>
                            </div>

                        </div>

                        {{-- Table Body --}}
                        <div class="card-body p-0">

                            <div class="table-responsive">
                                <table class="table listing-table mb-0">

                                    <thead class="thead-light">
                                        <tr>

                                            {{-- ID --}}
                                            <th>
                                                <a href="{{ route(
                                                    'purchase',
                                                    array_merge(request()->query(), [
                                                        'sort_by' => 'id',
                                                        'sort_order' => request('sort_by') === 'id' && request('sort_order', 'desc') === 'desc' ? 'asc' : 'desc',
                                                    ]),
                                                ) }}"
                                                    class="text-dark text-decoration-none">
                                                    ID
                                                    @if (request('sort_by', 'id') === 'id')
                                                        <i
                                                            class="fas fa-sort-{{ request('sort_order', 'desc') === 'asc' ? 'up' : 'down' }}"></i>
                                                    @else
                                                        <i class="fas fa-sort"></i>
                                                    @endif
                                                </a>
                                            </th>

                                            <th>Customer</th>

                                            <th>Products</th>

                                            <th>Basic Rate</th>

                                            <th>Freight Basis</th>

                                            <th>Payment Term</th>

                                            <th>Created</th>

                                            <th>Actions</th>

                                        </tr>
                                    </thead>

                                    <tbody class="list table_listing_body">

                                        @forelse($purchaseOrders as $purchaseOrder)

                                            <tr>
                                                {{-- Purchase Order ID --}}
                                                <td><strong>#{{ $purchaseOrder->id }}</strong></td>

                                                {{-- Customer --}}
                                                <td>{{ $purchaseOrder->customer->company_name ?? 'N/A' }}</td>

                                                {{-- Products --}}
                                                <td class="purchase-products">

                                                    @forelse($purchaseOrder->items as $item)
                                                        <div class="purchase-product-item">

                                                            <div>
                                                                <strong>{{ $item->product->product_name ?? 'N/A' }}</strong>
                                                            </div>

                                                            <small class="text-muted">
                                                                @if ($item->size)
                                                                    <b style="font-weight: bold; color: #1f2937;">Size:</b>
                                                                    {{ $item->size->size_name ?? '-' }}
                                                                @endif

                                                                @if ($item->sizeUnit)
                                                                    {{ $item->sizeUnit->unit_name ?? '' }}
                                                                @endif

                                                                @if ($item->grade)
                                                                    | <b
                                                                        style="font-weight: bold; color: #1f2937;">Grade:</b>
                                                                    {{ $item->grade->grade_name ?? '-' }}
                                                                @endif

                                                                @if (!is_null($item->length))
                                                                    | <b
                                                                        style="font-weight: bold; color: #1f2937;">Length:</b>
                                                                    {{ $item->length }}
                                                                @endif

                                                                @if (!is_null($item->qty))
                                                                    <br>
                                                                    <b style="font-weight: bold; color: #1f2937;">Qty:</b>
                                                                    {{ $item->qty }}
                                                                    {{ $item->qty_unit ?? '' }}
                                                                @endif

                                                                @if (!is_null($item->rate))
                                                                    | <b
                                                                        style="font-weight: bold; color: #1f2937;">Rate:</b>
                                                                    {{ $item->rate }}
                                                                @endif

                                                            </small>
                                                        </div>
                                                    @empty
                                                        <span class="text-muted">No items found</span>
                                                    @endforelse

                                                </td>

                                                {{-- Basic Rate --}}
                                                <td>
                                                    {{ $purchaseOrder->b_rate ?? '-' }}
                                                </td>

                                                {{-- Freight Basis --}}
                                                <td>
                                                    <span class="badge"
                                                        style="background-color: #e9ecef; color: #343a40; font-weight: 600;">
                                                        {{ $purchaseOrder->basis ?? 'N/A' }}</span>
                                                </td>

                                                {{-- Payment Term --}}
                                                <td>
                                                    {{ $purchaseOrder->term ?? '-' }}
                                                </td>

                                                {{-- Created Date --}}
                                                <td>
                                                    {{ $purchaseOrder->created_at ? $purchaseOrder->created_at->format('d-m-Y') : '-' }}
                                                </td>

                                                {{-- Actions --}}
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">

                                                        {{-- View button: enable when route exists --}}
                                                        <a href="{{ route('purchase.add', $purchaseOrder->id) }}"
                                                            class="btn btn-sm btn-info" title ="View"> <i
                                                                class="fas fa-eye"></i> </a>


                                                        {{-- Edit button: enable when route exists --}}
                                                        <a href="{{ route('purchase.add', ['id' => $purchaseOrder->id]) }}"
                                                            class="btn btn-sm btn-primary" title="Edit"> <i
                                                                class="fas fa-edit"></i> </a>


                                                        {{-- Delete Button --}}
                                                        <form action="{{ route('purchase.delete', $purchaseOrder->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this Purchase Order?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>
                                                <td colspan="8"class="text-center py-4"> <i
                                                        class="fas fa-box-open fa-2x text-muted mb-2"></i>

                                                    <p class="mb-0"> No purchase orders found. </p>

                                                    @if (request()->filled('search'))
                                                        <small class="text-muted"> Try searching with another keyword.
                                                        </small>
                                                    @endif

                                                </td>
                                            </tr>

                                        @endforelse

                                    </tbody>

                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if ($purchaseOrders->hasPages())
                                <div
                                    class="listing-pagination px-3 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

                                    <div>
                                        <small class="text-muted">
                                            Showing
                                            {{ $purchaseOrders->firstItem() ?? 0 }}
                                            to
                                            {{ $purchaseOrders->lastItem() ?? 0 }}
                                            of
                                            {{ $purchaseOrders->total() }}
                                            purchase orders
                                        </small>
                                    </div>

                                    <div>
                                        {{ $purchaseOrders->links('pagination::bootstrap-5') }}
                                    </div>

                                </div>
                            @endif

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

@endsection
