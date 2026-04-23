<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PMS for ESP')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #06b6d4;
            --primary-dark: #0369a1;
            --secondary: #a855f7;
            --warning: #f97316;
            --success: #16a34a;
            --danger: #dc2626;
        }

        body {
            background: linear-gradient(135deg, #f1f5f9 0%, #e0f2fe 50%, #f8fafc 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Manrope', sans-serif;
            min-height: 100vh;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f766e 0%, #06b6d4 50%, #0369a1 100%);
            padding: 24px 16px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .sidebar h4 {
            background: linear-gradient(135deg, #ffffff 0%, #cffafe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
            font-size: 20px;
            margin-bottom: 8px !important;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            display: block;
            padding: 12px 14px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            margin-bottom: 8px;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transform: translateX(6px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .sidebar a.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 100%);
            color: #ffffff;
            border-left: 4px solid rgba(255, 255, 255, 0.8);
            padding-left: 10px;
            font-weight: 600;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-bottom: 2px solid #e0f2fe !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        header h5 {
            color: #0f172a;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        header .btn-outline-danger {
            border-color: var(--danger);
            color: var(--danger);
            transition: all 0.3s ease;
        }

        header .btn-outline-danger:hover {
            background: linear-gradient(135deg, var(--danger) 0%, #b91c1c 100%);
            border-color: var(--danger);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .card-soft {
            border: 1px solid #e0f2fe !important;
            box-shadow: 0 4px 16px rgba(6, 182, 212, 0.1);
            border-radius: 12px !important;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .card-soft:hover {
            box-shadow: 0 8px 24px rgba(6, 182, 212, 0.15);
            transform: translateY(-4px);
        }

        .table thead th {
            background: linear-gradient(135deg, #cffafe 0%, #e0f2fe 100%) !important;
            color: #0369a1;
            font-weight: 700;
            border-bottom: 2px solid #a5f3fc !important;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fafc !important;
            box-shadow: inset 0 0 8px rgba(6, 182, 212, 0.1);
        }

        .alert {
            border-radius: 8px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%);
            border-color: #16a34a;
            color: #15803d;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
            border-color: #dc2626;
            color: #b91c1c;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fed7aa 0%, #ffedd5 100%);
            border-color: #f97316;
            color: #b45309;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #0e919e 100%);
            border: none;
            box-shadow: 0 2px 8px rgba(6, 182, 212, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0e919e 0%, var(--primary-dark) 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #15803d 100%);
            border: none;
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
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .badge-primary {
            background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
            color: var(--primary-dark);
        }

        .badge-success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #15803d;
        }

        .badge-warning {
            background: linear-gradient(135deg, #fed7aa 0%, #fecaca 100%);
            color: #b45309;
        }

        .badge-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
            color: #b91c1c;
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 sidebar p-3 text-white">
            <div class="mb-4">
                <h4 class="fw-bold mb-0">PMS for ESP</h4>
                <small class="text-white-50">Elementary Service Personnel</small>
            </div>
            <nav class="d-grid gap-2">
                @php
                    $role = optional(auth()->user()?->role)->slug;
                @endphp

                @if($role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}">Users</a>
                    <a href="{{ route('admin.employees.index') }}">Employees</a>
                    <a href="{{ route('timetables.index') }}" class="{{ request()->routeIs('timetables.*') ? 'active' : '' }}">Timetable</a>
                @elseif($role === 'supervisor')
                    <a href="{{ route('supervisor.dashboard') }}">Dashboard</a>
                    <a href="{{ route('supervisor.attendance.index') }}">Attendance</a>
                    <a href="{{ route('supervisor.tasks.index') }}">Tasks</a>
                    <a href="{{ route('timetables.index') }}" class="{{ request()->routeIs('timetables.*') ? 'active' : '' }}">Timetable</a>
                @elseif($role === 'staff')
                    <a href="{{ route('staff.dashboard') }}">Dashboard</a>
                    <a href="{{ route('staff.tasks.index') }}">My Tasks</a>
                    <a href="{{ route('staff.leaves.index') }}">Leaves</a>
                    <a href="{{ route('timetables.index') }}" class="{{ request()->routeIs('timetables.*') ? 'active' : '' }}">Timetable</a>
                @endif
                <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">Profile</a>
            </nav>
        </aside>
        <main class="col-lg-10 p-0">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">@yield('page_title', 'PMS for ESP')</h5>
                    <small class="text-muted">{{ auth()->user()?->role?->name ?? 'Guest' }}</small>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </form>
            </header>
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
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
</html>
