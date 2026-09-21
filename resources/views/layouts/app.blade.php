<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard | TTC Robotronics - Steel Industry ERP')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Extracted Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Chart.js for Dash UI Style Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
    @stack('styles')
</head>
<body>

    <div id="main-wrapper">
        <!-- Mobile Overlay Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebarMobile(event)"></div>

        <!-- Master Left Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Master Page Content Wrapper -->
        <div class="page-content-wrapper" id="pageContentWrapper">
            
            <!-- Master Top Header Navbar -->
            @include('layouts.header')

            <!-- Main Page Dynamic Body -->
            <main class="container-fluid py-4 flex-grow-1 px-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 rounded-3 py-3" role="alert" style="background-color: var(--accent-green-subtle); color: var(--accent-green); border-left: 4px solid var(--accent-green) !important;">
                        <i class="fa-solid fa-circle-check fs-5 me-2"></i>
                        <div class="fw-semibold">{{ session('success') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 rounded-3 py-3" role="alert" style="background-color: var(--red-subtle); color: var(--primary-red); border-left: 4px solid var(--primary-red) !important;">
                        <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                        <div class="fw-semibold">{{ session('error') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Master Footer -->
            @include('layouts.footer')
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Interactive Dash UI Master Scripts -->
    <script>
        // 1. GLOBAL SIDEBAR TOGGLE WITH DEBOUNCE GUARD
        let lastSidebarToggleTime = 0;

        function toggleSidebar(e) {
            if (e) {
                if (typeof e.preventDefault === 'function') e.preventDefault();
                if (typeof e.stopPropagation === 'function') e.stopPropagation();
            }

            // Strictly prevent double-execution within 300ms
            const now = Date.now();
            if (now - lastSidebarToggleTime < 300) {
                return;
            }
            lastSidebarToggleTime = now;

            const mainWrapper = document.getElementById('main-wrapper');
            if (!mainWrapper) return;

            if (window.innerWidth >= 992) {
                // Desktop toggle
                if (mainWrapper.classList.contains('sidebar-collapsed')) {
                    mainWrapper.classList.remove('sidebar-collapsed');
                } else {
                    mainWrapper.classList.add('sidebar-collapsed');
                }
            } else {
                // Mobile toggle
                if (mainWrapper.classList.contains('mobile-open')) {
                    mainWrapper.classList.remove('mobile-open');
                } else {
                    mainWrapper.classList.add('mobile-open');
                }
            }
        }
        window.toggleSidebar = toggleSidebar;

        function closeSidebarMobile(e) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            const mainWrapper = document.getElementById('main-wrapper');
            if (mainWrapper) {
                mainWrapper.classList.remove('mobile-open');
            }
        }
        window.closeSidebarMobile = closeSidebarMobile;

        // 2. DOM INITIALIZATION
        document.addEventListener('DOMContentLoaded', function () {
            const mainWrapper = document.getElementById('main-wrapper');
            const navToggleBtn = document.getElementById('nav-toggle');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeToggleIcon = document.getElementById('themeToggleIcon');

            // Ensure sidebar starts OPEN by default
            try {
                localStorage.removeItem('ttc_sidebar_collapsed');
            } catch (err) {}
            if (mainWrapper) {
                mainWrapper.classList.remove('sidebar-collapsed');
                mainWrapper.classList.remove('mobile-open');
            }

            // Attach single onclick handler to nav-toggle
            if (navToggleBtn) {
                navToggleBtn.onclick = toggleSidebar;
            }
            if (sidebarCloseBtn) {
                sidebarCloseBtn.onclick = closeSidebarMobile;
            }
            if (sidebarBackdrop) {
                sidebarBackdrop.onclick = closeSidebarMobile;
            }

            // Keyboard Shortcut: Ctrl + B or Esc
            document.addEventListener('keydown', function (e) {
                if (e.ctrlKey && e.key.toLowerCase() === 'b') {
                    e.preventDefault();
                    toggleSidebar();
                } else if (e.key === 'Escape' && mainWrapper && mainWrapper.classList.contains('mobile-open')) {
                    mainWrapper.classList.remove('mobile-open');
                }
            });

            // Theme Management
            let savedTheme = 'light';
            try {
                savedTheme = localStorage.getItem('ttc_theme') || 'light';
            } catch (err) {}

            function applyTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                if (themeToggleIcon && themeToggleBtn) {
                    if (theme === 'dark') {
                        themeToggleIcon.className = 'fa-solid fa-sun';
                        themeToggleBtn.title = 'Switch to Light Theme';
                    } else {
                        themeToggleIcon.className = 'fa-solid fa-moon';
                        themeToggleBtn.title = 'Switch to Dark Theme';
                    }
                }
                if (typeof window.updateChartsTheme === 'function') {
                    window.updateChartsTheme(theme);
                }
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function () {
                    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    applyTheme(nextTheme);
                    try {
                        localStorage.setItem('ttc_theme', nextTheme);
                    } catch (err) {}
                });
            }

            // Apply saved theme on boot
            applyTheme(savedTheme);
        });
    </script>
    @stack('scripts')
</body>
</html>
