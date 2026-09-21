<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login | TTC Robotronics - ERP</title>

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

    <style>
        :root {
            --primary-navy: #0B1F3A;
            --secondary-navy: #102A46;
            --card-navy: #1B304A;
            --primary-red: #E31B23;
            --dark-red: #B91C1C;
            --main-light: #F8FAFC;
            --secondary-light: #B8C5D6;
            --login-bg: #F8FAFC;

            --text-heading: #0F172A;
            --text-body: #475569;
            --text-muted: #64748B;
            --border-color: #CBD5E1;
            --border-light: #E2E8F0;
            --border-focus: #E31B23;
            --input-bg: #FFFFFF;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            --gradient-btn: linear-gradient(135deg, #E31B23 0%, #B91C1C 100%);
            --gradient-btn-hover: linear-gradient(135deg, #EF232C 0%, #991B1B 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #060e1a;
            color: var(--text-body);
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* Full Page Split Container */
        .login-split-page {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* -------------------------------------------------------------
           LEFT HERO PANEL (Industrial Steel Aesthetic - High Contrast)
        -------------------------------------------------------------- */
        .hero-panel {
            flex: 1 1 54%;
            position: relative;
            background: linear-gradient(135deg, rgba(11, 31, 58, 0.93) 0%, rgba(16, 42, 70, 0.88) 50%, rgba(7, 19, 36, 0.96) 100%),
                        url('{{ asset("assets/images/erw-pipes.jpg") }}') center center / cover no-repeat;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem 4.5rem;
            color: #F8FAFC;
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Subtle Blueprint Mesh Grid Overlay */
        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
            background-size: 36px 36px;
            pointer-events: none;
        }

        /* Ambient Glow Effect */
        .hero-panel::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            top: 20%;
            left: -100px;
            background: radial-gradient(circle, rgba(16, 42, 70, 0.35) 0%, transparent 70%);
            filter: blur(60px);
            pointer-events: none;
        }

        .hero-content-wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }

        /* Top Brand Pill */
        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(27, 48, 74, 0.6);
            border: 1px solid rgba(184, 197, 214, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 8px 18px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: #F8FAFC;
            width: fit-content;
        }

        .hero-badge-pill .pulse-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #38BDF8;
            box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7);
            animation: pulseDot 2s infinite;
        }

        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(56, 189, 248, 0); }
            100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
        }

        /* Main Hero Titles */
        .hero-middle {
            margin: auto 0;
            padding: 3rem 0;
        }

        .hero-headline {
            font-family: 'Outfit', sans-serif;
            font-size: 2.75rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.02em;
            margin-bottom: 1.25rem;
            color: #F8FAFC;
        }

        .hero-gradient-text {
            background: linear-gradient(135deg, #FFFFFF 0%, #B8C5D6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
            font-size: 2.1rem;
            font-weight: 600;
            margin-top: 0.35rem;
            letter-spacing: -0.01em;
        }

        .hero-lead-text {
            font-size: 1.05rem;
            line-height: 1.65;
            color: #B8C5D6;
            max-width: 520px;
            margin-bottom: 2.25rem;
            font-weight: 400;
        }

        /* Feature Matrix */
        .hero-features-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            max-width: 560px;
        }

        .feature-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: rgba(27, 48, 74, 0.45);
            border: 1px solid rgba(184, 197, 214, 0.15);
            backdrop-filter: blur(8px);
            padding: 14px 16px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(27, 48, 74, 0.7);
            border-color: rgba(184, 197, 214, 0.3);
            transform: translateY(-2px);
        }

        .feature-card-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: #1B304A;
            border: 1px solid rgba(184, 197, 214, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #B8C5D6;
            font-size: 0.95rem;
            flex-shrink: 0;
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .feature-card:hover .feature-card-icon {
            color: #F8FAFC;
            border-color: rgba(184, 197, 214, 0.4);
        }

        .feature-card-info h6 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #F8FAFC;
            margin: 0 0 2px 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .feature-card-info p {
            font-size: 0.8rem;
            color: #B8C5D6;
            margin: 0;
            line-height: 1.4;
        }

        /* Hero Footer */
        .hero-footer-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(184, 197, 214, 0.15);
            font-size: 0.82rem;
            color: #B8C5D6;
        }

        .hero-footer-bar .badge-industrial {
            color: #F8FAFC;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .hero-footer-bar .badge-industrial i {
            color: #B8C5D6;
        }

        /* -------------------------------------------------------------
           RIGHT FORM PANEL (Subtle Cool Off-White #F8FAFC)
        -------------------------------------------------------------- */
        .form-panel {
            flex: 1 1 46%;
            background-color: #F8FAFC;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem 4rem;
            overflow-y: auto;
            position: relative;
        }

        .login-inner-box {
            width: 100%;
            max-width: 440px;
            margin: auto;
        }

        /* Brand Showcase Header */
        .brand-showcase {
            text-align: center;
            margin-bottom: 2.25rem;
        }

        .logo-frame {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 26px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.25rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .logo-frame:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
            border-color: #CBD5E1;
        }

        .logo-frame img {
            height: 52px;
            width: auto;
            max-width: 270px;
            object-fit: contain;
            display: block;
        }

        .company-corporate-badge {
            display: inline-block;
            background: #EDF2F7;
            border: 1px solid #E2E8F0;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 0.5rem;
        }

        .brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 700;
            color: #0B1F3A;
            letter-spacing: -0.01em;
            margin-bottom: 0.25rem;
        }

        .brand-system-tag {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Welcome Section */
        .welcome-section {
            margin-bottom: 1.75rem;
            text-align: left;
        }

        .welcome-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: #0B1F3A;
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
        }

        .welcome-subtitle {
            font-size: 0.88rem;
            color: var(--text-muted);
            line-height: 1.5;
            margin: 0;
        }

        /* Modern Input Groups */
        .form-group-custom {
            margin-bottom: 1.35rem;
        }

        .form-label-custom {
            display: block;
            font-size: 0.83rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .input-control-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-control-box .lead-icon {
            position: absolute;
            left: 16px;
            color: #94A3B8;
            font-size: 0.95rem;
            transition: color 0.2s ease;
            pointer-events: none;
            z-index: 3;
        }

        .input-control-box .input-field {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 44px;
            background-color: #FFFFFF;
            border: 1.5px solid #CBD5E1;
            border-radius: 11px;
            font-size: 0.92rem;
            color: #0F172A;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
            outline: none;
        }

        .input-control-box .input-field::placeholder {
            color: #94A3B8;
            font-weight: 400;
            font-size: 0.88rem;
        }

        .input-control-box .input-field:hover {
            border-color: #94A3B8;
            background-color: #FFFFFF;
        }

        .input-control-box .input-field:focus {
            background-color: #FFFFFF;
            border-color: #E31B23;
            box-shadow: 0 0 0 3px rgba(227, 27, 35, 0.1);
        }

        .input-control-box:focus-within .lead-icon {
            color: #475569;
        }

        .input-control-box .input-field.is-invalid {
            border-color: #EF4444;
            background-color: #FFF5F5;
        }

        .input-control-box .input-field.is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        /* Password Toggle Button */
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #94A3B8;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 4;
        }

        .password-toggle-btn:hover {
            color: #1E293B;
            background-color: #F1F5F9;
        }

        /* Checkbox & Helpers */
        .form-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
            margin-bottom: 1.5rem;
        }

        .custom-checkbox-wrap {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            margin: 0;
        }

        .custom-checkbox-wrap input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 1.5px solid #CBD5E1;
            border-radius: 5px;
            background-color: #FFFFFF;
            cursor: pointer;
            display: grid;
            place-content: center;
            transition: all 0.2s ease;
            margin: 0;
        }

        .custom-checkbox-wrap input[type="checkbox"]::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            font-size: 10px;
            color: #FFFFFF;
            transform: scale(0);
            transition: transform 0.15s ease-in-out;
        }

        .custom-checkbox-wrap input[type="checkbox"]:checked {
            background-color: #E31B23;
            border-color: #E31B23;
        }

        .custom-checkbox-wrap input[type="checkbox"]:checked::before {
            transform: scale(1);
        }

        .custom-checkbox-wrap .checkbox-label-text {
            font-size: 0.85rem;
            font-weight: 500;
            color: #334155;
        }

        /* Modern Refined Red Login CTA Button */
        .btn-submit-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #E31B23 0%, #B91C1C 100%);
            border: none;
            border-radius: 12px;
            color: #FFFFFF;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 6px 18px -3px rgba(227, 27, 35, 0.35);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit-login:hover {
            background: linear-gradient(135deg, #EF232C 0%, #991B1B 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 22px -3px rgba(227, 27, 35, 0.45);
        }

        .btn-submit-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(227, 27, 35, 0.3);
        }

        .btn-submit-login .arrow-icon {
            font-size: 0.9rem;
            transition: transform 0.2s ease;
        }

        .btn-submit-login:hover .arrow-icon {
            transform: translateX(4px);
        }

        .btn-submit-login:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Error Feedback */
        .form-error-msg {
            color: #ef4444;
            font-size: 0.78rem;
            font-weight: 500;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Footer */
        .form-footer-copyright {
            text-align: center;
            padding-top: 2rem;
            font-size: 0.78rem;
            color: #94a3b8;
            font-weight: 400;
        }

        /* -------------------------------------------------------------
           RESPONSIVE BREAKPOINTS
        -------------------------------------------------------------- */
        @media (max-width: 1200px) {
            .hero-panel {
                padding: 3rem 2.5rem;
            }
            .form-panel {
                padding: 3rem 2.5rem;
            }
            .hero-headline {
                font-size: 2.3rem;
            }
            .hero-features-list {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }
        }

        @media (max-width: 991px) {
            .login-split-page {
                flex-direction: column;
            }
            .hero-panel {
                flex: none;
                min-height: auto;
                padding: 2.5rem 1.5rem;
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            .hero-middle {
                padding: 1.5rem 0;
            }
            .hero-headline {
                font-size: 1.9rem;
            }
            .hero-lead-text {
                font-size: 0.92rem;
                margin-bottom: 1.25rem;
            }
            .hero-features-list {
                display: none; /* Hide heavy feature cards on mobile to keep focus fast */
            }
            .hero-footer-bar {
                display: none;
            }
            .form-panel {
                flex: 1 1 auto;
                padding: 2.5rem 1.5rem;
                background-color: #ffffff;
            }
            .login-inner-box {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .hero-headline {
                font-size: 1.6rem;
            }
            .brand-name {
                font-size: 1.2rem;
            }
            .logo-frame img {
                height: 42px;
                max-width: 230px;
            }
            .welcome-title {
                font-size: 1.35rem;
            }
            .form-panel {
                padding: 2rem 1.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-split-page">
        <!-- ================= LEFT HERO PANEL ================= -->
        <div class="hero-panel">
            <div class="hero-content-wrapper">
                <!-- Top Badge Pill -->
                <div class="hero-badge-pill">
                    <span class="pulse-indicator"></span>
                    <span>TTC Robotronics ERP • Steel Industry Edition</span>
                </div>

                <!-- Middle Content -->
                <div class="hero-middle">
                    <h1 class="hero-headline">
                        TTC ERP
                        <span class="hero-gradient-text">Powerful Business Management.</span>
                    </h1>
                    <p class="hero-lead-text">
                        Manage your steel manufacturing, ERW pipes, inventory, and accounting operations with a centralized enterprise ERP solution.
                    </p>

                    <!-- Feature Matrix -->
                    <div class="hero-features-list">
                        <div class="feature-card">
                            <div class="feature-card-icon">
                                <i class="fa-solid fa-industry"></i>
                            </div>
                            <div class="feature-card-info">
                                <h6>Steel &amp; Inventory</h6>
                                <p>Real-time ERW pipes, coils &amp; raw material control</p>
                            </div>
                        </div>

                        <div class="feature-card">
                            <div class="feature-card-icon">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div class="feature-card-info">
                                <h6>Automated Vouchers</h6>
                                <p>Cash, bank, credit/debit notes &amp; GST compliance</p>
                            </div>
                        </div>

                        <div class="feature-card">
                            <div class="feature-card-icon">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <div class="feature-card-info">
                                <h6>Financial Ledgers</h6>
                                <p>Live party balances, aging &amp; multi-format reports</p>
                            </div>
                        </div>

                        <div class="feature-card">
                            <div class="feature-card-icon">
                                <i class="fa-solid fa-shield-check"></i>
                            </div>
                            <div class="feature-card-info">
                                <h6>Enterprise Security</h6>
                                <p>Granular role permissions &amp; encrypted sessions</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="hero-footer-bar">
                    <div class="badge-industrial">
                        <i class="fa-solid fa-cube"></i>
                        <span>DIVINE BRIGHT STEELS</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= RIGHT LOGIN FORM PANEL ================= -->
        <div class="form-panel">
            <div class="login-inner-box">
                <!-- Official Brand Showcase Header -->
                <div class="brand-showcase">
                    <div class="logo-frame">
                        <img src="{{ asset('assets/images/ttc-logo.png') }}" alt="TTC Robotronics Private Limited">
                    </div>
                    <div>
                        <span class="company-corporate-badge">ENTERPRISE SYSTEM</span>
                        <p class="brand-system-tag">Divine Bright Steels — Centralized Accounts ERP</p>
                    </div>
                </div>

                <!-- Welcome Text -->
                <div class="welcome-section">
                    <h2 class="welcome-title">Welcome !</h2>
                    <p class="welcome-subtitle">Please enter your username and password to sign in to your account</p>
                </div>

                <!-- Session Notifications / Alerts -->
                @if(view()->exists('admin.layouts.partials.alerts'))
                    @include('admin.layouts.partials.alerts')
                @else
                    @if(session('success'))
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-3 py-2 px-3 fs-7 mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info rounded-3 py-2 px-3 fs-7 mb-4">
                            <i class="fa-solid fa-circle-info me-2"></i> {{ session('info') }}
                        </div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning rounded-3 py-2 px-3 fs-7 mb-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('warning') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 py-2 px-3 fs-7 mb-4">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
                        </div>
                    @endif
                @endif

                <!-- Login Form -->
                <form action="{{ Route::has('login') ? route('login') : url('/login') }}" method="POST" id="erpLoginForm" class="needs-validation" novalidate>
                    @csrf

                    <!-- Email / Username Input -->
                    <div class="form-group-custom">
                        <label for="email" class="form-label-custom">Username or Email Address</label>
                        <div class="input-control-box">
                            <input 
                                type="text" 
                                name="email" 
                                id="email" 
                                class="input-field @error('email') is-invalid @enderror" 
                                placeholder="admin@gmail.com" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                autocomplete="username">
                            <i class="fa-solid fa-user lead-icon"></i>
                        </div>
                        @error('email')
                            <div class="form-error-msg">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="form-group-custom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label-custom mb-0">Password</label>
                        </div>
                        <div class="input-control-box">
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                class="input-field @error('password') is-invalid @enderror" 
                                placeholder="••••••••" 
                                value="{{ old('password') }}" 
                                required
                                autocomplete="current-password">
                            <i class="fa-solid fa-lock lead-icon"></i>
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Toggle password visibility" tabindex="-1">
                                <i class="fa-solid fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="form-error-msg">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me & Security Badge -->
                    <div class="form-meta-row">
                        <label class="custom-checkbox-wrap" for="remember">
                            <input type="checkbox" name="remember" id="remember" checked>
                            <span class="checkbox-label-text">Keep me logged in</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit-login" id="loginSubmitBtn">
                        <span id="btnText">Sign In to Dashboard</span>
                        <i class="fa-solid fa-arrow-right arrow-icon" id="btnArrow"></i>
                        <span id="btnSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Signing In...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Footer Copyright -->
            <div class="form-footer-copyright">
                &copy; 2026 TTC Robotronics Pvt. Ltd. All Rights Reserved.
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Interactive Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Password Show/Hide Toggle
            const togglePasswordBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');

            if (togglePasswordBtn && passwordInput && togglePasswordIcon) {
                togglePasswordBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        togglePasswordIcon.classList.remove('fa-eye');
                        togglePasswordIcon.classList.add('fa-eye-slash');
                        togglePasswordBtn.style.color = '#334155';
                    } else {
                        togglePasswordIcon.classList.remove('fa-eye-slash');
                        togglePasswordIcon.classList.add('fa-eye');
                        togglePasswordBtn.style.color = '';
                    }
                });
            }

            // Submit Loading State
            const erpLoginForm = document.getElementById('erpLoginForm');
            const loginSubmitBtn = document.getElementById('loginSubmitBtn');
            const btnText = document.getElementById('btnText');
            const btnArrow = document.getElementById('btnArrow');
            const btnSpinner = document.getElementById('btnSpinner');

            if (erpLoginForm && loginSubmitBtn) {
                erpLoginForm.addEventListener('submit', function (e) {
                    if (erpLoginForm.checkValidity()) {
                        btnText.classList.add('d-none');
                        btnArrow.classList.add('d-none');
                        btnSpinner.classList.remove('d-none');
                        loginSubmitBtn.disabled = true;
                        // Form submits naturally
                    }
                });
            }
        });
    </script>
</body>
</html>
