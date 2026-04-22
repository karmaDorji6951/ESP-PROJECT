<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Dashboard - ESP</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:500,600,700,800|space-grotesk:500,600,700&display=swap" rel="stylesheet" />
        <style>
            :root {
                --page-bg: #f8fafc;
                --sidebar-bg: #15262a;
                --sidebar-border: #1f3337;
                --ink: #0f1e2c;
                --ink-light: #1e293b;
                --muted: #64748b;
                --muted-light: #94a3b8;
                --card: #ffffff;
                --card-hover: #f8fafc;
                --line: #e2e8f0;
                --line-light: #cbd5e1;
                --brand: #0f766e;
                --brand-light: #14b8a6;
                --brand-soft: #ccf7f3;
                --brand-bg: #f0fdfc;
                --accent: #f59e0b;
                --accent-soft: #fef3c7;
                --alert: #e11d48;
                --alert-soft: #ffe4e6;
                --success: #10b981;
                --success-soft: #d1fae5;
                --admin-accent: #7c3aed;
                --admin-soft: #ede9fe;
                --shadow: 0 20px 50px rgba(15, 30, 44, 0.08);
                --shadow-sm: 0 8px 16px rgba(15, 30, 44, 0.06);
                --shadow-lg: 0 40px 80px rgba(15, 30, 44, 0.12);
                --radius-lg: 24px;
                --radius-md: 16px;
                --radius-sm: 12px;
                --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-normal: 300ms cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                box-sizing: border-box;
            }

            html {
                scroll-behavior: smooth;
            }

            body {
                margin: 0;
                font-family: "Manrope", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                background: var(--page-bg);
                color: var(--ink);
                min-height: 100vh;
                display: grid;
                grid-template-columns: 280px 1fr;
                grid-template-rows: 1fr;
            }

            /* ====== SIDEBAR ====== */
            .sidebar {
                background: var(--sidebar-bg);
                border-right: 1px solid var(--sidebar-border);
                display: flex;
                flex-direction: column;
                padding: 0;
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            }

            .sidebar-header {
                padding: 20px 16px;
                border-bottom: 1px solid var(--sidebar-border);
                display: flex;
                align-items: center;
                gap: 12px;
                text-decoration: none;
                color: #ffffff;
                font-weight: 700;
            }

            .sidebar-mark {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: grid;
                place-items: center;
                background: linear-gradient(135deg, var(--admin-accent) 0%, #a78bfa 100%);
                color: #ffffff;
                font-family: "Space Grotesk", monospace;
                font-weight: 700;
                font-size: 18px;
            }

            .brand h1 {
                margin: 0;
                font-size: 1rem;
                line-height: 1.2;
            }

            .brand p {
                margin: 3px 0 0;
                color: var(--muted-light);
                font-size: 0.75rem;
                font-weight: 500;
            }

            .nav-section {
                padding: 20px 12px;
                border-bottom: 1px solid var(--sidebar-border);
            }

            .nav-label {
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: var(--muted-light);
                padding: 0 12px;
                margin-bottom: 8px;
            }

            .nav-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 12px;
                border-radius: 12px;
                color: var(--muted-light);
                text-decoration: none;
                transition: all var(--transition-fast);
                margin-bottom: 4px;
            }

            .nav-item:hover,
            .nav-item.active {
                background: rgba(124, 58, 237, 0.15);
                color: #ffffff;
            }

            .nav-badge {
                margin-left: auto;
                background: var(--admin-accent);
                color: #ffffff;
                border-radius: 9999px;
                padding: 2px 8px;
                font-size: 0.75rem;
                font-weight: 700;
            }

            .sidebar-footer {
                margin-top: auto;
                padding: 16px 12px;
                border-top: 1px solid var(--sidebar-border);
            }

            .user-profile {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.05);
                text-decoration: none;
                color: #ffffff;
                transition: all var(--transition-fast);
            }

            .user-profile:hover {
                background: rgba(124, 58, 237, 0.15);
            }

            .user-avatar {
                width: 36px;
                height: 36px;
                border-radius: 8px;
                background: linear-gradient(135deg, var(--admin-accent) 0%, #a78bfa 100%);
                display: grid;
                place-items: center;
                color: #ffffff;
                font-weight: 700;
                font-size: 14px;
            }

            .user-name {
                margin: 0;
                font-size: 0.9rem;
                font-weight: 700;
            }

            .user-role {
                margin: 0;
                font-size: 0.75rem;
                color: var(--muted-light);
            }

            .main-container {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            .topbar {
                background: var(--card);
                border-bottom: 1px solid var(--line);
                padding: 16px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: var(--shadow-sm);
            }

            .topbar-left {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .menu-toggle {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: var(--ink);
                display: none;
            }

            .breadcrumb {
                color: var(--muted);
                font-size: 0.95rem;
            }

            .breadcrumb strong {
                color: var(--ink);
                font-weight: 700;
            }

            .topbar-right {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .icon-btn {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: var(--muted);
                transition: color var(--transition-fast);
                padding: 8px;
            }

            .icon-btn:hover {
                color: var(--admin-accent);
            }

            .content {
                flex: 1;
                padding: 32px 28px;
                overflow-y: auto;
            }

            .page-header {
                margin-bottom: 28px;
            }

            .page-title {
                margin: 0;
                font-size: 2rem;
                font-weight: 800;
                color: var(--ink);
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .page-subtitle {
                margin: 8px 0 0;
                color: var(--muted);
                font-size: 0.95rem;
            }

            /* ====== DASHBOARD GRID ====== */
            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 16px;
                margin-bottom: 24px;
            }

            .kpi-card {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 20px;
                box-shadow: var(--shadow-sm);
                transition: all var(--transition-normal);
            }

            .kpi-card:hover {
                border-color: var(--admin-accent);
                box-shadow: var(--shadow);
                transform: translateY(-4px);
            }

            .kpi-icon {
                font-size: 28px;
                margin-bottom: 12px;
            }

            .kpi-label {
                font-size: 0.85rem;
                color: var(--muted);
                margin: 0 0 4px;
                font-weight: 600;
            }

            .kpi-value {
                font-size: 2rem;
                font-weight: 800;
                color: var(--admin-accent);
                margin: 0;
            }

            .kpi-change {
                font-size: 0.8rem;
                color: var(--success);
                margin-top: 8px;
            }

            /* ====== TASK STATS ====== */
            .task-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 16px;
                margin-bottom: 24px;
            }

            .stat-item {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 16px;
                text-align: center;
            }

            .stat-number {
                font-size: 2.5rem;
                font-weight: 800;
                margin: 0;
            }

            .stat-label {
                font-size: 0.85rem;
                color: var(--muted);
                margin-top: 4px;
            }

            .stat-item.pending .stat-number { color: var(--accent); }
            .stat-item.in-progress .stat-number { color: var(--brand); }
            .stat-item.completed .stat-number { color: var(--success); }

            /* ====== TABLES ====== */
            .panel {
                background: var(--card);
                border: 1px solid var(--line);
                border-radius: var(--radius-md);
                padding: 20px;
                box-shadow: var(--shadow-sm);
                margin-bottom: 20px;
            }

            .panel h3 {
                margin: 0 0 16px;
                font-size: 1.1rem;
                font-weight: 700;
            }

            .table-wrapper {
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.9rem;
            }

            th {
                background: var(--card-hover);
                padding: 12px;
                text-align: left;
                font-weight: 700;
                color: var(--ink-light);
                border-bottom: 2px solid var(--line-light);
            }

            td {
                padding: 12px;
                border-bottom: 1px solid var(--line);
            }

            tr:hover {
                background: var(--card-hover);
            }

            .status-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 9999px;
                font-size: 0.8rem;
                font-weight: 600;
            }

            .status-pending {
                background: var(--accent-soft);
                color: #92400e;
            }

            .status-in-progress {
                background: var(--brand-soft);
                color: #134e4a;
            }

            .status-completed {
                background: var(--success-soft);
                color: #065f46;
            }

            .status-approved {
                background: var(--success-soft);
                color: #065f46;
            }

            .status-rejected {
                background: var(--alert-soft);
                color: #831843;
            }

            .action-btn {
                display: inline-block;
                padding: 6px 12px;
                border-radius: 8px;
                background: var(--admin-accent);
                color: #ffffff;
                text-decoration: none;
                font-size: 0.8rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all var(--transition-fast);
            }

            .action-btn:hover {
                background: #6d28d9;
                transform: translateY(-2px);
            }

            /* ====== FOOTER ====== */
            .footer {
                padding: 20px 28px;
                text-align: center;
                color: var(--muted);
                font-size: 0.85rem;
                border-top: 1px solid var(--line);
                display: flex;
                justify-content: space-between;
            }

            /* ====== RESPONSIVE ====== */
            @media (max-width: 1200px) {
                .kpi-grid {
                    grid-template-columns: repeat(2, 1fr);
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
                    height: 100vh;
                    z-index: 1000;
                    transform: translateX(-100%);
                    overflow-y: auto;
                    width: 280px;
                    transition: transform var(--transition-normal);
                }

                .sidebar.open {
                    transform: translateX(0);
                }

                .sidebar-overlay {
                    position: fixed;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.3);
                    opacity: 0;
                    pointer-events: none;
                    z-index: 999;
                    transition: opacity var(--transition-normal);
                }

                .sidebar-overlay.visible {
                    opacity: 1;
                    pointer-events: auto;
                }

                .menu-toggle {
                    display: block;
                }

                .kpi-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .task-stats {
                    grid-template-columns: 1fr;
                }

                .content {
                    padding: 16px 12px;
                }

                .page-title {
                    font-size: 1.5rem;
                }

                table {
                    font-size: 0.8rem;
                }

                th, td {
                    padding: 8px;
                }

                .footer {
                    flex-direction: column;
                    gap: 8px;
                }
            }

            @media (max-width: 480px) {
                .kpi-grid {
                    grid-template-columns: 1fr;
                }

                .page-title {
                    font-size: 1.2rem;
                }

                .topbar {
                    padding: 12px;
                    flex-direction: column;
                    gap: 12px;
                }

                .topbar-left,
                .topbar-right {
                    width: 100%;
                    justify-content: space-between;
                }
            }
        </style>
    </head>
    <body>
        <!-- SIDEBAR NAVIGATION -->
        <nav class="sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-header">
                <div class="sidebar-mark">⚙️</div>
                <div class="brand">
                    <h1>ESP Admin</h1>
                    <p>System Management</p>
                </div>
            </a>

            <div class="nav-section">
                <div class="nav-label">Management</div>
                <a href="{{ route('dashboard') }}" class="nav-item active">
                    📊 Dashboard
                </a>
                <a href="{{ route('employees.index') }}" class="nav-item">
                    👥 Staff Directory
                    <span class="nav-badge">{{ $summary['total_staff'] }}</span>
                </a>
                <a href="{{ route('attendance.index') }}" class="nav-item">
                    📋 Attendance
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-item">
                    ✓ All Tasks
                    <span class="nav-badge">{{ $summary['total_tasks'] }}</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Approvals</div>
                <a href="{{ route('leaves.index') }}" class="nav-item">
                    📅 Leave Requests
                    <span class="nav-badge">{{ $summary['pending_leaves'] }}</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-label">Reports</div>
                <a href="{{ route('reports.index') }}" class="nav-item">
                    📈 Reports
                </a>
            </div>

            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%; border: none; cursor: pointer; justify-content: flex-start;">
                        🚪 Logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- MAIN CONTENT -->
        <div class="main-container">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle sidebar">☰</button>
                    <nav class="breadcrumb">
                        <span>Welcome,</span>
                        <strong>{{ $user->name }}</strong>
                    </nav>
                </div>

                <div class="topbar-right">
                    <button class="icon-btn" title="Notifications">🔔</button>
                    <button class="icon-btn" title="More options">⋯</button>
                </div>
            </header>

            <div class="content">
                <!-- PAGE HEADER -->
                <div class="page-header">
                    <h2 class="page-title">
                        ⚙️ System Administration
                    </h2>
                    <p class="page-subtitle">Full control over all personnel and resources</p>
                </div>

                <!-- KEY METRICS -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-icon">👥</div>
                        <p class="kpi-label">Total Staff</p>
                        <p class="kpi-value">{{ $summary['total_staff'] }}</p>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon">✓</div>
                        <p class="kpi-label">Present Today</p>
                        <p class="kpi-value">{{ $summary['present_today'] }}</p>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon">📝</div>
                        <p class="kpi-label">Total Tasks</p>
                        <p class="kpi-value">{{ $summary['total_tasks'] }}</p>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon">⏳</div>
                        <p class="kpi-label">Pending Approvals</p>
                        <p class="kpi-value">{{ $summary['pending_leaves'] }}</p>
                    </div>
                </div>

                <!-- TASK STATISTICS -->
                <div class="task-stats">
                    <div class="stat-item pending">
                        <p class="stat-number">{{ $taskStats['pending'] }}</p>
                        <p class="stat-label">Pending Tasks</p>
                    </div>
                    <div class="stat-item in-progress">
                        <p class="stat-number">{{ $taskStats['in_progress'] }}</p>
                        <p class="stat-label">In Progress</p>
                    </div>
                    <div class="stat-item completed">
                        <p class="stat-number">{{ $taskStats['completed'] }}</p>
                        <p class="stat-label">Completed</p>
                    </div>
                </div>

                <!-- PENDING LEAVE REQUESTS -->
                <div class="panel">
                    <h3>📅 Pending Leave Requests ({{ $pendingLeaves->count() }})</h3>
                    @if($pendingLeaves->isNotEmpty())
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingLeaves as $leave)
                                        <tr>
                                            <td><strong>{{ $leave->employee?->name ?? $leave->user?->name }}</strong></td>
                                            <td>{{ $leave->leave_type }}</td>
                                            <td>{{ $leave->start_date->format('M d, Y') }}</td>
                                            <td>{{ $leave->end_date->format('M d, Y') }}</td>
                                            <td><span class="status-badge status-pending">{{ $leave->status }}</span></td>
                                            <td>
                                                <form action="{{ route('leaves.approve', $leave) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="action-btn" style="background: var(--success);">Approve</button>
                                                </form>
                                                <form action="{{ route('leaves.reject', $leave) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="action-btn" style="background: var(--alert);">Reject</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: var(--muted); text-align: center; padding: 20px;">No pending leave requests</p>
                    @endif
                </div>

                <!-- RECENT TASKS -->
                <div class="panel">
                    <h3>📋 Recent Tasks ({{ $recentTasks->count() }})</h3>
                    @if($recentTasks->isNotEmpty())
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Assigned To</th>
                                        <th>Assigned By</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTasks as $task)
                                        <tr>
                                            <td><strong>{{ $task->title }}</strong></td>
                                            <td>{{ $task->employee?->name }}</td>
                                            <td>{{ $task->assigner?->name }}</td>
                                            <td>{{ $task->deadline?->format('M d, Y') ?? 'N/A' }}</td>
                                            <td><span class="status-badge status-{{ strtolower(str_replace(' ', '-', $task->status)) }}">{{ $task->status }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: var(--muted); text-align: center; padding: 20px;">No tasks found</p>
                    @endif
                </div>

                <!-- TODAY'S ATTENDANCE -->
                <div class="panel">
                    <h3>✓ Today's Attendance ({{ $recentAttendance->count() }})</h3>
                    @if($recentAttendance->isNotEmpty())
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Status</th>
                                        <th>Recorded At</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendance as $attendance)
                                        <tr>
                                            <td><strong>{{ $attendance->employee?->name }}</strong></td>
                                            <td><span class="status-badge" style="background: {{ $attendance->status === 'Present' ? 'var(--success-soft)' : 'var(--alert-soft)' }}; color: {{ $attendance->status === 'Present' ? '#065f46' : '#831843' }};">{{ $attendance->status }}</span></td>
                                            <td>{{ $attendance->created_at->format('h:i A') }}</td>
                                            <td>{{ $attendance->remarks ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p style="color: var(--muted); text-align: center; padding: 20px;">No attendance records for today</p>
                    @endif
                </div>
            </div>

            <footer class="footer">
                <span>Employee Success Portal - Admin Dashboard</span>
                <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }} | PHP v{{ PHP_VERSION }}</span>
            </footer>
        </div>

        <script>
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const overlay = document.getElementById('sidebarOverlay');

            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('visible');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('visible');
            });

            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('visible');
                    }
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('visible');
                }
            });
        </script>
    </body>
</html>
