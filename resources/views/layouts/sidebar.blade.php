<aside class="sidebar-vertical" id="sidebarVertical">
    <!-- Sidebar Brand Header (Logo Only - Enlarged) -->
    <div class="sidebar-brand-box">
        <a href="{{ route('dashboard') }}" class="sidebar-brand-link" title="TTC Robotronics">
            <div class="sidebar-logo-card">
                <img src="{{ asset('assets/images/ttc-logo.png') }}" alt="TTC Robotronics" class="sidebar-brand-img">
            </div>
        </a>
        <button type="button" class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn" onclick="closeSidebarMobile(event)" aria-label="Close Sidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Navigation Links Container -->
    <div class="sidebar-nav-container">
        <ul class="sidebar-menu-list">
            <li class="sidebar-item">
                <a href="{{ route('dashboard') }}" class="sidebar-link active">
                    <i class="fa-solid fa-chart-pie nav-icon"></i>
                    <span>Dashboard</span>
                    <span class="sidebar-badge text-white" style="background-color: var(--primary-red);">Live</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#ordersCollapse" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#ordersCollapse" role="button" aria-expanded="false" aria-controls="ordersCollapse">
                    <i class="fa-solid fa-cart-shopping nav-icon"></i>
                    <span>Manage Orders</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>
                <div class="collapse" id="ordersCollapse">
                    <ul class="sidebar-submenu-list">
                        <li class="submenu-item">
                            <a href="#all-orders" class="submenu-link">
                                <span class="submenu-dot"></span>
                                <span>Purchase Orders</span>
                                <span class="sidebar-badge text-white ms-auto" style="background-color: var(--accent-amber); font-size: 0.65rem;">5</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="#create-order" class="submenu-link">
                                <span class="submenu-dot"></span>
                                <span>Sale Orders</span>
                                <span class="sidebar-badge text-white ms-auto" style="background-color: var(--accent-amber); font-size: 0.65rem;">5</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="#pending-orders" class="submenu-link">
                                <span class="submenu-dot"></span>
                                <span>Sale Orders Sizes</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
             <li class="sidebar-item">
                <a href="#usersCollapse" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#usersCollapse" role="button" aria-expanded="{{ request()->routeIs('users.*') ? 'true' : 'false' }}" aria-controls="usersCollapse">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span>Manage Users</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('users.*') ? 'show' : '' }}" id="usersCollapse">
                    <ul class="sidebar-submenu-list">
                        <li class="submenu-item">
                            <a href="{{ route('users.index') }}" class="submenu-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>All Users</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('users.create') }}" class="submenu-link {{ request()->routeIs('users.create') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Add User / Role</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <!-- Sidebar Bottom User Profile Card -->
    <div class="sidebar-footer-widget">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->first_name ?? $user->first_name ?? 'A', 0, 1)) }}
        </div>
        <div class="user-info-text text-truncate">
            <div class="user-name text-truncate text-white fw-bold" style="font-size: 0.85rem;">{{ auth()->user()->name ?? $user->name ?? 'User' }}</div>
            <div class="user-role" style="font-size: 0.7rem; color: #94A3B8;">{{ auth()->user()->role->name ?? $user->role->name ?? 'Admin' }}</div>
        </div>
    </div>
</aside>
