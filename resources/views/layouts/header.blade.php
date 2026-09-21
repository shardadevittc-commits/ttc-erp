<header class="header-navbar">
    <div class="d-flex align-items-center">
        <!-- EXACT DASH UI SIDEBAR TOGGLE BUTTON -->
        <button id="nav-toggle" class="nav-toggle-btn" type="button" onclick="toggleSidebar(event)" aria-label="Toggle Side Menu" title="Click to collapse / expand side menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="currentColor" class="bi bi-text-indent-left" viewBox="0 0 16 16">
                <path d="M2 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm.646 2.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L4.293 8 2.646 6.354a.5.5 0 0 1 0-.708zM7 6.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
            </svg>
        </button>

        <!-- Dash UI Search Bar -->
        <div class="header-search-wrap d-none d-md-block">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="search" class="header-search-input" placeholder="Search modules, trucks, heats, challans...">
            <span class="shortcut-badge">⌘K</span>
        </div>
    </div>

    <!-- Right Action Controls -->
    <div class="d-flex align-items-center gap-3">
        <!-- Theme Toggle Switcher (Dark / Light) -->
        <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="Toggle Light / Dark Mode">
            <i class="fa-solid fa-moon" id="themeToggleIcon"></i>
        </button>

        <!-- Live Shift Status -->
        <div class="system-pill-live d-none d-sm-inline-flex">
            <span class="live-dot"></span> Shift A • Active
        </div>

        <!-- User Profile Pill -->
        <div class="dropdown">
            <div class="user-nav-profile d-none d-md-flex" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                <div class="sidebar-user-avatar" style="width: 28px; height: 28px; font-size: 0.75rem;">
                    {{ strtoupper(substr(auth()->user()->first_name ?? $user->first_name ?? 'A', 0, 1)) }}
                </div>
                <span class="fw-semibold" style="font-size: 0.82rem; color: var(--text-heading);">{{ auth()->user()->name ?? $user->name ?? 'User' }}</span>
                <span class="badge border" style="font-size: 0.65rem; background: var(--red-subtle); color: var(--primary-red); border-color: var(--red-border) !important;">
                    {{ auth()->user()->role->name ?? $user->role->name ?? 'Admin' }}
                </span>
            </div>

            <style>
                @keyframes dropdownFadeIn {
                    from { opacity: 0; transform: translateY(-10px) scale(0.98); }
                    to { opacity: 1; transform: translateY(0) scale(1); }
                }
                .profile-dropdown-item {
                    transition: all 0.2s ease-in-out;
                    border-radius: 8px;
                    padding: 10px 12px;
                    margin: 2px 0;
                }
                .profile-dropdown-item:hover {
                    background-color: #f8f9fa;
                    transform: translateX(4px);
                }
                .avatar-gradient {
                    background: linear-gradient(135deg, #dc3545, #ff6b6b);
                    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
                }
            </style>
            
            <div class="dropdown-menu dropdown-menu-end shadow-lg" style="width: 380px; height: max-content !important; min-height: auto !important; border-radius: 16px; border: 1px solid rgba(0,0,0,0.08); padding: 24px; z-index: 1050; margin-top: 15px; background: #ffffff; animation: dropdownFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                <div style="background: linear-gradient(145deg, #fff5f5, #ffffff); border: 1px solid rgba(254, 205, 211, 0.6); border-radius: 12px; padding: 24px 20px; text-align: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div class="avatar-gradient" style="width: 68px; height: 68px; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: bold; margin: 0 auto 12px; border: 3px solid #ffffff;">
                        {{ strtoupper(substr(auth()->user()->first_name ?? $user->first_name ?? 'A', 0, 1)) }}
                    </div>
                    <h6 style="margin: 0; font-weight: 700; font-size: 1.15rem; color: #111827; letter-spacing: -0.3px;">{{ auth()->user()->name ?? $user->name ?? 'User' }}</h6>
                    <div style="margin-top: 10px;">
                        <span class="badge" style="background: rgba(220, 53, 69, 0.1); color: #dc3545; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid rgba(220, 53, 69, 0.2); letter-spacing: 0.5px;">ROLE: {{ strtoupper(auth()->user()->role->name ?? $user->role->name ?? 'ADMIN') }}</span>
                    </div>
                </div>
                
                <div style="font-size: 0.875rem; color: #4b5563;">
                    <div class="d-flex justify-content-between profile-dropdown-item">
                        <span style="color: #6b7280; font-weight: 500;"><i class="fa-solid fa-user me-2" style="color: #9ca3af; width: 16px;"></i>Username</span>
                        <span class="fw-semibold" style="color: #111827;">{{ auth()->user()->username ?? 'admin' }}</span>
                    </div>
                    <div class="d-flex justify-content-between profile-dropdown-item">
                        <span style="color: #6b7280; font-weight: 500;"><i class="fa-solid fa-envelope me-2" style="color: #9ca3af; width: 16px;"></i>Email</span>
                        <span class="fw-semibold" style="color: #111827;">{{ auth()->user()->email ?? 'admin@gmail.com' }}</span>
                    </div>
                    <div class="d-flex justify-content-between profile-dropdown-item">
                        <span style="color: #6b7280; font-weight: 500;"><i class="fa-solid fa-phone me-2" style="color: #9ca3af; width: 16px;"></i>Phone</span>
                        <span class="fw-semibold" style="color: #111827;">{{ auth()->user()->phone_number ?? '+91-9876543210' }}</span>
                    </div>
                    <div class="d-flex justify-content-between profile-dropdown-item">
                        <span style="color: #6b7280; font-weight: 500;"><i class="fa-solid fa-location-dot me-2" style="color: #9ca3af; width: 16px;"></i>Location</span>
                        <span class="fw-semibold" style="color: #111827;">Plant Head Office</span>
                    </div>
                    <div class="d-flex justify-content-between profile-dropdown-item">
                        <span style="color: #6b7280; font-weight: 500;"><i class="fa-solid fa-clock-rotate-left me-2" style="color: #9ca3af; width: 16px;"></i>Last Login</span>
                        <span class="fw-semibold" style="color: #111827;">{{ now()->format('Y-m-d H:i') }}</span>
                    </div>
                </div>

                <div style="margin-top: 18px; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.1); border-radius: 8px; padding: 10px 14px; font-size: 0.75rem; color: #059669; display: flex; align-items: center; justify-content: center; font-weight: 500;">
                    <i class="fa-solid fa-shield-halved me-2 text-success" style="font-size: 0.9rem;"></i>
                    <span>Secure login via {{ request()->ip() }}. Audit trail active.</span>
                </div>
            </div>
        </div>
        <!-- Logout Button -->
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn-logout" title="Sign out of system">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </button>
        </form>
    </div>
</header>
