@php
    $role = strtolower(auth()->user()->role->name ?? '');
    $productsActive = request()->routeIs('brands*', 'products*', 'sizes*', 'grades*');
    $locationsActive = request()->routeIs('cities*', 'states*');
    $usersActive = request()->routeIs('users.*');
@endphp

<aside class="sidebar-vertical" id="sidebarVertical">

    <!-- Sidebar Brand Header -->
    <div class="sidebar-brand-box">
        <a href="{{ route('dashboard') }}" class="sidebar-brand-link" title="TTC Robotronics">
            <div class="sidebar-logo-card">
                <img src="{{ asset('assets/images/ttc-logo.png') }}"
                     alt="TTC Robotronics"
                     class="sidebar-brand-img">
            </div>
        </a>

        <button type="button"
                class="sidebar-close-btn d-lg-none"
                id="sidebarCloseBtn"
                onclick="closeSidebarMobile(event)"
                aria-label="Close Sidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Navigation Links -->
    <div class="sidebar-nav-container">
        <ul class="sidebar-menu-list">

            {{-- Dashboard: ADMIN + SALE --}}
            @if(in_array($role, ['admin', 'sale']))
                <li class="sidebar-item">
                    <a href="{{ route('dashboard') }}"
                       class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie nav-icon"></i>
                        <span>Dashboard</span>

                        @if($role === 'admin')
                            <span class="sidebar-badge text-white"
                                  style="background-color: var(--primary-red);">
                                Live
                            </span>
                        @endif
                    </a>
                </li>
            @endif


            {{-- =========================
                MANAGE ORDERS
            ========================== --}}
            @if(in_array($role, ['admin', 'sale']))
                <li class="sidebar-item">

                    <a href="#ordersCollapse"
                       class="sidebar-link {{ request()->is('orders*') ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       data-bs-target="#ordersCollapse"
                       role="button"
                       aria-expanded="{{ request()->is('orders*') ? 'true' : 'false' }}"
                       aria-controls="ordersCollapse">

                        <i class="fa-solid fa-cart-shopping nav-icon"></i>
                        <span>Manage Orders</span>
                        <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                    </a>

                    <div class="collapse {{ request()->is('orders*') ? 'show' : '' }}"
                         id="ordersCollapse">

                        <ul class="sidebar-submenu-list">

                            {{-- Purchase Orders: ADMIN ONLY --}}
                            @if($role === 'admin')
                                <li class="submenu-item">
                                    <a href="#all-orders" class="submenu-link">
                                        <span class="submenu-dot"></span>
                                        <span>Purchase Orders</span>

                                        <span class="sidebar-badge text-white ms-auto"
                                              style="background-color: var(--accent-amber); font-size: 0.65rem;">
                                            5
                                        </span>
                                    </a>
                                </li>
                            @endif

                            {{-- Sale Orders: ADMIN + SALE --}}
                            <li class="submenu-item">
                                <a href="#create-order" class="submenu-link">
                                    <span class="submenu-dot"></span>
                                    <span>Sale Orders</span>

                                    <span class="sidebar-badge text-white ms-auto"
                                          style="background-color: var(--accent-amber); font-size: 0.65rem;">
                                        5
                                    </span>
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
            @endif


            {{-- =========================
                MANAGE PRODUCTS
            ========================== --}}
            <li class="sidebar-item">

                <a href="#productsCollapse"
                   class="sidebar-link {{ $productsActive ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   data-bs-target="#productsCollapse"
                   role="button"
                   aria-expanded="{{ $productsActive ? 'true' : 'false' }}"
                   aria-controls="productsCollapse">

                    <i class="fa-solid fa-boxes nav-icon"></i>
                    <span>Manage Products</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>

                <div class="collapse {{ $productsActive ? 'show' : '' }}"
                     id="productsCollapse">

                    <ul class="sidebar-submenu-list">

                        <li class="submenu-item">
                            <a href="{{ route('brands') }}"
                               class="submenu-link {{ request()->routeIs('brands*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Brands</span>
                            </a>
                        </li>

                        <li class="submenu-item">
                            <a href="{{ route('products') }}"
                               class="submenu-link {{ request()->routeIs('products*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Products</span>
                            </a>
                        </li>

                        <li class="submenu-item">
                            <a href="{{ route('sizes') }}"
                               class="submenu-link {{ request()->routeIs('sizes*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Sizes</span>
                            </a>
                        </li>

                        <li class="submenu-item">
                            <a href="{{ route('grades') }}"
                               class="submenu-link {{ request()->routeIs('grades*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Grades</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>


            {{-- =========================
                MANAGE LOCATIONS
            ========================== --}}
            <li class="sidebar-item">

                <a href="#locationsCollapse"
                   class="sidebar-link {{ $locationsActive ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   data-bs-target="#locationsCollapse"
                   role="button"
                   aria-expanded="{{ $locationsActive ? 'true' : 'false' }}"
                   aria-controls="locationsCollapse">

                    <i class="fa-solid fa-location-dot nav-icon"></i>
                    <span>Manage Locations</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>

                <div class="collapse {{ $locationsActive ? 'show' : '' }}"
                     id="locationsCollapse">

                    <ul class="sidebar-submenu-list">

                        <li class="submenu-item">
                            <a href="{{ route('cities') }}"
                               class="submenu-link {{ request()->routeIs('cities*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Cities</span>
                            </a>
                        </li>

                        <li class="submenu-item">
                            <a href="{{ route('states') }}"
                               class="submenu-link {{ request()->routeIs('states*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>States</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>


            {{-- =========================
                MANAGE USERS
            ========================== --}}
            @if(in_array($role, ['admin', 'sale']))
                <li class="sidebar-item">

                    <a href="#usersCollapse"
                       class="sidebar-link {{ $usersActive ? 'active' : '' }}"
                       data-bs-toggle="collapse"
                       data-bs-target="#usersCollapse"
                       role="button"
                       aria-expanded="{{ $usersActive ? 'true' : 'false' }}"
                       aria-controls="usersCollapse">

                        <i class="fa-solid fa-users nav-icon"></i>
                        <span>Manage Users</span>
                        <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                    </a>

                    <div class="collapse {{ $usersActive ? 'show' : '' }}"
                         id="usersCollapse">

                        <ul class="sidebar-submenu-list">

                            {{-- ALL USERS: ADMIN + SALE --}}
                            <li class="submenu-item">
                                <a href="{{ route('users.users') }}"
                                   class="submenu-link {{ request()->routeIs('users.users') ? 'active' : '' }}">
                                    <span class="submenu-dot"></span>
                                    <span>All Users</span>
                                </a>
                            </li>

                            {{-- ADD USER / ROLE: ADMIN ONLY --}}
                            @if($role === 'admin')
                                <li class="submenu-item">
                                    <a href="{{ route('users.add') }}"
                                       class="submenu-link {{ request()->routeIs('users.add') ? 'active' : '' }}">
                                        <span class="submenu-dot"></span>
                                        <span>Add User / Role</span>
                                    </a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </li>
            @endif

        </ul>
    </div>


    <!-- Sidebar Bottom User Profile -->
    <div class="sidebar-footer-widget">

        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}
        </div>

        <div class="user-info-text text-truncate">

            <div class="user-name text-truncate text-white fw-bold"
                 style="font-size: 0.85rem;">
                {{ auth()->user()->name ?? 'User' }}
            </div>

            <div class="user-role"
                 style="font-size: 0.7rem; color: #94A3B8;">
                {{ auth()->user()->role->name ?? 'Admin' }}
            </div>

        </div>

    </div>

</aside>