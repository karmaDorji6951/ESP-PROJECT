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
            --supervisor-accent: #2563eb;
            --supervisor-light: #dbeafe;
            --supervisor-dark: #1e40af;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --border-color: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
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

        /* Sidebar Styling */
        .supervisor-sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--supervisor-accent) 0%, var(--supervisor-dark) 100%);
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
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: 600;
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
            background-color: var(--danger);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #dc2626;
        }

        .supervisor-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* Mobile Responsive */
        @media (max-width: 1200px) {
            .supervisor-sidebar {
                width: 240px;
            }

            .supervisor-main {
                margin-left: 240px;
            }

            .supervisor-content {
                padding: 16px;
            }
        }

        @media (max-width: 768px) {
            .supervisor-sidebar {
                width: 280px;
                position: fixed;
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }

            .supervisor-sidebar.active {
                transform: translateX(0);
            }

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
    </style>
    @stack('styles')
</head>
<body>
    <div class="supervisor-container">
        <!-- Sidebar -->
        <div class="supervisor-sidebar" id="supervisorSidebar">
            <div class="sidebar-header">
                <div class="logo-section">
                    <div class="logo">📋</div>
                    <span class="app-name">ESP Manager</span>
                </div>
            </div>

            <nav class="sidebar-menu supervisor-menu">
                <a href="{{ route('dashboard') }}" class="menu-item @if(Route::currentRouteName() === 'dashboard') active @endif">
                    <span class="menu-icon">📊</span>
                    <span class="menu-label">Dashboard</span>
                </a>
                <a href="{{ route('supervisor.tasks.index') }}" class="menu-item @if(strpos(Route::currentRouteName(), 'supervisor.tasks') === 0) active @endif">
                    <span class="menu-icon">✓</span>
                    <span class="menu-label">My Tasks</span>
                </a>
                <a href="#attendance" class="menu-item">
                    <span class="menu-icon">📅</span>
                    <span class="menu-label">Attendance</span>
                </a>
                <a href="#leaves" class="menu-item">
                    <span class="menu-icon">🏖️</span>
                    <span class="menu-label">Leave Reviews</span>
                </a>
                <a href="#staff" class="menu-item">
                    <span class="menu-icon">👥</span>
                    <span class="menu-label">Staff Directory</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('supervisorSidebar');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });

                document.querySelectorAll('.menu-item').forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('active');
                        }
                    });
                });

                document.addEventListener('click', function(event) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('active');
                        }
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
