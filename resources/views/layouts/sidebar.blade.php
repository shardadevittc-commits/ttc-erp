@php
    $role = strtolower(auth()->user()->role->name ?? '');
    $productsActive = request()->routeIs('brands*', 'products*', 'sizes*', 'grades*');
    $locationsActive = request()->routeIs('cities*', 'states*');
    $usersActive = request()->routeIs('users.*');
@endphp

<aside class="sidebar-vertical" id="sidebarVertical">

    <!-- Sidebar Brand -->
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

    <!-- Navigation -->
    <div class="sidebar-nav-container">
        <ul class="sidebar-menu-list">

            <!-- Dashboard -->
            <li class="sidebar-item">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie nav-icon"></i>
                    <span>Dashboard</span>
                    <span class="sidebar-badge text-white" style="background-color: var(--primary-red);">Live</span>
                </a>
            </li>

            <!-- Manage Orders -->
            @php
                $ordersActive = request()->routeIs('saleOrders*');
            @endphp

            <li class="sidebar-item">
                <a href="#ordersCollapse" class="sidebar-link {{ $ordersActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#ordersCollapse" role="button" aria-expanded="{{ $ordersActive ? 'true' : 'false' }}" aria-controls="ordersCollapse">
                    <i class="fa-solid fa-cart-shopping nav-icon"></i>
                    <span>Manage Orders</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>

                <div class="collapse {{ $ordersActive ? 'show' : '' }}" id="ordersCollapse">
                    <ul class="sidebar-submenu-list">

                        <!-- Sale Orders -->
                        <li class="submenu-item">
                            <a href="{{ route('saleOrders') }}" class="submenu-link {{ request()->routeIs('saleOrders*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Sale Orders</span>
                            </a>
                        </li>

                        <!-- Sale Order Items -->
                        {{-- <li class="submenu-item">
                            <a href="{{ route('saleOrderItems') }}" class="submenu-link {{ request()->routeIs('saleOrderItems*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Sale Order Items</span>
                            </a>
                        </li> --}}

                        <!-- Purchase Orders -->
                        {{-- <li class="submenu-item">
                            <a href="{{ route('purchaseOrders') }}" class="submenu-link {{ request()->routeIs('purchaseOrders*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Purchase Orders</span>
                            </a>
                        </li> --}}

                    </ul>
                </div>
            </li>

            <!-- Manage Customers -->
            <li class="sidebar-item">
                <a href="{{ route('customers') }}" class="sidebar-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span>Customers</span>
                </a>
            </li>

            <!-- Manage Products -->
            @php
                $productsActive = request()->routeIs('brands*', 'products*', 'sizes*', 'grades*', 'units*');
            @endphp
            <li class="sidebar-item">
                <a href="#productsCollapse" class="sidebar-link {{ $productsActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#productsCollapse" role="button" aria-expanded="{{ $productsActive ? 'true' : 'false' }}" aria-controls="productsCollapse">
                    <i class="fa-solid fa-boxes nav-icon"></i>
                    <span>Manage Products</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>

                <div class="collapse {{ $productsActive ? 'show' : '' }}" id="productsCollapse">
                    <ul class="sidebar-submenu-list">
                        <!-- Brands -->
                        <li class="submenu-item">
                            <a href="{{ route('brands') }}" class="submenu-link {{ request()->routeIs('brands*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Brands</span>
                            </a>
                        </li>

                        <!-- Products -->
                        <li class="submenu-item">
                            <a href="{{ route('products') }}"
                               class="submenu-link {{ request()->routeIs('products*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Products</span>
                            </a>
                        </li>

                        <!-- Sizes -->
                        <li class="submenu-item">
                            <a href="{{ route('sizes') }}"
                               class="submenu-link {{ request()->routeIs('sizes*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Sizes</span>
                            </a>
                        </li>

                        <!-- Grades -->
                        <li class="submenu-item">
                            <a href="{{ route('grades') }}"
                               class="submenu-link {{ request()->routeIs('grades*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Grades</span>
                            </a>
                        </li>

                        <!-- Units -->
                        <li class="submenu-item">
                            <a href="{{ route('units') }}" class="submenu-link {{ request()->routeIs('units*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Units</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- Manage Locations -->
            @php
                $locationsActive = request()->routeIs('cities*', 'states*');
            @endphp

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

                <div class="collapse {{ $locationsActive ? 'show' : '' }}" id="locationsCollapse">
                    <ul class="sidebar-submenu-list">

                        <!-- Cities -->
                        <li class="submenu-item">
                            <a href="{{ route('cities') }}"
                               class="submenu-link {{ request()->routeIs('cities*') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Cities</span>
                            </a>
                        </li>

                        <!-- States -->
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

            <!-- Manage Users -->
            @php
                $usersActive = request()->routeIs('users.*');
            @endphp
            <li class="sidebar-item">
                <a href="#usersCollapse" class="sidebar-link {{ $usersActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#usersCollapse" role="button" aria-expanded="{{ $usersActive ? 'true' : 'false' }}" aria-controls="usersCollapse">
                    <i class="fa-solid fa-users nav-icon"></i>
                    <span>Manage Users</span>
                    <i class="fa-solid fa-chevron-down submenu-arrow"></i>
                </a>

                <div class="collapse {{ $usersActive ? 'show' : '' }}" id="usersCollapse">
                    <ul class="sidebar-submenu-list">

                        <!-- All Users -->
                        <li class="submenu-item">
                            <a href="{{ route('users.users') }}" class="submenu-link {{ request()->routeIs('users.users') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>All Users</span>
                            </a>
                        </li>

                        <!-- Add User -->
                        <li class="submenu-item">
                            <a href="{{ route('users.add') }}" class="submenu-link {{ request()->routeIs('users.add') ? 'active' : '' }}">
                                <span class="submenu-dot"></span>
                                <span>Add User / Role</span>
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

    <!-- Sidebar Footer / User Profile -->
    <div class="sidebar-footer-widget">
        <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->first_name ?? ($user->first_name ?? 'A'), 0, 1)) }}</div>
        <div class="user-info-text text-truncate">
            <div class="user-name text-truncate text-white fw-bold" style="font-size: 0.85rem;">{{ auth()->user()->name ?? ($user->name ?? 'User') }}</div>
            <div class="user-role" style="font-size: 0.7rem; color: #94A3B8;">{{ auth()->user()->role->name ?? ($user->role->name ?? 'Admin') }}</div>
        </div>

    </div>

</aside>
