@extends('layouts.app')

@section('title', 'Manage Users & Roles | TTC Robotronics - Steel Industry ERP')

@push('styles')

@endpush

@section('content')
    <!-- Floating Toast Notification Area -->
    <div id="statusToastContainer" class="status-toast-container"></div>

    <!-- Page Header & Action Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Users & Roles</li>
                </ol>
            </nav>
            <h2 class="fs-4 fw-bold m-0" style="color: var(--text-heading); letter-spacing: -0.3px;">
                <i class="fa-solid fa-users me-2 text-danger"></i>User & Role Management
            </h2>
            <p class="text-muted small m-0 mt-1">Manage system operators, assign plant department roles, and toggle Active/Inactive status directly from the list.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('users.create') }}" class="btn text-white px-3 py-2 fw-semibold rounded-3 shadow-sm" style="background-color: var(--primary-red); border-color: var(--primary-red);">
                <i class="fa-solid fa-user-plus me-1.5"></i> Add New User / Role
            </a>
        </div>
    </div>

    <!-- Quick Stat KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="p-3 rounded-3 border" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Total Users</span>
                        <h3 class="fs-4 fw-bold mt-1 mb-0" style="color: var(--text-heading);">{{ $stats['total'] }}</h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: var(--accent-blue-subtle); color: var(--accent-blue);">
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="p-3 rounded-3 border" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Active Operators</span>
                        <h3 id="statActiveCount" class="fs-4 fw-bold mt-1 mb-0" style="color: var(--accent-green);">{{ $stats['active'] }}</h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: var(--accent-green-subtle); color: var(--accent-green);">
                        <i class="fa-solid fa-user-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="p-3 rounded-3 border" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Inactive / Suspended</span>
                        <h3 id="statInactiveCount" class="fs-4 fw-bold mt-1 mb-0" style="color: var(--accent-amber);">{{ $stats['inactive'] }}</h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: var(--accent-amber-subtle); color: var(--accent-amber);">
                        <i class="fa-solid fa-user-slash fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="p-3 rounded-3 border" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Available Roles</span>
                        <h3 class="fs-4 fw-bold mt-1 mb-0" style="color: var(--primary-red);">{{ $stats['roles_count'] }}</h3>
                    </div>
                    <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: var(--red-subtle); color: var(--primary-red);">
                        <i class="fa-solid fa-shield-halved fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="card border mb-4 rounded-3" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text border-end-0" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search by name, username, email, phone..." style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <select name="role_id" class="form-select" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                        <option value="">-- All Assigned Roles --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                        <option value="">-- Status --</option>
                        <option value="{{ \App\Models\User::STATUS_ACTIVE }}" {{ request('status') == \App\Models\User::STATUS_ACTIVE ? 'selected' : '' }}>Active</option>
                        <option value="{{ \App\Models\User::STATUS_INACTIVE }}" {{ request('status') == \App\Models\User::STATUS_INACTIVE ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3" style="background: var(--accent-blue); border-color: var(--accent-blue);">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'role_id', 'status']))
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary rounded-3" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card border rounded-3 overflow-hidden" style="background: var(--bg-card); border-color: var(--border-card) !important; box-shadow: var(--card-shadow);">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="color: var(--text-body);">
                <thead style="background: var(--table-header); border-bottom: 1px solid var(--border-color);">
                    <tr class="text-uppercase small" style="color: var(--text-muted); font-size: 0.75rem; letter-spacing: 0.5px;">
                        <th class="ps-3 py-3" style="width: 50px;">#</th>
                        <th class="py-3">User Profile</th>
                        <th class="py-3">Role Assigned</th>
                        <th class="py-3">Contact Details</th>
                        <th class="py-3 text-center" style="width: 140px;">Active / Inactive</th>
                        <th class="py-3">Created</th>
                        <th class="pe-3 py-3 text-end" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $userItem)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td class="ps-3 fw-semibold text-muted" style="font-size: 0.85rem;">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $userItem->avatar_url }}" alt="{{ $userItem->name }}" class="rounded-circle border" style="width: 42px; height: 42px; object-fit: cover; border-color: var(--border-color) !important;">
                                    <div>
                                        <div class="fw-bold" style="color: var(--text-heading); font-size: 0.92rem;">
                                            {{ $userItem->name ?: 'Unnamed User' }}
                                        </div>
                                        <div class="text-muted small">
                                            <span class="badge px-1.5 py-0.5 rounded text-secondary" style="background: var(--bg-input); font-size: 0.72rem;">
                                                <i class="fa-solid fa-at me-1"></i>{{ $userItem->username }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($userItem->role)
                                    @php
                                        $roleSlug = strtolower($userItem->role->short_name ?? '');
                                        $badgeBg = 'var(--accent-blue-subtle)';
                                        $badgeColor = 'var(--accent-blue)';
                                        $badgeBorder = 'var(--accent-blue-border)';
                                        if ($roleSlug === 'admin') {
                                            $badgeBg = 'var(--red-subtle)';
                                            $badgeColor = 'var(--primary-red)';
                                            $badgeBorder = 'var(--red-border)';
                                        } elseif (in_array($roleSlug, ['gate', 'weight'])) {
                                            $badgeBg = 'rgba(14, 165, 233, 0.1)';
                                            $badgeColor = '#0284C7';
                                            $badgeBorder = 'rgba(14, 165, 233, 0.3)';
                                        } elseif (in_array($roleSlug, ['lab', 'production', 'lab_production'])) {
                                            $badgeBg = 'rgba(245, 158, 11, 0.1)';
                                            $badgeColor = '#D97706';
                                            $badgeBorder = 'rgba(245, 158, 11, 0.3)';
                                        } elseif (in_array($roleSlug, ['sale', 'purchase', 'account'])) {
                                            $badgeBg = 'rgba(16, 185, 129, 0.1)';
                                            $badgeColor = '#059669';
                                            $badgeBorder = 'rgba(16, 185, 129, 0.3)';
                                        }
                                    @endphp
                                    <span class="badge px-2.5 py-1.5 rounded-pill border fw-semibold" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border-color: {{ $badgeBorder }} !important; font-size: 0.75rem;">
                                        <i class="fa-solid fa-shield-cat me-1"></i>{{ $userItem->role->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Role</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column small">
                                    <span style="color: var(--text-heading);"><i class="fa-regular fa-envelope me-1.5 text-muted"></i>{{ $userItem->email }}</span>
                                    <span class="text-muted mt-0.5"><i class="fa-solid fa-phone me-1.5"></i>{{ $userItem->phone_number ?: 'Not provided' }}</span>
                                </div>
                            </td>
                            
                            <!-- Robust Interactive Toggle Switch Column -->
                            <td class="text-center">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <label class="erp-switch" title="Click to toggle Active / Inactive">
                                        <input type="checkbox" 
                                               id="statusSwitch{{ $userItem->id }}" 
                                               data-url="{{ route('users.toggle-status', $userItem->id) }}"
                                               data-user-id="{{ $userItem->id }}"
                                               data-user-name="{{ $userItem->name }}"
                                               {{ $userItem->status == \App\Models\User::STATUS_ACTIVE ? 'checked' : '' }}
                                               onchange="handleStatusToggle(this)">
                                        <span class="erp-slider"></span>
                                    </label>
                                    <span id="statusBadge{{ $userItem->id }}" 
                                          class="badge rounded-pill border px-2 py-0.5 mt-1" 
                                          style="{{ $userItem->status == \App\Models\User::STATUS_ACTIVE ? 'background: var(--accent-green-subtle); color: var(--accent-green); border-color: var(--accent-green-border) !important;' : 'background: var(--accent-amber-subtle); color: var(--accent-amber); border-color: var(--accent-amber-border) !important;' }} font-size: 0.68rem;">
                                        {{ $userItem->status == \App\Models\User::STATUS_ACTIVE ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>

                            <td class="small text-muted">
                                {{ $userItem->created_at ? $userItem->created_at->format('d M, Y') : '-' }}
                            </td>
                            <td class="pe-3 text-end">
                                <div class="d-inline-flex gap-1.5">
                                    <a href="{{ route('users.edit', $userItem->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-2.5 py-1" title="Edit User">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if(auth()->id() !== $userItem->id)
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2.5 py-1" title="Delete User" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $userItem->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteModal{{ $userItem->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow" style="background: var(--bg-card); color: var(--text-heading);">
                                                    <div class="modal-header border-bottom" style="border-color: var(--border-color) !important;">
                                                        <h5 class="modal-title fs-6 fw-bold">
                                                            <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Delete User Account
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start py-3">
                                                        <p class="mb-2">Are you sure you want to delete user <strong>{{ $userItem->name }}</strong> (<code>{{ $userItem->username }}</code>)?</p>
                                                        <p class="text-muted small m-0">This operator account will be soft-deleted from the ERP system and they won't be able to log in.</p>
                                                    </div>
                                                    <div class="modal-footer border-top" style="border-color: var(--border-color) !important;">
                                                        <button type="button" class="btn btn-sm btn-secondary rounded-2" data-bs-dismiss="modal">Cancel</button>
                                                        <form method="POST" action="{{ route('users.destroy', $userItem->id) }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger rounded-2">
                                                                <i class="fa-solid fa-trash me-1"></i> Yes, Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-user-slash fs-1 d-block mb-3 text-secondary opacity-50"></i>
                                <h5 class="fs-6 fw-bold">No Users Found</h5>
                                <p class="small text-muted mb-3">No user records matched your search or filters.</p>
                                <a href="{{ route('users.create') }}" class="btn btn-sm text-white px-3 py-1.5 rounded-3" style="background: var(--primary-red);">
                                    <i class="fa-solid fa-plus me-1"></i> Add First User
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center p-3 border-top" style="background: var(--bg-card); border-color: var(--border-color) !important;">
                <span class="small text-muted">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </span>
                <div>
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
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
@endpush
