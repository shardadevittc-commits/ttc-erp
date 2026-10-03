@extends('layouts.app')

@section('title', 'Manage Brands | TTC Robotronics - Steel Industry ERP')

@push('styles')

@endpush

@section('content')

    <!-- Floating Toast Notification Area -->
    {{-- <div id="statusToastContainer" class="status-toast-container"></div> --}}


   <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Brands</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('brands.add') }}" class="header-btn header-btn--primary"><i class="fas fa-plus"></i> New </a>
                @include('admin.brands.filters')
            </div>
        </div>
    </div>


    

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    {{-- @include('admin.partials.flash_messages') --}}
                    <!--!!!!! DO NOT REMOVE listing-block CLASS. INCLUDE THIS IN PARENT DIV OF TABLE ON LISTING USERS !!!!!-->
                    <div class="card listing-block">
                        <div class="card-header">
                            <div class="heading">
                                <h5 class="mb-0">Here Is Your Brands Listing!</h5>
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
                                                {{-- @if(isset($_GET['sort']) && $_GET['sort'] == 'users.id' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="users.id" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'users.id' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="users.id" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="users.id" data-sort="asc"></i>
                                                @endif --}}
                                            </th>
                                            <th class="sort">
                                                Brand Name
                                                {{-- @if(isset($_GET['sort']) && $_GET['sort'] == 'brands.brand_name' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="brands.brand_name" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'brands.brand_name' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="brands.brand_name" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="brands.brand_name"></i>
                                                @endif --}}
                                            </th>
                                            <th class="sort">
                                                Status
                                                @if(isset($_GET['sort']) && $_GET['sort'] == 'brands.status' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
                                                <i class="fas fa-sort-down active" data-field="brands.status" data-sort="asc"></i>
                                                @elseif(isset($_GET['sort']) && $_GET['sort'] == 'brands.status' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
                                                <i class="fas fa-sort-up active" data-field="brands.status" data-sort="desc"></i>
                                                @else
                                                <i class="fas fa-sort" data-field="brands.status"></i>
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
                                        @if(!empty($listing->items()))
                                            @include('admin.brands.listingLoop')
                                        @else
                                        <td align="left" colspan="7">
                                            No records found!
                                        </td>
                                        @endif
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

{{-- @push('scripts')
<script>
    /**
     * Handle real-time toggle switch for Active / Inactive status
     */
    function handleStatusToggle(checkbox) {
        const url = checkbox.getAttribute('data-url');
        const userId = checkbox.getAttribute('data-user-id');
        const badge = document.getElementById('statusBadge' + userId);
        const previousState = !checkbox.checked;

        // Visual loading state
        checkbox.disabled = true;

        // Retrieve CSRF token from meta or fallback
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                _token: csrfToken
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Status update failed'); });
            }
            return response.json();
        })
        .then(data => {
            checkbox.disabled = false;
            if (data.success) {
                // Update badge visuals
                if (data.status === {{ \App\Models\User::STATUS_ACTIVE }}) {
                    checkbox.checked = true;
                    if (badge) {
                        badge.textContent = 'Active';
                        badge.style = 'background: var(--accent-green-subtle); color: var(--accent-green); border-color: var(--accent-green-border) !important; font-size: 0.68rem;';
                    }
                    updateKpiCounters(1); // Increment active, decrement inactive
                } else {
                    checkbox.checked = false;
                    if (badge) {
                        badge.textContent = 'Inactive';
                        badge.style = 'background: var(--accent-amber-subtle); color: var(--accent-amber); border-color: var(--accent-amber-border) !important; font-size: 0.68rem;';
                    }
                    updateKpiCounters(-1); // Decrement active, increment inactive
                }
                showStatusToast(data.message, 'success');
            } else {
                checkbox.checked = previousState;
                showStatusToast(data.message || 'Action cannot be performed', 'error');
            }
        })
        .catch(error => {
            checkbox.disabled = false;
            checkbox.checked = previousState;
            showStatusToast(error.message || 'Network error occurred while toggling status', 'error');
        });
    }

    /**
     * Dynamically update KPI counter cards on top
     */
    function updateKpiCounters(direction) {
        const activeEl = document.getElementById('statActiveCount');
        const inactiveEl = document.getElementById('statInactiveCount');
        if (activeEl && inactiveEl) {
            let active = parseInt(activeEl.textContent) || 0;
            let inactive = parseInt(inactiveEl.textContent) || 0;
            if (direction === 1) {
                activeEl.textContent = Math.max(0, active + 1);
                inactiveEl.textContent = Math.max(0, inactive - 1);
            } else {
                activeEl.textContent = Math.max(0, active - 1);
                inactiveEl.textContent = Math.max(0, inactive + 1);
            }
        }
    }

    /**
     * Show clean floating toast notification
     */
    function showStatusToast(message, type) {
        const container = document.getElementById('statusToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'status-toast text-white';
        
        if (type === 'success') {
            toast.style.background = '#065F46';
            toast.innerHTML = `<i class="fa-solid fa-circle-check fs-6 text-white"></i> <span>${message}</span>`;
        } else {
            toast.style.background = '#991B1B';
            toast.innerHTML = `<i class="fa-solid fa-triangle-exclamation fs-6 text-white"></i> <span>${message}</span>`;
        }

        container.appendChild(toast);

        // Auto remove toast after 3.5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }
</script>
@endpush --}}
