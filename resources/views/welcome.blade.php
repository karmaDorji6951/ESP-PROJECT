<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management System - ESP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .landing-container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
        }

        .header-section {
            text-align: center;
            color: white;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease;
        }

        .header-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-section p {
            font-size: 1.3rem;
            opacity: 0.95;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
            animation: fadeInUp 0.8s ease;
        }

        .role-card {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.25);
        }

        .role-card .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .admin-card .icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .supervisor-card .icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .staff-card .icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .role-card .icon {
            color: white;
        }

        .role-card h2 {
            font-size: 1.8rem;
            color: #2d3748;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .role-card p {
            color: #718096;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .features {
            text-align: left;
            margin-bottom: 30px;
        }

        .features li {
            list-style: none;
            padding: 8px 0;
            color: #4a5568;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .features li:before {
            content: "✓";
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #e6fffa;
            color: #0d9488;
            text-align: center;
            line-height: 25px;
            margin-right: 12px;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            padding: 12px 35px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn-login:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .admin-card:hover .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .supervisor-card:hover .btn-login {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .staff-card:hover .btn-login {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            opacity: 0.9;
            animation: fadeIn 1s ease;
        }

        .footer p {
            margin: 5px 0;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 2.5rem;
            }

            .header-section p {
                font-size: 1.1rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .role-card {
                padding: 30px 20px;
            }
<<<<<<< HEAD
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="header-section">
            <h1>🏢 Employee System Portal</h1>
            <p>Manage your team and track productivity efficiently</p>
        </div>

        <div class="cards-grid">
            <!-- Admin Card -->
            <div class="role-card admin-card">
                <div class="icon">👨‍💼</div>
                <h2>Admin Panel</h2>
                <p>Complete system control and management</p>
                <ul class="features">
                    <li>Manage Users & Roles</li>
                    <li>Create Employees</li>
                    <li>View All Reports</li>
                    <li>System Monitoring</li>
                </ul>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login">Admin Login</a>
                @endif
            </div>
=======

            .activity-list {
                list-style: none;
                margin: 0;
                padding: 0;
                display: grid;
                gap: 12px;
            }

            .activity-list li {
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                padding: 14px;
                display: grid;
                grid-template-columns: auto 1fr auto;
                gap: 12px;
                align-items: flex-start;
                background: #f9fafb;
                transition: all var(--transition-fast);
            }

            .activity-list li:hover {
                background: var(--card);
                border-color: var(--line-light);
                box-shadow: 0 4px 12px rgba(15, 30, 44, 0.08);
                transform: translateX(4px);
            }

            .dot {
                width: 11px;
                height: 11px;
                border-radius: 999px;
                flex-shrink: 0;
                margin-top: 3px;
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
            }

            .dot.ok {
                background: var(--success);
                box-shadow: 0 0 8px rgba(16, 185, 129, 0.4);
            }

            .dot.warn {
                background: var(--accent);
                box-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
            }

            .dot.alert {
                background: var(--alert);
                box-shadow: 0 0 8px rgba(225, 29, 72, 0.4);
            }

            .activity-list h4 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 700;
                color: var(--ink);
            }

            .activity-list p {
                margin: 4px 0 0;
                color: var(--muted);
                font-size: 0.82rem;
                line-height: 1.5;
            }

            .pill {
                font-size: 0.75rem;
                border-radius: 999px;
                padding: 6px 11px;
                font-weight: 700;
                background: #eef2f7;
                color: #475569;
                flex-shrink: 0;
                white-space: nowrap;
            }

            .calendar {
                display: grid;
                gap: 12px;
            }

            .event {
                border: 1px solid var(--line);
                border-radius: var(--radius-sm);
                padding: 13px;
                display: grid;
                gap: 6px;
                background: #f9fafb;
                transition: all var(--transition-fast);
                border-left: 4px solid transparent;
                position: relative;
            }

            .event::before {
                content: "";
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                border-radius: 4px 0 0 4px;
            }

            .event:hover {
                background: var(--card);
                box-shadow: 0 4px 12px rgba(15, 30, 44, 0.08);
                transform: translateX(4px);
            }

            .event strong {
                font-size: 0.92rem;
                font-weight: 700;
                color: var(--ink);
            }

            .event span {
                color: var(--muted);
                font-size: 0.8rem;
                font-weight: 500;
            }

            .event.green {
                border-left-color: var(--success);
            }

            .event.green::before {
                background: var(--success);
            }

            .event.gold {
                border-left-color: var(--accent);
            }

            .event.gold::before {
                background: var(--accent);
            }

            .event.red {
                border-left-color: var(--alert);
            }

            .event.red::before {
                background: var(--alert);
            }

            .footer {
                margin-top: 20px;
                border-top: 1px solid var(--line);
                padding-top: 16px;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
                gap: 12px;
                color: var(--muted-light);
                font-size: 0.8rem;
                font-weight: 500;
            }

            /* ====== ANIMATIONS ====== */
            @keyframes slideInLeft {
                from {
                    transform: translateX(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes rise-in {
                from {
                    transform: translateY(20px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes float-up {
                from {
                    transform: translateY(12px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-12px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes growWidth {
                from {
                    width: 0;
                    opacity: 0.5;
                }
                to {
                    width: 78%;
                    opacity: 1;
                }
            }

            /* ====== RESPONSIVE ====== */
            @media (max-width: 1200px) {
                .content {
                    padding: 24px 20px;
                }

                .panel {
                    padding: 20px;
                }

                .hero h2 {
                    font-size: clamp(1.3rem, 2.5vw, 2rem);
                }
            }

            @media (max-width: 1024px) {
                .hero {
                    grid-template-columns: 1fr;
                }

                .lower-grid {
                    grid-template-columns: 1fr;
                }

                .kpi-grid {
                    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
                }

                .topbar {
                    padding: 14px 20px;
                }
            }

            @media (max-width: 768px) {
                body {
                    grid-template-columns: 1fr;
                }

                .sidebar {
                    position: fixed;
                    left: 0;
                    top: 0;
                    bottom: 0;
                    width: 280px;
                    z-index: 999;
                    transform: translateX(-100%);
                    transition: transform var(--transition-normal);
                }

                .sidebar.open {
                    transform: translateX(0);
                }

                .sidebar-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    z-index: 998;
                }

                .sidebar.open ~ .sidebar-overlay {
                    display: block;
                }

                .menu-toggle {
                    display: flex;
                }

                .search-box {
                    display: none;
                }

                .topbar {
                    padding: 12px 16px;
                }

                .content {
                    padding: 20px 16px;
                }

                .panel {
                    padding: 16px;
                }

                .hero h2 {
                    font-size: clamp(1.2rem, 4vw, 1.8rem);
                }
            }

            @media (max-width: 640px) {
                .hero {
                    gap: 16px;
                }

                .kpi-grid {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .quick-actions {
                    flex-direction: column;
                }

                .quick-actions .btn {
                    width: 100%;
                }

                .content {
                    padding: 16px 14px;
                }

                .hero p {
                    font-size: 0.95rem;
                }

                .section-title {
                    font-size: 0.95rem;
                }

                .activity-list li {
                    padding: 12px;
                    gap: 10px;
                    grid-template-columns: auto 1fr;
                }

                .activity-list h4 {
                    font-size: 0.85rem;
                }

                .activity-list p {
                    font-size: 0.75rem;
                }

                .pill {
                    font-size: 0.7rem;
                    padding: 4px 8px;
                }

                .topbar-left {
                    gap: 10px;
                }

                .breadcrumb {
                    display: none;
                }
            }

            @media (max-width: 480px) {
                .topbar {
                    flex-direction: column;
                    gap: 12px;
                    padding: 12px;
                }

                .topbar-left {
                    width: 100%;
                }

                .topbar-right {
                    width: 100%;
                    justify-content: flex-end;
                }

                .hero h2 {
                    font-size: 1.1rem;
                }

                .content {
                    padding: 12px 10px;
                }

                .kpi strong {
                    font-size: 1.4rem;
                }

                .footer {
                    flex-direction: column;
                    gap: 8px;
                    font-size: 0.75rem;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                * {
                    animation-duration: 1ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 1ms !important;
                }
            }

            /* ====== NOTIFICATIONS ====== */
            .notifications-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-width: 420px;
                pointer-events: none;
            }

            .notification {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 16px;
                box-shadow: var(--shadow-lg);
                display: flex;
                align-items: flex-start;
                gap: 12px;
                animation: slideInRight 300ms var(--transition-normal) forwards;
                pointer-events: auto;
                cursor: pointer;
                transition: all var(--transition-normal);
                position: relative;
                overflow: hidden;
            }

            .notification::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 4px;
                background: var(--brand);
            }

            .notification.success {
                border-color: var(--success-soft);
                background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            }

            .notification.success::before {
                background: var(--success);
            }

            .notification.info {
                border-color: var(--brand-soft);
                background: linear-gradient(135deg, #ffffff 0%, #f0fdfc 100%);
            }

            .notification.info::before {
                background: var(--brand);
            }

            .notification.warning {
                border-color: var(--accent-soft);
                background: linear-gradient(135deg, #ffffff 0%, #fefce8 100%);
            }

            .notification.warning::before {
                background: var(--accent);
            }

            .notification.error {
                border-color: var(--alert-soft);
                background: linear-gradient(135deg, #ffffff 0%, #ffe4e6 100%);
            }

            .notification.error::before {
                background: var(--alert);
            }

            .notification-icon {
                font-size: 20px;
                flex-shrink: 0;
                margin-top: 2px;
            }

            .notification-content {
                flex: 1;
                overflow: hidden;
            }

            .notification-title {
                font-weight: 700;
                font-size: 0.95rem;
                color: var(--ink);
                margin: 0 0 4px;
                line-height: 1.2;
            }

            .notification-message {
                font-size: 0.85rem;
                color: var(--muted);
                margin: 0;
                line-height: 1.4;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .notification-close {
                background: none;
                border: none;
                cursor: pointer;
                color: var(--muted-light);
                font-size: 18px;
                padding: 0;
                flex-shrink: 0;
                transition: color var(--transition-fast);
                line-height: 1;
                margin-top: 2px;
            }

            .notification-close:hover {
                color: var(--ink);
            }

            .notification:hover {
                box-shadow: var(--shadow);
                border-color: var(--line-light);
                transform: translateY(-2px);
            }

            .notification.fade-out {
                animation: slideOutRight 300ms var(--transition-normal) forwards;
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(100%);
                    pointer-events: none;
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            @keyframes slideOutRight {
                from {
                    opacity: 1;
                    transform: translateX(0);
                }
                to {
                    opacity: 0;
                    transform: translateX(100%);
                    pointer-events: none;
                }
            }

            @media (max-width: 768px) {
                .notifications-container {
                    max-width: 100%;
                    width: calc(100% - 20px);
                    top: 10px;
                    right: 10px;
                    left: 10px;
                }

                .notification {
                    padding: 14px;
                    gap: 10px;
                }

                .notification-icon {
                    font-size: 18px;
                }

                .notification-title {
                    font-size: 0.9rem;
                }

                .notification-message {
                    font-size: 0.8rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- SIDEBAR -->
        <nav class="sidebar" id="sidebar">
            <a href="{{ url('/') }}" class="sidebar-header">
                <span class="sidebar-mark">ESP</span>
                <h2 class="sidebar-title">Employee Success</h2>
            </a>

            <div class="sidebar-nav">
                <a href="#" class="nav-item active">
                    <span class="nav-icon">📊</span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-label">Employees</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span class="nav-label">Attendance</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">✓</span>
                    <span class="nav-label">Tasks</span>
                    <span class="nav-badge">12</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📅</span>
                    <span class="nav-label">Leave Requests</span>
                    <span class="nav-badge">3</span>
                </a>

                <div class="sidebar-divider"></div>

                <a href="#" class="nav-item">
                    <span class="nav-icon">📈</span>
                    <span class="nav-label">Reports</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-label">Settings</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">❓</span>
                    <span class="nav-label">Help</span>
                </a>
            </div>

            <div class="sidebar-footer">
                @auth
                    <a href="{{ url('/home') }}" class="user-info">
                        <span class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        <div class="user-details">
                            <p class="user-name">{{ Auth::user()->name }}</p>
                            <p class="user-role">Supervisor</p>
                        </div>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Sign In
                    </a>
                @endauth
            </div>
        </nav>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- NOTIFICATIONS CONTAINER -->
        <div id="notificationsContainer" class="notifications-container"></div>

        <!-- MAIN CONTENT -->
        <div class="main-container">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar">☰</button>
                    <nav class="breadcrumb">
                        <span>Welcome back,</span>
                        @auth
                            <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>
                        @endauth
                    </nav>
                </div>

                <div class="topbar-right">
                    <div class="search-box">
                        <span style="font-size: 16px;">🔍</span>
                        <input type="text" placeholder="Search employees, tasks...">
                    </div>
                    <button class="icon-btn" title="Notifications">🔔</button>
                    <button class="icon-btn" title="More options">⋯</button>
                </div>
            </header>

            <main class="content">
                <div class="hero">
                    <article class="panel hero-main">
                        <span class="eyebrow">Live workspace update</span>
                        <h2>Run your team with clarity, speed, and confidence.</h2>
                        <p>
                            Track attendance, employee focus, and pending tasks in one place. This layout keeps the most important information visible first, so managers can act faster and teams can stay aligned.
                        </p>

                        <div class="quick-actions">
                            <a href="{{ url('/home') }}" class="btn btn-primary">View Attendance</a>
                            <a href="{{ url('/home') }}" class="btn btn-secondary">Open Task Board</a>
                            <a href="{{ url('/home') }}" class="btn btn-secondary">Review Leave Requests</a>
                        </div>

                        <div class="kpi-grid">
                            <div class="kpi">
                                <span>Active Employees</span>
                                <strong>128</strong>
                                <small>+9 this month</small>
                            </div>
                            <div class="kpi">
                                <span>On-Time Check-ins</span>
                                <strong>94.7%</strong>
                                <small>+2.1% weekly</small>
                            </div>
                            <div class="kpi">
                                <span>Open Tasks</span>
                                <strong>36</strong>
                                <small>12 high priority</small>
                            </div>
                        </div>
                    </article>

                    <aside class="panel focus-card">
                        <h3>Weekly Delivery Health</h3>
                        <div>
                            <p style="margin: 0 0 8px; color: var(--muted); font-size: 0.84rem;">Current sprint completion</p>
                            <div class="progress-track" role="img" aria-label="Sprint progress 78 percent">
                                <i></i>
                            </div>
                            <p style="margin: 8px 0 0; font-weight: 800;">78% complete</p>
                        </div>

                        <ul class="focus-list">
                            <li><b>Payroll Audit</b> <em>Due in 2 days</em></li>
                            <li><b>Policy Acknowledgements</b> <em>17 pending</em></li>
                            <li><b>Leave Conflict Review</b> <em>3 blocked</em></li>
                        </ul>
                    </aside>
                </div>

                <div class="lower-grid">
                    <section class="panel">
                        <h3 class="section-title">Recent Operations Activity</h3>
                        <ul class="activity-list">
                            <li>
                                <span class="dot ok"></span>
                                <div>
                                    <h4>Attendance imported for all departments</h4>
                                    <p>Data synchronized from biometric gateway.</p>
                                </div>
                                <span class="pill">Done</span>
                            </li>
                            <li>
                                <span class="dot warn"></span>
                                <div>
                                    <h4>Design review meeting moved to 3:00 PM</h4>
                                    <p>Calendar updated for project members.</p>
                                </div>
                                <span class="pill">Updated</span>
                            </li>
                            <li>
                                <span class="dot alert"></span>
                                <div>
                                    <h4>2 leave requests need manager approval</h4>
                                    <p>Pending longer than 24 hours.</p>
                                </div>
                                <span class="pill">Action</span>
                            </li>
                        </ul>
                    </section>

                    <section class="panel">
                        <h3 class="section-title">Today Schedule</h3>
                        <div class="calendar">
                            <article class="event green">
                                <strong>09:30 AM - Team Standup</strong>
                                <span>Product, Engineering, and QA</span>
                            </article>
                            <article class="event gold">
                                <strong>01:00 PM - Hiring Panel</strong>
                                <span>Interview loop for Backend Engineer</span>
                            </article>
                            <article class="event red">
                                <strong>04:00 PM - Compliance Checkpoint</strong>
                                <span>Finalize monthly employee records</span>
                            </article>
                        </div>
                    </section>
                </div>

                <footer class="footer">
                    <span>Employee Success Portal</span>
                    <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}</span>
                </footer>
            </main>
        </div>

        <script>
            // ====== SIDEBAR TOGGLE ======
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const overlay = document.getElementById('sidebarOverlay');
>>>>>>> dc6a725898e2646dcf5320af921e423695e9a643

            <!-- Supervisor Card -->
            <div class="role-card supervisor-card">
                <div class="icon">👨‍⚔️</div>
                <h2>Supervisor</h2>
                <p>Manage team and operations</p>
                <ul class="features">
                    <li>Manage Staff</li>
                    <li>Assign Tasks</li>
                    <li>Track Attendance</li>
                    <li>Approve Leaves</li>
                </ul>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login">Supervisor Login</a>
                @endif
            </div>

            <!-- Staff Card -->
            <div class="role-card staff-card">
                <div class="icon">👨‍💻</div>
                <h2>Staff</h2>
                <p>View your tasks and requests</p>
                <ul class="features">
                    <li>Track My Tasks</li>
                    <li>View Attendance</li>
                    <li>Request Leave</li>
                    <li>View Dashboard</li>
                </ul>
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-login">Staff Login</a>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><strong>Employee System Portal</strong></p>
            <p>Streamline Your Team Management Process</p>
        </div>
    </div>

<<<<<<< HEAD
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
=======
            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                }
            });

            // ====== NOTIFICATION SYSTEM ======
            class NotificationManager {
                constructor() {
                    this.container = document.getElementById('notificationsContainer');
                    this.notifications = [];
                    this.initializeEcho();
                }

                initializeEcho() {
                    // Check if Laravel Echo is available
                    if (typeof window.Echo === 'undefined') {
                        console.warn('Laravel Echo not initialized. Install it with: npm install laravel-echo');
                        return;
                    }

                    // Alternative: Use database polling for development
                    // Listen for task assignments
                    this.listenForTaskAssignments();
                    // Listen for task submissions
                    this.listenForTaskSubmissions();
                }

                listenForTaskAssignments() {
                    // Listen on private channel for current logged-in user
                    @auth
                        const userId = {{ Auth::user()->id }};
                        if (window.Echo) {
                            try {
                                window.Echo.private(`user.${userId}`)
                                    .listen('TaskAssigned', (data) => {
                                        this.showNotification(
                                            'New Task Assigned! ✓',
                                            data.message || `Task: ${data.title}`,
                                            'success',
                                            '📋'
                                        );
                                    });
                            } catch (e) {
                                console.log('Echo listener setup:', e);
                            }
                        }
                    @endauth
                }

                listenForTaskSubmissions() {
                    // Listen on private channel for supervisor/manager
                    @auth
                        const userId = {{ Auth::user()->id }};
                        if (window.Echo) {
                            try {
                                window.Echo.private(`user.${userId}`)
                                    .listen('TaskSubmitted', (data) => {
                                        this.showNotification(
                                            'Work Submission Received! 📝',
                                            data.message || `${data.submitted_by} submitted work`,
                                            'info',
                                            '📊'
                                        );
                                    });
                            } catch (e) {
                                console.log('Echo listener setup:', e);
                            }
                        }
                    @endauth
                }

                showNotification(title, message, type = 'info', icon = 'ℹ️') {
                    const id = Date.now();
                    const notification = document.createElement('div');
                    notification.className = `notification ${type}`;
                    notification.id = `notification-${id}`;
                    notification.innerHTML = `
                        <div class="notification-icon">${icon}</div>
                        <div class="notification-content">
                            <p class="notification-title">${title}</p>
                            <p class="notification-message">${message}</p>
                        </div>
                        <button class="notification-close" aria-label="Close notification">&times;</button>
                    `;

                    this.container.appendChild(notification);
                    this.notifications.push(id);

                    // Close button listener
                    notification.querySelector('.notification-close').addEventListener('click', () => {
                        this.removeNotification(id);
                    });

                    // Auto-close after 8 seconds
                    let timeout = setTimeout(() => {
                        this.removeNotification(id);
                    }, 8000);

                    notification.addEventListener('mouseenter', () => {
                        clearTimeout(timeout);
                    });

                    notification.addEventListener('mouseleave', () => {
                        timeout = setTimeout(() => {
                            this.removeNotification(id);
                        }, 3000);
                    });
                }

                removeNotification(id) {
                    const element = document.getElementById(`notification-${id}`);
                    if (element) {
                        element.classList.add('fade-out');
                        setTimeout(() => {
                            if (element.parentNode) {
                                element.remove();
                            }
                            this.notifications = this.notifications.filter(n => n !== id);
                        }, 300);
                    }
                }
            }

            // Initialize notification manager when DOM is ready
            document.addEventListener('DOMContentLoaded', () => {
                window.notificationManager = new NotificationManager();

                // Example: Show a welcome notification
                @auth
                    setTimeout(() => {
                        window.notificationManager.showNotification(
                            'Welcome back! 👋',
                            'Real-time notifications will appear here when tasks are assigned or submitted.',
                            'success',
                            '🎉'
                        );
                    }, 500);
                @endauth
            });
        </script>
    </body>
>>>>>>> dc6a725898e2646dcf5320af921e423695e9a643
</html>
