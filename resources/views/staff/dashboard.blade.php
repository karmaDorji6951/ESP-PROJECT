@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page_title', 'Staff Dashboard')

@push('styles')
<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #0F2044 0%, #16345f 58%, #1D9E75 155%);
        color: #fff;
        border-radius: 20px;
        padding: 24px 28px;
        box-shadow: 0 18px 40px rgba(15, 32, 68, 0.18);
        overflow: hidden;
        position: relative;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: auto -100px -100px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .stat-card, .section-card {
        border: 1px solid rgba(15, 32, 68, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 32, 68, 0.06);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .stat-card .card-body {
        padding: 18px;
        flex: 1;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .section-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(15, 32, 68, 0.08);
        padding: 16px 20px;
    }

    .card-row {
        align-items: stretch;
    }

    .dashboard-card-title {
        font-size: 14px;
        font-weight: 600;
        color: #526173;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 14px;
    }
    .table-avatar { display: flex; align-items: center; gap: 10px; }
    .user-avatar { width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #1D9E75 0%, #60a5fa 100%); display: grid; place-items: center; color: #ffffff; font-weight: 700; font-size: 14px; }
    .evaluation-row {
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .evaluation-row:hover {
        background: rgba(29, 158, 117, 0.05);
    }
</style>
@endpush

@section('content')
<div class="dashboard-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="text-uppercase fw-semibold mb-2" style="letter-spacing: 0.14em; opacity: 0.85; font-size: 12px;">Personal Workspace</div>
            <h2 class="fw-bold mb-2">Welcome, {{ auth()->user()->name }}.</h2>
            <p class="mb-0" style="max-width: 720px; opacity: 0.92;">Keep an eye on your tasks, attendance, and leave requests from a cleaner, more focused dashboard.</p>
        </div>
    </div>
</div>

<!-- Personal Stats -->
<div class="row g-4 mb-4 card-row">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-list-task"></i></div>
                    <div>
                        <div class="text-muted small mb-1">My Tasks</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['my_tasks'] }}</div>
                        <div class="text-muted small">Assigned to you</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-check-circle"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Completed</div>
                        <div class="fs-3 fw-bold mb-0 text-success">{{ $summary['completed_tasks'] }}</div>
                        <div class="text-muted small">Finished work</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(15, 32, 68, 0.08); color: #0F2044;"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="text-muted small mb-1">In Progress</div>
                        <div class="fs-3 fw-bold mb-0 text-info">{{ $summary['in_progress_tasks'] }}</div>
                        <div class="text-muted small">Currently active</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #D97706;"><i class="bi bi-clock"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Pending</div>
                        <div class="fs-3 fw-bold mb-0 text-warning">{{ $summary['pending_tasks'] }}</div>
                        <div class="text-muted small">Waiting to start</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Stats -->
<div class="row g-3 mb-4 card-row">
    <div class="col-md-4">
        <div class="card section-card h-100">
            <div class="card-body text-center">
                <div class="dashboard-card-title">Total Leaves</div>
                <div class="fs-2 fw-bold">{{ $summary['total_leaves'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card section-card h-100">
            <div class="card-body text-center">
                <div class="dashboard-card-title">Approved</div>
                <div class="fs-2 fw-bold text-success">{{ $summary['approved_leaves'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card section-card h-100">
            <div class="card-body text-center">
                <div class="dashboard-card-title">Pending</div>
                <div class="fs-2 fw-bold text-warning">{{ $summary['pending_leaves'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Section -->
@if ($notifications->count() > 0)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card section-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Notifications</span>
                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach ($notifications as $notification)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $notification->data['message'] ?? 'New notification' }}</div>
                                @if(isset($notification->data['title']))
                                    <small class="text-muted">Task: {{ $notification->data['title'] }}</small>
                                @endif
                                @if(isset($notification->data['deadline']))
                                    <div class="mt-1">
                                        <small class="text-warning">⏰ Deadline: {{ $notification->data['deadline'] }}</small>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end">
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                @if(!$notification->read_at)
                                    <span class="badge bg-primary ms-2">New</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Main Content -->
<div class="row g-4">
    <!-- Attendance section removed as requested -->

    <!-- My Tasks (now matching Leave Requests card) -->
    <div class="col-lg-12">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>My Tasks</span>
                <a href="{{ route('staff.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTasks as $task)
                            <tr>
                                <td class="align-middle">
                                    <div class="table-avatar">
                                        <div class="user-avatar">{{ strtoupper(substr(optional($task->assigner)->name ?? auth()->user()->name, 0, 1)) }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    <small class="text-muted">{{ Str::limit($task->description, 40) }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'Pending' ? 'bg-danger' : 'bg-info') }}">
                                        {{ $task->status }}
                                    </span>
                                </td>
                                <td class="align-middle"><small>{{ $task->deadline?->format('Y-m-d') ?? '-' }}</small></td>
                                <td class="align-middle"><small>
                                    @if($task->deadline)
                                        @if(\Carbon\Carbon::now()->gt($task->deadline))
                                            <span class="text-danger">Overdue</span>
                                        @else
                                            {{ $task->deadline->diffForHumans() }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No tasks assigned.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- My Evaluations -->
    <div class="col-lg-12">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>My Evaluations</span>
                <a href="{{ route('staff.evaluations.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Task</th>
                            <th>Evaluator</th>
                            <th>Rating</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Evaluated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myEvaluations as $evaluation)
                            <tr class="evaluation-row" data-href="{{ route('staff.evaluations.show', $evaluation) }}" tabindex="0" aria-label="Open evaluation details for {{ $evaluation->task->title ?? 'task' }}">
                                <td>
                                    <div class="fw-semibold">{{ $evaluation->task->title ?? 'N/A' }}</div>
                                    @if($evaluation->task)
                                        <small class="text-muted">Task #{{ $evaluation->task->id }}</small>
                                    @endif
                                </td>
                                <td>{{ $evaluation->evaluator->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $evaluation->rating ?? 'N/A' }}/5</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $evaluation->grade ?? 'N/A' }}</span>
                                </td>
                                <td style="max-width: 280px;">
                                    <small class="text-muted">{{ Str::limit($evaluation->remarks ?? 'No remarks provided', 80) }}</small>
                                </td>
                                <td>{{ $evaluation->evaluated_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No evaluations available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- My Leave Requests -->
    <div class="col-lg-12">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>My Leave Requests</span>
                <a href="{{ route('staff.leaves.create') }}" class="btn btn-sm btn-outline-success">Request Leave</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myLeaves as $leave)
                            <tr>
                                <td>{{ $leave->leave_type ?? 'N/A' }}</td>
                                <td>{{ $leave->from_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $leave->to_date?->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    @if($leave->from_date && $leave->to_date)
                                        {{ $leave->from_date->diffInDays($leave->to_date) + 1 }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $leave->status === 'Approved' ? 'bg-success' : ($leave->status === 'Rejected' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $leave->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No leave requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.evaluation-row[data-href]').forEach(function(row) {
        row.addEventListener('click', function() {
            window.location.href = row.dataset.href;
        });

        row.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                window.location.href = row.dataset.href;
            }
        });
    });
});
</script>

@endsection
