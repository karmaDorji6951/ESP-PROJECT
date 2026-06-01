@extends('layouts.app')

@section('page_title', 'My Tasks')
@section('title', 'My Assigned Tasks')

@section('content')
<div class="tasks-page">
    <!-- Header with Create Button -->
    <div class="app-page-hero d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="app-page-hero-kicker mb-2">Supervisor Workspace</div>
            <h1 class="app-page-hero-title mb-2">Assigned Tasks</h1>
            <p class="app-page-hero-subtitle">Manage and track tasks assigned to your team.</p>
        </div>
        <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-light app-page-hero-action">
            + Assign New Task
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Tasks Table -->
    <div class="tasks-table-wrapper">
        @if($tasks->count() > 0)
            <table class="tasks-table">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Task Title</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Due</th>
                        <th>Assigned Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr class="task-row">
                        <td class="employee-cell">
                            <div class="employee-info">
                                <div class="employee-avatar">
                                    {{ strtoupper(substr($task->employee->name ?? 'N', 0, 1)) }}
                                </div>
                                <div class="employee-details">
                                    <div class="employee-name">{{ $task->employee->name ?? 'Unknown' }}</div>
                                    <div class="employee-id">#{{ $task->employee->id ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="title-cell">
                            <div class="task-title">{{ $task->title }}</div>
                            @if($task->description)
                            <div class="task-description">{{ Str::limit($task->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="status-cell">
                            <span class="status-badge {{ $task->status === 'Completed' ? 'status-completed' : ($task->status === 'In Progress' ? 'status-in-progress' : 'status-pending') }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td class="deadline-cell">
                            @if($task->deadline)
                                <span class="deadline-text">{{ $task->deadline->format('M d, Y') }}</span>
                                @if($task->deadline->isPast() && $task->status !== 'Completed')
                                <div class="deadline-overdue">Overdue</div>
                                @endif
                            @else
                                <span class="text-muted">No deadline</span>
                            @endif
                        </td>
                        <td class="deadline-cell">
                            @if($task->deadline)
                                @if(\Carbon\Carbon::now()->gt($task->deadline) && $task->status !== 'Completed')
                                    <span class="text-danger">Overdue</span>
                                @else
                                    {{ $task->deadline->diffForHumans() }}
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="date-cell">
                            {{ $task->created_at->format('M d, Y') }}
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('supervisor.tasks.show', $task) }}" class="action-btn btn-view" title="View">👁</a>
                            <a href="{{ route('supervisor.tasks.edit', $task) }}" class="action-btn btn-edit" title="Edit">✎</a>
                            @if($task->status === 'Completed' && $task->latestSubmission)
                                @if($task->evaluation)
                                    <a href="{{ route('supervisor.tasks.evaluation.create', $task) }}" class="action-btn btn-evaluate" title="Evaluated">✓</a>
                                @else
                                    <a href="{{ route('supervisor.tasks.evaluation.create', $task) }}" class="action-btn btn-evaluate" title="Evaluate">📝</a>
                                @endif
                            @endif
                            <form action="{{ route('supervisor.tasks.destroy', $task) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn btn-delete" title="Delete">🗑</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination-wrapper">
                {{ $tasks->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <div class="empty-title">No Tasks Assigned Yet</div>
                <div class="empty-description">Start by assigning a new task to your staff</div>
                <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">
                    Assign Your First Task
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
    <style>
        .tasks-page {
            --bg-primary: #ffffff;
            --bg-secondary: #f5f1e8;
            --border-color: #d4c4a8;
            --text-primary: #2c3e50;
            --text-secondary: #4f6472;
            --text-muted: #7a6a5a;
            --supervisor-accent: #2c3e50;
            --supervisor-dark: #1a252f;
            --supervisor-light: #7a9fb5;
        }

        /* Content Header */
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
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

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
            color: #f5f1e8;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #3d5568 0%, #1a252f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-icon {
            font-size: 18px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d9e8e1 0%, #f5f1e8 100%);
            color: #2c3e50;
            border: 1px solid #b7c9bf;
        }

        /* Tasks Table */
        .tasks-table-wrapper {
            background-color: var(--bg-primary);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
        }

        .tasks-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tasks-table thead {
            background: linear-gradient(135deg, #f5f1e8 0%, #ede6d9 100%);
            border-bottom: 2px solid var(--border-color);
        }

        .tasks-table th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tasks-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s;
        }

        .tasks-table tbody tr:hover {
            background-color: #ede6d9;
        }

        .tasks-table td {
            padding: 16px;
            color: var(--text-primary);
            vertical-align: middle;
        }

        .employee-cell {
            min-width: 200px;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2c3e50 0%, #7a9fb5 100%);
            color: #f5f1e8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .employee-details {
            flex: 1;
        }

        .employee-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .employee-id {
            font-size: 12px;
            color: var(--text-muted);
        }

        .title-cell {
            min-width: 250px;
        }

        .task-title {
            font-weight: 500;
            color: var(--text-primary);
        }

        .task-description {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .status-cell {
            min-width: 130px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-in-progress {
            background-color: #dbeafe;
            color: #0c4a6e;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .deadline-cell {
            min-width: 140px;
        }

        .deadline-text {
            color: var(--text-primary);
            font-weight: 500;
        }

        .deadline-overdue {
            font-size: 12px;
            color: var(--danger);
            font-weight: 600;
            margin-top: 2px;
        }

        .date-cell {
            color: var(--text-muted);
            font-size: 14px;
            min-width: 120px;
        }

        .actions-cell {
            display: flex;
            gap: 8px;
            min-width: 100px;
            align-items: center;
            justify-content: center;
        }

        .action-btn {
            background: none;
            border: 1px solid transparent;
            font-size: 18px;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 6px;
            transition: all 0.3s;
            min-width: 36px;
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-edit:hover {
            background-color: #e6eef4;
        }

        .btn-edit {
            background: #eef4f8;
            color: #2c3e50;
        }

        .btn-delete {
            color: #7a4a4a;
        }

        .btn-delete:hover {
            color: var(--danger);
            background-color: #f8e8e8;
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 16px;
            display: flex;
            justify-content: center;
            border-top: 1px solid var(--border-color);
            background: #f5f1e8;
        }

        .pagination-wrapper ul {
            list-style: none;
            display: flex;
            gap: 8px;
        }

        .pagination-wrapper li {
            margin: 0;
        }

        .pagination-wrapper a,
        .pagination-wrapper span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 4px;
            text-decoration: none;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            font-size: 14px;
            transition: all 0.3s;
        }

        .pagination-wrapper a:hover {
            background-color: var(--supervisor-accent);
            color: white;
            border-color: var(--supervisor-accent);
        }

        .pagination-wrapper .active span {
            background-color: var(--supervisor-accent);
            color: white;
            border-color: var(--supervisor-accent);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 56px 20px;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }

        .empty-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .empty-description {
            color: var(--text-muted);
            margin-bottom: 24px;
        }

        .tasks-page .text-muted,
        .tasks-page td,
        .tasks-page th,
        .tasks-page h1,
        .tasks-page .task-title,
        .tasks-page .employee-name,
        .tasks-page .deadline-text {
            color: var(--text-primary);
        }

        .tasks-page small,
        .tasks-page .employee-id,
        .tasks-page .date-cell,
        .tasks-page .task-description,
        .tasks-page .empty-description {
            color: var(--text-muted);
        }

        /* Utility Classes */
        .text-muted {
            color: var(--text-muted);
        }

        /* Mobile Responsive */
        @media (max-width: 1200px) {
            .supervisor-content {
                padding: 16px;
            }

            .content-header {
                flex-direction: column;
                gap: 16px;
            }

            .tasks-table {
                font-size: 13px;
            }

            .tasks-table td,
            .tasks-table th {
                padding: 12px;
            }
        }

        @media (max-width: 768px) {
            .supervisor-content {
                padding: 12px;
            }

            .content-header {
                flex-direction: column;
                gap: 12px;
            }

            .btn {
                width: 100%;
            }

            .tasks-table-wrapper {
                overflow-x: auto;
            }

            .tasks-table {
                min-width: 600px;
                font-size: 12px;
            }

            .tasks-table td,
            .tasks-table th {
                padding: 8px;
            }

            .employee-cell {
                min-width: 150px;
            }

            .title-cell {
                min-width: 180px;
            }
        }

        @media (max-width: 480px) {
            .header-title h1 {
                font-size: 22px;
            }

            .tasks-table {
                min-width: 500px;
                font-size: 11px;
            }

            .tasks-table td,
            .tasks-table th {
                padding: 6px;
            }

            .action-btn {
                padding: 4px 6px;
                font-size: 16px;
            }
        }
    </style>
@endpush
