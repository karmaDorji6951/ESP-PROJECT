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
        body { background: #f4f7fb; }
        .sidebar { min-height: 100vh; background: #1f2937; }
        .sidebar a { color: #cbd5e1; text-decoration: none; display: block; padding: .75rem 1rem; border-radius: .5rem; }
        .sidebar a:hover, .sidebar a.active { background: #374151; color: #fff; }
        .card-soft { border: 0; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
        .table thead th { background: #eef2ff; }
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
