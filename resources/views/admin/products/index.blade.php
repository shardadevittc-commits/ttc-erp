@extends('layouts.app')

@section('title', 'Manage Products | TTC Robotronics - Steel Industry ERP')

@push('styles')

@endpush

@section('content')

   <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Products</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('products.add') }}" class="header-btn header-btn--primary"><i class="fas fa-plus"></i> New </a>
                @include('admin.products.filters')
            </div>
        </div>
    </div>

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    {{-- @include('admin.partials.flash_messages') --}}
                    <!--!!!!! DO NOT REMOVE listing-block CLASS. INCLUDE THIS IN PARENT DIV OF TABLE ON LISTING USERS !!!!!-->
                    <div class="card listing-block ajax-listing-loop">
                        <div class="card-header">
                            <div class="heading">
                                <h5 class="mb-0">Here Is Your Products Listing!</h5>
                            </div>
                            <div class="actions">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="search" class="form-control listing-search" placeholder="Search..." value="{{ request('search', '') }}" aria-label="Search Grades">
                                </div>
                            </div>
                            {{-- <div class="actions">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" class="form-control listing-search" placeholder="Search..." value="{{ (isset($_GET['search']) && $_GET['search'] ? $_GET['search'] : '') }}">
                                </div>
                                @if(Permission::hasPermission('users', 'update') || Permission::hasPermission('users', 'delete'))
                                <div class="action_dropdown btn-group">
                                    <a href="javascript:;" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                            @if(Permission::hasPermission('users', 'update'))
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" 
                                                onclick="bulk_actions('{{ route('admin.products.bulkActions', ['action' => 'active']) }}', 'active');">
                                                    <i class="fas fa-circle text-success"></i>
                                                    <span class="status">Publish</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" 
                                                onclick="bulk_actions('{{ route('admin.products.bulkActions', ['action' => 'inactive']) }}', 'inactive');">
                                                    <i class="fas fa-circle text-danger"></i>
                                                    <span class="status">Unpublish</span>
                                                </a>
                                            </li>
                                            @endif

                                            @if(Permission::hasPermission('users', 'update') && Permission::hasPermission('users', 'delete'))
                                            <div class="dropdown-divider"></div>
                                            @endif

                                            @if(Permission::hasPermission('users', 'delete'))
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" 
                                                onclick="bulk_actions('{{ route('admin.products.bulkActions', ['action' => 'delete']) }}', 'delete');">
                                                    <i class="fas fa-times text-danger"></i>
                                                    <span class="status">Delete</span>
                                                </a>
                                            </li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>
                                @endif
                            </div> --}}
                        </div>
                        <!--!!!!! DO NOT REMOVE listing-table, mark_all  CLASSES. INCLUDE THIS IN ALL TABLES LISTING USERS !!!!!-->
                        <div class="card-body p-0">
                            <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading products...
                            </div>
                            <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                            <div class="table-responsive text-nowrap">
                                <table class="table listing-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="sort">
                                                <!--- MAKE SURE TO USE PROPOER FIELD IN data-field AND PROPOER DIRECTION IN data-sort -->
                                                Id
                                                @if(request('sort_by', 'id') === 'id' && request('sort_order', 'desc') === 'asc')
                                                <i class="fas fa-sort-down active" data-field="id"></i>
                                                @elseif(request('sort_by', 'id') === 'id')
                                                <i class="fas fa-sort-up active" data-field="id"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="id"></i>
                                                @endif
                                            </th>
                                            <th class="sort">
                                                Products Name
                                                @if(request('sort_by') === 'product_name' && request('sort_order') === 'asc')
                                                <i class="fas fa-sort-down active" data-field="product_name"></i>
                                                @elseif(request('sort_by') === 'product_name')
                                                <i class="fas fa-sort-up active" data-field="product_name"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="product_name"></i>
                                                @endif
                                            </th>
                                            <th class="sort">
                                                Status
                                                @if(request('sort_by') === 'status' && request('sort_order') === 'asc')
                                                <i class="fas fa-sort-down active" data-field="status"></i>
                                                @elseif(request('sort_by') === 'status')
                                                <i class="fas fa-sort-up active" data-field="status"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="status"></i>
                                                @endif
                                            </th>
                                            <th class="sort">
                                                Created
                                            </th>
                                            <th>
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="list table_listing_body">
                                        @include('admin.products.listingLoop')
                                    </tbody>
                                    
                                    {{-- <tfoot>
                                        <tr>
                                            <th align="left" colspan="20">
                                                @include('admin.partials.pagination', ["pagination" => $listing])
                                            </th>
                                        </tr>
                                    </tfoot> --}}
                                </table>
                            </div>
                            <div class="listing-pagination px-3 py-2">
                                {{ $listing->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection