@extends('layouts.app')

@section('title', 'Manage Users & Roles | TTC Robotronics - Steel Industry ERP')

@push('styles')

@endpush

@section('content')

    <!-- Floating Toast Notification Area -->
    {{-- <div id="statusToastContainer" class="status-toast-container"></div> --}}


   <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Users & Roles</h6>
                    <span>Manage system operators, assign plant department roles, and toggle Active/Inactive status directly from the list.</span>
                </div>
                <div class="page-header__tabs">
                    <a href="#" class="page-tab active">All Users </a>
                    {{-- <a href="#" class="page-tab"> Pending </a>
                    <a href="#" class="page-tab">Processing</a>
                    <a href="#" class="page-tab"> Completed </a>
                    <a href="#" class="page-tab"> Cancelled</a> --}}
                </div>
            </div>

            <div class="page-header__actions">
                @if(auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin')
                    <a href="{{ route('users.add') }}" class="header-btn header-btn--primary"><i class="fas fa-plus"></i> Add New User / Role </a>
                @endif
                @include('admin.users.filters')
            </div>
        </div>
    </div>

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">

                    <div class="card listing-block">
                        <div class="card-header">
                            <div class="heading">
                                <h5 class="mb-0">Here Is Your Users & Roles Listing!</h5>
                            </div>
                            {{-- <div class="actions">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></span>
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
                                                onclick="bulk_actions('{{ route('admin.users.bulkActions', ['action' => 'active']) }}', 'active');">
                                                    <i class="fas fa-circle text-success"></i>
                                                    <span class="status">Publish</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="javascript:;" 
                                                onclick="bulk_actions('{{ route('admin.users.bulkActions', ['action' => 'inactive']) }}', 'inactive');">
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
                                                onclick="bulk_actions('{{ route('admin.users.bulkActions', ['action' => 'delete']) }}', 'delete');">
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
                                                @if(isset($_GET['sort']) && $_GET['sort'] == 'users.id' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="users.id" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'users.id' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="users.id" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="users.id" data-sort="asc"></i>
                                                @endif
                                            </th>
                                            <th class="sort">
                                                User Profile
                                                @if(isset($_GET['sort']) && $_GET['sort'] == 'users.first_name' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="users.first_name" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'users.first_name' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="users.first_name" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="users.first_name"></i>
                                                @endif
                                            </th>
                                            <th class="sort">
                                                Role Assigned
                                                @if(isset($_GET['sort']) && $_GET['sort'] == 'users.first_name' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="users.first_name" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'users.first_name' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="users.first_name" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="users.first_name"></i>
                                                @endif
                                            </th>
                                            <th class="sort">
                                                Contact Details
                                            </th>
                                            <th class="sort">
                                                Status
                                                @if(isset($_GET['sort']) && $_GET['sort'] == 'users.first_name' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="users.first_name" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'users.first_name' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="users.first_name" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="users.first_name"></i>
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
                                    <tbody class="list">
                                        @if(!empty($listing->items()))
                                            @include('admin.users.listingLoop')
                                        @else
                                        <td align="left" colspan="7">
                                            No records found!
                                        </td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
