<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('page_title', 'Supervisor') - ESP</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=manrope:500,600,700,800|space-grotesk:500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root {
            /* Primary Brand Colors - Modern Vibrant Teal */
            --supervisor-accent: #06b6d4;
            --supervisor-light: #cffafe;
            --supervisor-dark: #0369a1;
            
            /* Secondary Colors - Complementary Purple & Orange */
            --secondary-purple: #a855f7;
            --secondary-purple-light: #e9d5ff;
            --secondary-orange: #f97316;
            --secondary-orange-light: #fed7aa;
            
            /* Text Colors */
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #78716c;
            
            /* Background Colors - More Colorful */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-accent: #e0f2fe;
            
            /* Border Color */
            --border-color: #cbd5e1;
            
            /* Status Colors - Vibrant */
            --success: #16a34a;
            --success-light: #dcfce7;
            --warning: #ea580c;
            --warning-light: #ffedd5;
            --danger: #dc2626;
            --danger-light: #fee2e2;
            --info: #0369a1;
            --info-light: #e0f2fe;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-primary);
        }

        .supervisor-container {
            display: flex;
            min-height: 100vh;
            background-color: var(--bg-secondary);
        }

        /* Sidebar Styling - Vibrant Gradient */
        .supervisor-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #06b6d4 0%, #0369a1 50%, #0f766e 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            font-size: 28px;
        }

        .app-name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
            margin-bottom: 30px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            margin-bottom: 8px;
            position: relative;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.15) 100%);
            color: #ffffff;
            font-weight: 600;
            border-left: 4px solid rgba(255, 255, 255, 0.8);
            padding-left: 12px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .menu-icon {
            font-size: 18px;
            min-width: 20px;
        }

        .menu-label {
            flex: 1;
        }

        .badge-count {
            background-color: var(--danger);
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
        }

        .user-role {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Main Content */
        .supervisor-main {
            margin-left: 280px;
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .supervisor-topbar {
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            flex: 1;
            margin-left: 16px;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .notification-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .notification-btn:hover {
            background-color: var(--bg-secondary);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger) 0%, #b91c1c 100%);
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .logout-btn:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }

        .supervisor-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* Mobile Responsive */
        @media (max-width: 1200px) {
            .supervisor-content {
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .supervisor-main {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .supervisor-content {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .topbar-title {
                font-size: 16px;
            }
        }

        /* Colorful Card Styles */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: var(--bg-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--supervisor-light) 0%, var(--bg-accent) 100%);
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0;
        }

        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, var(--supervisor-accent) 0%, #0e919e 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0e919e 0%, #0369a1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-purple) 0%, #9333ea 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(168, 85, 247, 0.3);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #9333ea 0%, #7e22ce 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #15803d 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #b91c1c 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
        }

        /* Alert Styles */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .alert-success {
            background-color: var(--success-light);
            border-color: var(--success);
            color: #15803d;
        }

        .alert-warning {
            background-color: var(--warning-light);
            border-color: var(--warning);
            color: #b45309;
        }

        .alert-danger {
            background-color: var(--danger-light);
            border-color: var(--danger);
            color: #b91c1c;
        }

        .alert-info {
            background-color: var(--info-light);
            border-color: var(--info);
            color: #0369a1;
        }

        /* Badge Styles */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }

        .badge-primary {
            background: var(--supervisor-light);
            color: var(--supervisor-dark);
        }

        .badge-success {
            background: var(--success-light);
            color: #15803d;
        }

        .badge-warning {
            background: var(--warning-light);
            color: #b45309;
        }

        .badge-danger {
            background: var(--danger-light);
            color: #b91c1c;
        }

        .badge-purple {
            background: var(--secondary-purple-light);
            color: var(--secondary-purple);
        }

        .badge-orange {
            background: var(--secondary-orange-light);
            color: var(--secondary-orange);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="supervisor-container">
        
        <!-- Sidebar Navigation -->
        <div class="supervisor-sidebar">
            <div class="sidebar-header">
                <div class="logo-section">
                    <span class="logo">📋</span>
                    <span class="app-name">ESP</span>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('supervisor.dashboard') }}" class="menu-item {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                        <span class="menu-icon">📊</span>
                        <span class="menu-label">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('supervisor.tasks.index') }}" class="menu-item {{ request()->routeIs('supervisor.tasks.*') ? 'active' : '' }}">
                        <span class="menu-icon">📝</span>
                        <span class="menu-label">Tasks</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('timetables.index') }}" class="menu-item {{ request()->routeIs('timetables.*') ? 'active' : '' }}">
                        <span class="menu-icon">📅</span>
                        <span class="menu-label">Timetables</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('evaluations.index') }}" class="menu-item {{ request()->routeIs('evaluations.index') ? 'active' : '' }}">
                        <span class="menu-icon">📋</span>
                        <span class="menu-label">Reviewed</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('evaluations.create') }}" class="menu-item {{ request()->routeIs('evaluations.create') ? 'active' : '' }}">
                        <span class="menu-icon">✍️</span>
                        <span class="menu-label">Evaluations</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('profile.show') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <span class="menu-icon">👤</span>
                        <span class="menu-label">Profile</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Supervisor</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="supervisor-main">
            <div class="supervisor-topbar">
                <button class="sidebar-toggle" id="sidebarToggle">☰</button>
                <div class="topbar-title">@yield('topbar_title', 'Dashboard')</div>
                <div class="topbar-actions">
                    <button class="notification-btn">📢</button>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>

            <div class="supervisor-content">
                @yield('content')
            </div>
        </div>
    </div>

        @stack('scripts')
</body>
</html>
