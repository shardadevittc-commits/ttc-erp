@extends('layouts.app')

@section('title', 'Manage Customers | TTC Robotronics - Steel Industry ERP')

@push('styles')

@endpush

@section('content')

    <!-- Floating Toast Notification Area -->
    {{-- <div id="statusToastContainer" class="status-toast-container"></div> --}}


   <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Customers</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('customers.add') }}" class="header-btn header-btn--primary"><i class="fas fa-plus"></i> New </a>
                @include('admin.customers.filters')
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
                                <h5 class="mb-0">Here Is Your Customers Listing!</h5>
                            </div>
                            <div class="actions">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="search" class="form-control listing-search" placeholder="Search..." value="{{ request('search', '') }}" aria-label="Search customers">
                                </div>
                            </div>
                            
                        </div>
                        <!--!!!!! DO NOT REMOVE listing-table, mark_all  CLASSES. INCLUDE THIS IN ALL TABLES LISTING USERS !!!!!-->
                        <div class="card-body p-0">
                            <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading customers...
                            </div>
                            <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                            <div class="table-responsive text-nowrap">
                                <table class="table listing-table">
                                    <thead class="thead-light">
                                        <tr>
                                            {{-- <th width="5%">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input mark_all" id="mark_all">
                                                    <label class="form-check-label" for="mark_all"></label>
                                                </div>
                                            </th> --}}
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
                                                Company Name
                                                @if(request('sort_by') === 'company_name' && request('sort_order') === 'asc')
                                                <i class="fas fa-sort-down active" data-field="company_name"></i>
                                                @elseif(request('sort_by') === 'company_name')
                                                <i class="fas fa-sort-up active" data-field="company_name"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="company_name"></i>
                                                @endif
                                            </th>
                                            {{-- <th class="sort">
                                                Emails
                                                @if(request('sort_by') === 'email' && request('sort_order') === 'asc')
                                                <i class="fas fa-sort-down active" data-field="email"></i>
                                                @elseif(request('sort_by') === 'email')
                                                <i class="fas fa-sort-up active" data-field="email"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="email"></i>
                                                @endif
                                            </th> --}}
                                            <th class="sort">
                                                State / Country
                                                @if(request('sort_by') === 'state_id' && request('sort_order') === 'asc')
                                                <i class="fas fa-sort-down active" data-field="state_id"></i>
                                                @elseif(request('sort_by') === 'state_id')
                                                <i class="fas fa-sort-up active" data-field="state_id"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="state_id"></i>
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
                                        @include('admin.customers.listingLoop')
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