@php
    $role = strtolower(auth()->user()->role->name ?? '');
    $roleId = (int)(auth()->user()->role_id ?? 0);
    $isGateUser   = $roleId === 4 ;
    $isWeightUser = $roleId === 5;
    $isUnloadUser = $roleId === 6;
    $isLabUser    = $roleId === 8;
    $isDispatchUser = $roleId === 7;

    $productsActive = request()->routeIs('brands*','products*','sizes*','grades*','units*', 'grades.composition*', 'grade_properties*', 'grade_min_max*');
    $locationsActive = request()->routeIs('cities*','states*');
    $usersActive = request()->routeIs('users.*');
    $ordersActive = request()->routeIs('saleOrders*', 'purchase*');
@endphp

<aside class="sidebar-vertical" id="sidebarVertical">
    <div class="sidebar-brand-box">
        <a href="{{ $isGateUser ? route('dashboard.gate') : route('dashboard') }}" class="sidebar-brand-link" title="TTC Robotronics">
            <div class="sidebar-logo-card"><img src="{{ asset('assets/images/ttc-logo.png') }}" alt="TTC Robotronics" class="sidebar-brand-img"></div>
        </a>
        <button type="button" class="sidebar-close-btn d-lg-none" id="sidebarCloseBtn" onclick="closeSidebarMobile(event)" aria-label="Close Sidebar"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="sidebar-nav-container">
        <ul class="sidebar-menu-list">

            @if($isGateUser)

                <li class="sidebar-item">
                    <a href="{{ route('dashboard.gate') }}" class="sidebar-link {{ request()->routeIs('dashboard.gate') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('gate.index') }}" class="sidebar-link {{ request()->routeIs('gate.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-right-to-bracket nav-icon"></i>
                        <span>Vehicle Management</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#reportsCollapse" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#reportsCollapse" role="button" aria-expanded="false" aria-controls="reportsCollapse"><i class="fa-solid fa-chart-column nav-icon"></i><span>Reports</span><i class="fa-solid fa-chevron-down submenu-arrow"></i></a>
                    <div class="collapse" id="reportsCollapse">
                        <ul class="sidebar-submenu-list">
                            <li class="submenu-item"><a href="" class="submenu-link"><span class="submenu-dot"></span><span>History</span></a></li>
                        </ul>
                    </div>
                </li>
            @endif
            @if($isWeightUser)

                <li class="sidebar-item">
                    <a href="{{ route('dashboard.weight') }}" class="sidebar-link {{ request()->routeIs('dashboard.weight') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('weight.index') }}" class="sidebar-link {{ request()->routeIs('weight.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-weight-scale nav-icon"></i>
                        <span>Weight Management</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#reportsCollapse" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#reportsCollapse" role="button" aria-expanded="false" aria-controls="reportsCollapse"><i class="fa-solid fa-chart-column nav-icon"></i><span>Reports</span><i class="fa-solid fa-chevron-down submenu-arrow"></i></a>
                    <div class="collapse" id="reportsCollapse">
                        <ul class="sidebar-submenu-list">
                            <li class="submenu-item"><a href="{{ route('weight.history') }}" class="submenu-link {{ request()->routeIs('weight.history') ? 'active' : '' }}"><span class="submenu-dot"></span><span>History</span></a></li>
                        </ul>
                    </div>
                </li>
            @endif

            @if($isUnloadUser)
                <li class="sidebar-item">
                    <a href="{{ route('dashboard.unloader') }}" class="sidebar-link {{ request()->routeIs('dashboard.unloader') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('unloader.index') }}" class="sidebar-link {{ request()->routeIs('unloader.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-truck-pickup nav-icon"></i>
                        <span>Unload Materials</span>
                    </a>
                </li>
            @endif

            @if($isLabUser)
                <li class="sidebar-item">
                    <a href="{{ route('dashboard.lab') }}" class="sidebar-link {{ request()->routeIs('dashboard.lab') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('lab.index') }}" class="sidebar-link {{ request()->routeIs('lab.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-right-to-bracket nav-icon"></i>
                        <span>Lab Management</span>
                    </a>
                </li>
            @endif

            @if($isDispatchUser)
                <li class="sidebar-item">
                    <a href="{{ route('dashboard.dispatch') }}" class="sidebar-link {{ request()->routeIs('dashboard.dispatch') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-high nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('dispatch.index') }}" class="sidebar-link {{ request()->routeIs('dispatch.index') ? 'active' : '' }}">
                        <i class="fa-solid fa-right-to-bracket nav-icon"></i>
                        <span>Dispatch Management</span>
                    </a>
                </li>
            @endif

            @if(in_array($role,['admin','sale']))
                <li class="sidebar-item"><a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-pie nav-icon"></i><span>Dashboard</span><span class="sidebar-badge text-white" style="background-color:var(--primary-red);">Live</span></a></li>

                <li class="sidebar-item">
                    <a href="#ordersCollapse" class="sidebar-link {{ $ordersActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#ordersCollapse" role="button" aria-expanded="{{ $ordersActive ? 'true' : 'false' }}" aria-controls="ordersCollapse"><i class="fa-solid fa-cart-shopping nav-icon"></i><span>Manage Orders</span><i class="fa-solid fa-chevron-down submenu-arrow"></i></a>
                    <div class="collapse {{ $ordersActive ? 'show' : '' }}" id="ordersCollapse">
                        <ul class="sidebar-submenu-list">
                            <li class="submenu-item"><a href="{{ route('saleOrders') }}" class="submenu-link {{ request()->routeIs('saleOrders*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Sale Orders</span></a></li>
                            <li class="submenu-item"><a href="{{ route('purchase') }}" class="submenu-link {{ request()->routeIs('purchase*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Purchase Orders</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('customers') }}" class="sidebar-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
                        <i class="fa-solid fa-users nav-icon"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('rawMaterials') }}" class="sidebar-link {{ request()->routeIs('rawMaterials*') ? 'active' : '' }}">
                        <i class="fa-solid fa-boxes-stacked nav-icon"></i>
                        <span>RM Stock</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('vehicleHistories') }}" class="sidebar-link {{ request()->routeIs('vehicleHistories*') ? 'active' : '' }}">
                        <i class="fa-solid fa-truck nav-icon"></i>
                        <span>Vehicle History</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="#productsCollapse" class="sidebar-link {{ $productsActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#productsCollapse" role="button" aria-expanded="{{ $productsActive ? 'true' : 'false' }}" aria-controls="productsCollapse"><i class="fa-solid fa-boxes nav-icon"></i><span>Manage Products</span><i class="fa-solid fa-chevron-down submenu-arrow"></i></a>
                    <div class="collapse {{ $productsActive ? 'show' : '' }}" id="productsCollapse">
                        <ul class="sidebar-submenu-list">
                            <li class="submenu-item"><a href="{{ route('brands') }}" class="submenu-link {{ request()->routeIs('brands*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Brands</span></a></li>
                            <li class="submenu-item"><a href="{{ route('products') }}" class="submenu-link {{ request()->routeIs('products*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Products</span></a></li>
                            <li class="submenu-item"><a href="{{ route('sizes') }}" class="submenu-link {{ request()->routeIs('sizes*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Sizes</span></a></li>
                            <li class="submenu-item"><a href="{{ route('grades') }}" class="submenu-link {{ request()->routeIs('grades*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Grades</span></a></li>
                            <li class="submenu-item"><a href="{{ route('grades.composition') }}" class="submenu-link {{ request()->routeIs('grades.composition*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Grade Compositions</span></a></li>
                            <li class="submenu-item"><a href="{{ route('grade_properties') }}" class="submenu-link {{ request()->routeIs('grade_properties*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Grade Properties</span></a></li>
                            <li class="submenu-item"><a href="{{ route('grade_min_max') }}" class="submenu-link {{ request()->routeIs('grade_min_max*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Grade Min/Max Values</span></a></li>
                            <li class="submenu-item"><a href="{{ route('units') }}" class="submenu-link {{ request()->routeIs('units*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Units</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-item">
                    <a href="#locationsCollapse" class="sidebar-link {{ $locationsActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#locationsCollapse" role="button" aria-expanded="{{ $locationsActive ? 'true' : 'false' }}" aria-controls="locationsCollapse"><i class="fa-solid fa-location-dot nav-icon"></i><span>Manage Locations</span><i class="fa-solid fa-chevron-down submenu-arrow"></i></a>
                    <div class="collapse {{ $locationsActive ? 'show' : '' }}" id="locationsCollapse">
                        <ul class="sidebar-submenu-list">
                            <li class="submenu-item"><a href="{{ route('cities') }}" class="submenu-link {{ request()->routeIs('cities*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Cities</span></a></li>
                            <li class="submenu-item"><a href="{{ route('states') }}" class="submenu-link {{ request()->routeIs('states*') ? 'active' : '' }}"><span class="submenu-dot"></span><span>States</span></a></li>
                        </ul>
                    </div>
                </li>


                <li class="sidebar-item">
                    <a href="#usersCollapse" class="sidebar-link {{ $usersActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#usersCollapse" role="button" aria-expanded="{{ $usersActive ? 'true' : 'false' }}" aria-controls="usersCollapse"><i class="fa-solid fa-users nav-icon"></i><span>Manage Users</span><i class="fa-solid fa-chevron-down submenu-arrow"></i></a>
                    <div class="collapse {{ $usersActive ? 'show' : '' }}" id="usersCollapse">
                        <ul class="sidebar-submenu-list">
                            <li class="submenu-item"><a href="{{ route('users.users') }}" class="submenu-link {{ request()->routeIs('users.users') ? 'active' : '' }}"><span class="submenu-dot"></span><span>All Users</span></a></li>
                            @if($role === 'admin')
                                <li class="submenu-item"><a href="{{ route('users.add') }}" class="submenu-link {{ request()->routeIs('users.add') ? 'active' : '' }}"><span class="submenu-dot"></span><span>Add User / Role</span></a></li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
    </div>

    <div class="sidebar-footer-widget">
        <div class="sidebar-user-avatar">{{ strtoupper(substr(auth()->user()->first_name ?? ($user->first_name ?? 'A'),0,1)) }}</div>
        <div class="user-info-text text-truncate">
            <div class="user-name text-truncate text-white fw-bold" style="font-size:0.85rem;">{{ auth()->user()->name ?? ($user->name ?? 'User') }}</div>
            <div class="user-role" style="font-size:0.7rem;color:#94A3B8;">{{ auth()->user()->role->name ?? ($user->role->name ?? 'Admin') }}</div>
        </div>
    </div>
</aside>
