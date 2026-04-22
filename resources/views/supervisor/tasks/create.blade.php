@extends('layouts.app')

@section('content')
<div class="supervisor-tasks-container">
    <div class="supervisor-sidebar">
        <div class="sidebar-header">
            <div class="logo-section">
                <div class="logo">📋</div>
                <span class="app-name">ESP Manager</span>
            </div>
        </div>

        <nav class="sidebar-menu supervisor-menu">
            <a href="{{ route('dashboard') }}" class="menu-item">
                <span class="menu-icon">📊</span>
                <span class="menu-label">Dashboard</span>
            </a>
            <a href="{{ route('supervisor.tasks.index') }}" class="menu-item active">
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

    <div class="supervisor-main">
        <div class="supervisor-topbar">
            <button class="sidebar-toggle" id="sidebarToggle">☰</button>
            <div class="topbar-title">Assign New Task</div>
            <div class="topbar-actions">
                <button class="notification-btn">📢</button>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <div class="supervisor-content">
            <!-- Header -->
            <div class="content-header">
                <div class="header-title">
                    <h1>Assign New Task</h1>
                    <p class="text-muted">Create and assign a task to your team members</p>
                </div>
            </div>

            <!-- Form -->
            <div class="form-container">
                <form action="{{ route('supervisor.tasks.store') }}" method="POST" class="task-form">
                    @csrf

                    <!-- Employee Selection -->
                    <div class="form-group">
                        <label for="employee_id" class="form-label">Select Employee *</label>
                        <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                            <option value="">-- Choose an employee --</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->first_name }} {{ $employee->last_name }} (ID: {{ $employee->id }})
                            </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Task Title -->
                    <div class="form-group">
                        <label for="title" class="form-label">Task Title *</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                               placeholder="Enter task title" value="{{ old('title') }}" required>
                        @error('title')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Task Description -->
                    <div class="form-group">
                        <label for="description" class="form-label">Task Description</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" placeholder="Enter task description (optional)">{{ old('description') }}</textarea>
                        @error('description')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="status" class="form-label">Status *</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('status')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div class="form-group">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="form-control @error('deadline') is-invalid @enderror" 
                               value="{{ old('deadline') }}">
                        @error('deadline')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            ✓ Assign Task
                        </button>
                        <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

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

body {
    font-family: 'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background-color: var(--bg-secondary);
    color: var(--text-primary);
}

.supervisor-tasks-container {
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

/* Content Header */
.content-header {
    margin-bottom: 24px;
}

.header-title h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.header-title p {
    color: var(--text-muted);
    font-size: 14px;
}

/* Form Container */
.form-container {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 32px;
    max-width: 600px;
}

.task-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Form Groups */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 14px;
}

.form-control {
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s;
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

.form-control:focus {
    outline: none;
    border-color: var(--supervisor-accent);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control.is-invalid {
    border-color: var(--danger);
}

.form-control.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

/* Error Messages */
.error-message {
    color: var(--danger);
    font-size: 13px;
    font-weight: 500;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 12px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    flex: 1;
    text-align: center;
}

.btn-primary {
    background-color: var(--supervisor-accent);
    color: white;
}

.btn-primary:hover {
    background-color: var(--supervisor-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-secondary {
    background-color: var(--border-color);
    color: var(--text-primary);
}

.btn-secondary:hover {
    background-color: var(--bg-secondary);
}

/* Utility Classes */
.text-muted {
    color: var(--text-muted);
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

    .form-container {
        padding: 24px;
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

    .form-container {
        padding: 20px;
        max-width: none;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .supervisor-container {
        flex-direction: column;
    }

    .topbar-title {
        font-size: 16px;
    }

    .header-title h1 {
        font-size: 22px;
    }

    .form-container {
        padding: 16px;
        border-radius: 6px;
    }

    .form-label {
        font-size: 13px;
    }

    .form-control {
        font-size: 13px;
        padding: 10px 12px;
    }

    .btn {
        font-size: 13px;
        padding: 10px 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.supervisor-sidebar');

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
});
</script>
@endsection
