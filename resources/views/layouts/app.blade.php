<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PMS for ESP')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary: #0F2044; /* deep navy */
            --primary-dark: #0b1730;
            --primary-light: #1D9E75; /* teal accent used for active states */
            --secondary: #64748b;
            --accent: #ffffff;
            --warning: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #0f172a;
            --light: #f1f5f9;
            --border: #e2e8f0;
            --shadow: rgba(0, 0, 0, 0.1);
            --gradient-dashboard: linear-gradient(135deg, #0F2044 0%, #173b6b 60%, #1D9E75 160%);
            --gradient-primary: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            --gradient-secondary: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);

            /* Alias tokens used by feature views (profile, timetable, etc.) */
            --bg-primary: #ffffff;
            --bg-secondary: var(--light);
            --border-color: var(--border);
            --text-primary: var(--dark);
            --text-secondary: var(--secondary);
            --text-muted: var(--secondary);
            --supervisor-accent: var(--primary);
            --supervisor-dark: var(--primary-dark);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--gradient-secondary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--dark);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container-fluid {
            max-width: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .row {
            margin: 0;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: var(--gradient-dashboard);
            padding: 24px 20px !important;
            box-shadow: 0 4px 24px var(--shadow);
            overflow-y: auto;
            z-index: 1000;
            color: #ffffff;
        }

        /* Mobile off-canvas behavior */
        .sidebar.mobile-hidden {
            transform: translateX(-110%);
            transition: transform 0.28s ease;
            will-change: transform;
        }

        .sidebar.mobile-open {
            transform: translateX(0) !important;
            transition: transform 0.28s ease;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 900;
            transition: opacity 0.2s ease;
        }

        .sidebar-backdrop.show {
            display: block;
        }

        .sidebar .brand {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar h4 {
            color: white;
            font-weight: 700;
            font-size: 20px;
            margin-bottom: 4px !important;
            letter-spacing: -0.5px;
        }

        .sidebar small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            font-weight: 500;
        }

        .sidebar nav {
            margin-top: 24px;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-weight: 500;
            position: relative;
            margin-bottom: 8px;
            font-size: 14px;
            border: 1px solid transparent;
            min-width: 0;
        }

        .sidebar a span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, 0.06);
            color: white;
            border-left: 4px solid var(--primary-light);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .sidebar a i {
            margin-right: 12px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        header {
            background: white;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 3px var(--shadow);
            padding: 16px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            width: 100%;
        }

        /* Mobile header toggle button spacing */
        .mobile-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-size: 18px;
        }

        header h5 {
            color: var(--dark) !important;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 0;
            font-size: 18px;
            text-shadow: none;
            opacity: 1;
        }

        header small {
            color: var(--secondary) !important;
            font-size: 13px;
            font-weight: 500;
            opacity: 1;
        }

        .app-header-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 0 24px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .app-header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1 1 auto;
            overflow: hidden;
        }

        .app-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
            gap: 8px;
            white-space: nowrap;
        }

        header h5, header small { overflow: hidden; text-overflow: ellipsis; }

        .notification-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary-light);
            display: inline-block;
            box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.12);
            position: absolute;
            top: 6px;
            right: 6px;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 24px;
            right: 24px;
        }

        /* Ensure sidebar content doesn't get hidden behind the footer */
        .sidebar { padding-bottom: 140px; }

        .profile-card-link {
            display: block;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
            color: inherit;
            text-decoration: none;
            transition: transform 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .profile-card-link:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(29, 158, 117, 0.65);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
            transform: translateY(-1px);
        }

        .profile-card-link:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.25), 0 8px 20px rgba(0, 0, 0, 0.18);
        }

        .profile-card-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-card-copy {
            flex: 1;
            min-width: 0;
            color: white;
        }

        .profile-card-name {
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-card-meta {
            font-size: 12px;
            opacity: 0.9;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-card-arrow {
            color: rgba(255, 255, 255, 0.8);
            font-size: 18px;
            flex-shrink: 0;
        }

        /* Modern Notification Styles */
        .notification-item.unread {
            background-color: var(--light);
            border-left: 3px solid var(--primary-light);
        }
        
        .notification-item.unread .fw-semibold {
            color: var(--dark) !important;
        }
        
        .notification-item.read .fw-semibold {
            color: var(--secondary) !important;
        }
        
        .notification-item:hover {
            background-color: var(--border);
        }

        /* Modern Form Styles */
        .form-control, .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 6px;
            font-size: 14px;
        }

        /* Modern Badge Styles */
        .badge {
            border-radius: 6px;
            font-weight: 500;
            padding: 4px 8px;
            font-size: 12px;
        }

        .badge.bg-primary {
            background: var(--primary) !important;
        }

        .badge.bg-success {
            background: var(--success) !important;
        }

        .badge.bg-danger {
            background: var(--danger) !important;
        }

        .badge.bg-warning {
            background: var(--warning) !important;
            color: white;
        }

        .badge.bg-info {
            background: var(--info) !important;
        }

        /* Modern Table Styles */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--accent);
            border-bottom: 2px solid var(--border);
            font-weight: 600;
            color: var(--dark);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }

        .table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--light);
        }

        /* Modern Alert Styles */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 12px 16px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border-left: 4px solid var(--warning);
        }

        .alert-info {
            background: rgba(6, 182, 212, 0.1);
            color: var(--info);
            border-left: 4px solid var(--info);
        }

        main {
            background: var(--accent);
            min-height: calc(100vh - 48px);
            margin-left: 280px;
            padding-left: 0;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        /* Further reduce space between sidebar and content */
        main > .p-4 {
            padding: 32px 4px !important;
            padding-top: 100px !important;
            overflow-x: hidden;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        /* Uniform in-page header styling (used by Schedule / My Tasks / Leave, etc.) */
        .app-page-hero {
            background: var(--gradient-dashboard);
            color: #ffffff;
            border-radius: 18px;
            padding: 22px 26px;
            box-shadow: 0 18px 40px var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .app-page-hero::after {
            content: '';
            position: absolute;
            inset: auto -120px -120px auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
            z-index: 0;
        }

        .app-page-hero > * {
            position: relative;
            z-index: 1;
        }

        .app-page-hero-kicker {
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            opacity: 0.85;
        }

        .app-page-hero-title {
            font-weight: 800;
            font-size: clamp(1.6rem, 2.5vw, 2.35rem);
            margin: 0;
        }

        .app-page-hero-subtitle {
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            max-width: 760px;
        }

        .app-page-hero-action {
            border-radius: 999px;
            padding-inline: 18px;
            font-weight: 600;
            color: var(--primary);
            position: relative;
            z-index: 1;
        }

        .app-page-hero-action:hover {
            color: var(--primary);
        }

        /* Center cards in main content */
        .row {
            justify-content: center;
        }

        /* Modern Card Styles */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px var(--shadow);
            transition: all 0.2s ease;
            background: white;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 4px 12px var(--shadow);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--accent);
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
            font-weight: 600;
            color: var(--dark);
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .card-soft {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px var(--shadow);
            transition: all 0.2s ease;
        }

        .card-soft:hover {
            box-shadow: 0 4px 12px var(--shadow);
            transform: translateY(-1px);
        }

        /* Modern Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--gradient-primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .btn-success {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .btn-outline-danger {
            color: var(--danger);
            border-color: var(--danger);
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px var(--shadow);
        }

        header .btn-outline-danger {
            border-color: #7a7a7a;
            color: #2c3e50;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 0.9rem;
        }

        header .btn-outline-danger:hover {
            background: linear-gradient(135deg, #d4c4a8 0%, #c9b8a0 100%);
            border-color: #d4c4a8;
            color: #2c3e50;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 196, 168, 0.3);
        }

        .card-soft {
            border: 1px solid #d4c4a8 !important;
            box-shadow: 0 4px 16px rgba(122, 159, 181, 0.1);
            border-radius: 12px !important;
            background: #ffffff;
            transition: all 0.3s ease;
            margin-bottom: 12px;
        }

        .card-soft:hover {
            box-shadow: 0 8px 24px rgba(122, 159, 181, 0.15);
            transform: translateY(-4px);
        }

        .card-soft .card-header {
            background: linear-gradient(135deg, #f5f1e8 0%, #ede6d9 100%) !important;
            border-bottom: 1px solid #d4c4a8 !important;
            padding: 10px 14px !important;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .card-soft .card-body {
            padding: 12px 14px !important;
        }

        .card-soft .card-title {
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .card-soft .card-body .fs-2 {
            color: #2c3e50 !important;
        }

        .card-soft .card-body .text-muted {
            color: #7a6a5a !important;
        }

        .card-soft .card-body .bi {
            color: #7a9fb5 !important;
        }

        .table {
            margin-bottom: 0;
            table-layout: auto;
        }

        .table thead th {
            background: linear-gradient(135deg, #e8dcc8 0%, #f5f1e8 100%) !important;
            color: #2c3e50;
            font-weight: 700;
            border-bottom: 2px solid #d4c4a8 !important;
            padding: 14px 16px !important;
            vertical-align: middle;
        }

        .table tbody td {
            padding: 14px 16px !important;
            vertical-align: middle;
            color: #2c3e50;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e8dcc8;
        }

        .table tbody tr:hover {
            background-color: #f5f1e8 !important;
            box-shadow: inset 0 0 8px rgba(122, 159, 181, 0.1);
        }

        .table-responsive {
            padding: 0;
        }

        .alert {
            border-radius: 8px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
            border: none;
            color: #f5f1e8;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3d5568 0%, #1a252f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.4);
            color: #f5f1e8;
        }

        .btn-success {
            background: linear-gradient(135deg, #5a8a7a 0%, #4a7a6a 100%);
            border: none;
            color: #f5f1e8;
            box-shadow: 0 2px 8px rgba(90, 138, 122, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #4a7a6a 0%, #3a6a5a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(90, 138, 122, 0.4);
            color: #f5f1e8;
        }

        .btn-warning {
            background: linear-gradient(135deg, #d4c4a8 0%, #c9b8a0 100%);
            border: none;
            color: #2c3e50;
            box-shadow: 0 2px 8px rgba(212, 196, 168, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #c9b8a0 0%, #b8a890 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 196, 168, 0.4);
            color: #2c3e50;
        }

        .btn-outline-primary {
            border-color: #2c3e50;
            color: #2c3e50;
        }

        .btn-outline-primary:hover {
            background: #2c3e50;
            border-color: #2c3e50;
            color: #f5f1e8;
        }

        .btn-outline-secondary {
            border-color: #7a9fb5;
            color: #7a9fb5;
        }

        .btn-outline-secondary:hover {
            background: #7a9fb5;
            border-color: #7a9fb5;
            color: #f5f1e8;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-primary {
            background: linear-gradient(135deg, #e8dcc8 0%, #f5f1e8 100%);
            color: #2c3e50;
        }

        .badge-success {
            background: linear-gradient(135deg, #d9e8e1 0%, #eef7f3 100%);
            color: #3a6a5a;
        }

        .badge-warning {
            background: linear-gradient(135deg, #ede6d9 0%, #f5f1e8 100%);
            color: #7a6a5a;
        }

        .badge-danger {
            background: linear-gradient(135deg, #e8d9d9 0%, #f5ede8 100%);
            color: #7a4a4a;
        }

        .badge-info {
            background: linear-gradient(135deg, #d9e8f0 0%, #ede8f5 100%);
            color: #3d5568;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            display: block;
        }

        .form-control,
        .form-select {
            border: 1px solid #d4c4a8;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #2c3e50;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #7a9fb5;
            box-shadow: 0 0 0 3px rgba(122, 159, 181, 0.1);
            outline: none;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-check {
            padding-left: 28px;
            margin-bottom: 12px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-left: -28px;
            margin-top: 3px;
            border: 1px solid #d4c4a8;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #7a9fb5;
            border-color: #7a9fb5;
        }

        .form-check-label {
            margin-bottom: 0;
            color: #2c3e50;
            cursor: pointer;
        }

        /* Spacing utilities */
        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
        }

        /* Alert styling */
        .alert {
            border-radius: 8px;
            border-left: 4px solid;
            font-weight: 500;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d9e8e1 0%, #ede6d9 100%);
            border-color: #5a8a7a;
            color: #2c3e50;
        }

        .alert-danger {
            background: linear-gradient(135deg, #e8d9d9 0%, #ede6d9 100%);
            border-color: #a85a5a;
            color: #5a3a3a;
        }

        .alert-warning {
            background: linear-gradient(135deg, #ede6d9 0%, #f5f1e8 100%);
            border-color: #d4c4a8;
            color: #7a6a5a;
        }

        .alert-info {
            background: linear-gradient(135deg, #d9e8f0 0%, #ede8f5 100%);
            border-color: #7a9fb5;
            color: #2c3e50;
        }

        /* Heading spacing */
        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 16px;
            color: #2c3e50;
        }

        /* Button spacing */
        .btn {
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-group {
            gap: 8px;
            display: flex;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }

        .btn-lg {
            padding: 12px 24px;
            font-size: 1rem;
        }

        /* Badge spacing */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            margin-right: 4px;
        }

        /* Global box-sizing and responsive helpers */
        * { box-sizing: border-box; }
        img, iframe, embed, video { max-width: 100%; height: auto; }

        /* Ensure large tables don't force page width */
        .table-responsive { max-width: 100%; overflow-x: auto; }

        /* Prevent body horizontal scroll */
        html, body { overflow-x: hidden; }

        /* Responsive Design */
        @media (max-width: 1200px) {
            main > .p-4 {
                padding: 24px !important;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                width: 280px;
                height: 100vh;
                top: 0;
                left: 0;
                padding: 16px !important;
            }
            
            .sidebar .brand {
                margin-bottom: 20px;
                padding-bottom: 16px;
            }
            
            main {
                margin-left: 0;
                min-height: auto;
                width: 100%;
            }
            
            main > .p-4 {
                padding: 20px 4px !important;
            }
            
            header {
                position: fixed !important;
                padding: 12px 16px !important;
            }

            .app-header-inner {
                padding: 0 16px;
            }

            /* show collapsed sidebar by default on small screens */
            .sidebar.mobile-hidden {
                transform: translateX(-110%);
            }
            /* Make sidebar footer flow with content on small screens to avoid overlap */
            .sidebar-footer {
                position: relative !important;
                bottom: auto !important;
                left: auto !important;
                right: auto !important;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid rgba(255,255,255,0.06);
            }
            .sidebar { padding-bottom: 30px; }

            .app-header-inner {
                padding: 0 16px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: min(88vw, 320px);
                padding: 12px !important;
                border-right: 1px solid rgba(255, 255, 255, 0.08);
            }
            
            .sidebar h4 {
                font-size: 18px;
            }
            
            .sidebar a {
                padding: 12px 14px;
                font-size: 14px;
                min-height: 44px;
            }
            
            .sidebar a i {
                font-size: 15px;
                width: 20px;
            }
            
            header {
                padding: 10px 12px !important;
            }

            .app-header-inner {
                padding: 0 12px;
            }
            
            header h5 {
                font-size: 15px;
            }
            
            header small {
                font-size: 11px;
            }
            
            main > .p-4 {
                padding: 74px 12px 14px 12px !important;
            }
            
            .card-body {
                padding: 14px;
            }
            
            .btn {
                padding: 8px 12px;
                font-size: 14px;
            }

            /* Place profile card inline below navigation on small screens */
            .sidebar-footer {
                position: relative !important;
                bottom: auto !important;
                left: auto !important;
                right: auto !important;
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid rgba(255,255,255,0.06);
            }

            .profile-card-link {
                display: flex !important;
                align-items: center !important;
                gap: 10px;
                padding: 10px 12px !important;
                border-radius: 10px !important;
                box-shadow: none !important;
                transform: none !important;
                background: rgba(255,255,255,0.03) !important;
                margin: 0;
            }

            .profile-card-content { gap: 10px; }

            .profile-card-name { white-space: normal; overflow: visible; text-overflow: unset; }

            .sidebar { padding-bottom: 24px; }
        }

        @media (max-width: 576px) {
            header {
                padding: 8px 10px !important;
            }

            .app-header-inner {
                padding: 0 10px;
                gap: 10px;
            }

            .app-header-meta {
                gap: 8px;
            }

            .app-header-actions {
                gap: 6px;
            }

            .mobile-toggle {
                width: 38px;
                height: 38px;
            }

            main > .p-4 {
                padding: 70px 10px 12px 10px !important;
            }

            .sidebar a {
                padding: 12px;
                font-size: 13px;
            }

            .profile-card-link {
                padding: 10px 11px !important;
            }

            .profile-card-name {
                font-size: 13px;
            }

            .profile-card-meta {
                font-size: 11px;
            }
        }

        /* Smooth Transitions */
        a, button, .btn, .form-control, .form-select, .card, .sidebar a {
            transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        /* Focus States */
        .btn:focus, .form-control:focus, .form-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }
        /* Stackable table helper: converts wide tables into vertical cards on small screens */
        .stackable-table thead { }
        @media (max-width: 640px) {
            .stackable-table thead { display: none; }
            .stackable-table, .stackable-table tbody, .stackable-table tr, .stackable-table td { display: block; width: 100%; }
            .stackable-table tr { margin-bottom: 12px; border-bottom: 1px solid var(--border); padding-bottom: 12px; }
            .stackable-table td { padding: 8px 0; display: flex; justify-content: space-between; align-items: center; }
            .stackable-table td::before { content: attr(data-label); font-weight: 700; color: var(--text-secondary); margin-right: 12px; flex: 0 0 45%; }
            .stackable-table td > * { flex: 1 1 auto; text-align: right; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row" style="margin: 0;">
        <aside class="sidebar mobile-hidden" id="sidebar">
            <div class="brand">
                <h4 class="fw-bold mb-0">PMS for ESP</h4>
                <small>Elementary Service Personnel</small>
            </div>
            <nav>
                @php
                    $role = trim((string) optional(auth()->user()?->role)->slug);
                    $role = $role !== '' ? strtolower($role) : strtolower(trim((string) optional(auth()->user()?->role)->name));
                @endphp

                <div class="nav-section">
                    <div class="nav-section-title" style="color: rgba(255,255,255,0.85); font-weight:600; margin:8px 0 6px;">Main</div>
                    @if($role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @elseif($role === 'supervisor')
                        <a href="{{ route('supervisor.dashboard') }}" class="{{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('staff.dashboard') }}" class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @endif
                    <a href="{{ route('timetables.index') }}" class="{{ request()->routeIs('timetables.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar3"></i> Schedule
                    </a>
                    <a href="{{ route('feedback.index') }}" class="{{ request()->routeIs('feedback.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-left-text"></i> Feedback
                    </a>
                </div>

                <div class="nav-section" style="margin-top:12px;">
                    <div class="nav-section-title" style="color: rgba(255,255,255,0.85); font-weight:600; margin:8px 0 6px;">Management</div>
                    @if($role === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> Users
                        </a>
                        <a href="{{ route('admin.employees.index') }}" class="{{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                            <i class="bi bi-person-badge"></i> Employees
                        </a>
                        <a href="{{ route('admin.leaves.index') }}" class="{{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i> Leave Management
                        </a>
                        <a href="{{ route('evaluations.index') }}" class="{{ request()->routeIs('evaluations.index') ? 'active' : '' }}">
                            <i class="bi bi-journal-check"></i> Reviewed
                        </a>
                        <a href="{{ route('evaluations.create') }}" class="{{ request()->routeIs('evaluations.create') ? 'active' : '' }}">
                            <i class="bi bi-pencil-square"></i> Evaluations
                        </a>
                        <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                            <i class="bi bi-graph-up"></i> Analytics
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark"></i> Reports
                        </a>
                    @endif

                    @if($role === 'supervisor')
                        @php
                            $employeeCountForAttendanceNav = \App\Models\Employee::query()->count();
                            $markedCountForAttendanceNav = $employeeCountForAttendanceNav > 0
                                ? \App\Models\Attendance::query()
                                    ->whereDate('attendance_date', today()->toDateString())
                                    ->distinct('employee_id')
                                    ->count('employee_id')
                                : 0;
                            $attendanceMarkedTodayForNav = $employeeCountForAttendanceNav > 0
                                && $markedCountForAttendanceNav >= $employeeCountForAttendanceNav;
                        @endphp
                        <a href="{{ route('supervisor.attendance.index') }}" class="{{ request()->routeIs('supervisor.attendance.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-check"></i> Attendance
                            @if($attendanceMarkedTodayForNav)
                                <span class="badge bg-success ms-auto">Done</span>
                            @endif
                        </a>
                        <a href="{{ route('supervisor.tasks.index') }}" class="{{ request()->routeIs('supervisor.tasks.*') ? 'active' : '' }}">
                            <i class="bi bi-list-task"></i> Tasks
                        </a>
                        <a href="{{ route('evaluations.index') }}" class="{{ request()->routeIs('evaluations.index') ? 'active' : '' }}">
                            <i class="bi bi-journal-check"></i> Reviewed
                        </a>
                        <a href="{{ route('evaluations.create') }}" class="{{ request()->routeIs('evaluations.create') ? 'active' : '' }}">
                            <i class="bi bi-pencil-square"></i> Evaluations
                        </a>
                        <a href="{{ route('supervisor.reports.index') }}" class="{{ request()->routeIs('supervisor.reports.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark"></i> Reports
                        </a>
                        <a href="{{ route('supervisor.leaves.index') }}" class="{{ request()->routeIs('supervisor.leaves.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i> Leave Requests
                            @php
                                $authUser = auth()->user();
                                $unreadLeaveRequestNotificationsCount = $authUser
                                    ? $authUser->unreadNotifications()->where('data->type', 'leave_request')->count()
                                    : 0;
                            @endphp
                            @if($unreadLeaveRequestNotificationsCount > 0)
                                <span class="badge bg-danger ms-auto">{{ $unreadLeaveRequestNotificationsCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if($role === 'staff')
                        <a href="{{ route('staff.tasks.index') }}" class="{{ request()->routeIs('staff.tasks.*') ? 'active' : '' }}">
                            <i class="bi bi-list-task"></i> My Tasks
                        </a>
                        <a href="{{ route('staff.evaluations.index') }}" class="{{ request()->routeIs('staff.evaluations.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-check"></i> Reviewed
                        </a>
                        <a href="{{ route('staff.leaves.index') }}" class="{{ request()->routeIs('staff.leaves.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-plus"></i> Leaves
                        </a>
                    @endif
                </div>

                            </nav>

            <div class="sidebar-footer">
                @if(auth()->check())
                    @php
                        $user = auth()->user();
                        $initials = collect(explode(' ', trim($user->name)))->filter()->map(fn($p) => strtoupper(substr($p,0,1)))->take(2)->join('');
                    @endphp
                    <a href="{{ route('profile.show') }}" class="profile-card-link">
                        <div class="profile-card-content">
                            <div style="width:44px; height:44px; border-radius:50%; background: var(--primary-light); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0;">{{ $initials }}</div>
                            <div class="profile-card-copy">
                                <div class="profile-card-name">{{ $user->name }}</div>
                                <div class="profile-card-meta">{{ $user->role?->name ?? '' }} • Profile</div>
                            </div>
                            <i class="bi bi-chevron-right profile-card-arrow"></i>
                        </div>
                    </a>
                    <div style="display:flex; align-items:center; justify-content:flex-end; margin-top:10px;">
                        <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn" style="background: transparent; border: 1px solid rgba(255,255,255,0.08); color: white; padding:6px 8px; border-radius:8px;">Logout</button>
                        </form>
                    </div>
                @endif
            </div>
        </aside>
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()" aria-hidden="true"></div>
        <main class="p-0">
            <header>
                <div class="app-header-inner">
                        <button class="btn btn-outline-secondary mobile-toggle d-lg-none me-2" id="sidebarToggle" aria-label="Open navigation" aria-controls="sidebar" aria-expanded="false">
                            <i class="bi bi-list"></i>
                        </button>
                    <div class="app-header-meta">
                        <h5 class="mb-0">@yield('page_title', 'PMS for ESP')</h5>
                        <small class="text-muted">{{ auth()->user()?->role?->name ?? 'Guest' }} • {{ now()->format('F j, Y') }}</small>
                    </div>
                    <div class="app-header-actions">
                        @auth
                            <div class="dropdown">
                                <button class="btn position-relative p-2" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notifications">
                                    <i class="bi bi-bell fs-5"></i>
                                    @if(auth()->user()->unreadNotifications()->count() > 0)
                                        <span class="notification-dot" aria-hidden="true"></span>
                                    @endif
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px; max-height: 400px; overflow-y: auto;" aria-labelledby="notificationDropdown">
                                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                                        <span>Notifications</span>
                                        @if(auth()->user()->unreadNotifications()->count() > 0)
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="markAllNotificationsAsRead()">Mark all read</button>
                                        @endif
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    @forelse(auth()->user()->notifications()->latest()->limit(10)->get() as $notification)
                                        <li class="dropdown-item notification-item {{ $notification->read_at ? 'read' : 'unread' }}" style="white-space: normal; padding: 12px 16px;">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <a href="{{ \App\Helpers\NotificationHelper::getNotificationUrlWithMarkAsRead($notification) }}" class="text-decoration-none {{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                                                        <div class="fw-semibold">
                                                            {{ \App\Helpers\NotificationHelper::getNotificationIcon($notification) }} {{ $notification->data['message'] ?? 'New notification' }}
                                                        </div>
                                                    </a>
                                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                @if(!$notification->read_at)
                                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="d-inline m-0 p-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm text-muted p-0 m-0" title="Mark as read" style="background: none; border: none;">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </li>
                                    @empty
                                        <li class="dropdown-item text-muted text-center py-3">
                                            <i class="bi bi-bell-slash d-block fs-2 mb-2"></i>
                                            No notifications
                                        </li>
                                    @endforelse
                                    @if(auth()->user()->notifications()->count() > 10)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a href="{{ route('notifications.index') }}" class="dropdown-item text-center">
                                                View all notifications
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>
            <div class="p-4" style="padding-top: 100px;">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>

<script>
function markAllNotificationsAsRead() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('{{ route('notifications.read-all') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error marking notifications as read:', error);
    });
}

// Mobile sidebar toggle behavior
(function(){
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    if(!sidebar || !toggle || !backdrop) return;

    function openSidebar() {
        sidebar.classList.add('mobile-open');
        sidebar.classList.remove('mobile-hidden');
        backdrop.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        sidebar.classList.add('mobile-hidden');
        backdrop.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    // Expose close for inline onclick
    window.closeSidebar = closeSidebar;

    toggle.addEventListener('click', function(e){
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        if(expanded) closeSidebar(); else openSidebar();
    });

    backdrop.addEventListener('click', closeSidebar);

    // Close on ESC
    document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') closeSidebar();
    });

    // Ensure sidebar is visible on large screens
    function handleResize(){
        if(window.innerWidth >= 992){
            sidebar.classList.remove('mobile-hidden');
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('mobile-hidden');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize();
})();
</script>
</html>
