@extends('layouts.app')

@section('title', 'Staff Dashboard')
@section('page_title', 'Staff Dashboard')

@section('content')
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Welcome!</strong> - Track your tasks, attendance, and leave requests here.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Personal Stats -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-list-task text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">My Tasks</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['my_tasks'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Completed</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $summary['completed_tasks'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-hourglass-split text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">In Progress</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $summary['in_progress_tasks'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $summary['pending_tasks'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leave Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Total Leaves</div>
                <div class="fs-2 fw-bold">{{ $summary['total_leaves'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Approved</div>
                <div class="fs-2 fw-bold text-success">{{ $summary['approved_leaves'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Pending</div>
                <div class="fs-2 fw-bold text-warning">{{ $summary['pending_leaves'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Section -->
@if ($notifications->count() > 0)
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="card card-soft border-info">
            <div class="card-header bg-info text-white fw-semibold d-flex justify-content-between align-items-center">
                <span>🔔 Recent Notifications</span>
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
    <!-- My Recent Attendance -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">My Attendance (Last 10 Days)</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->attendance_date?->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge {{ $attendance->status === 'Present' ? 'bg-success' : ($attendance->status === 'Absent' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                                <td><small>{{ $attendance->remarks ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No attendance records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- My Tasks -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>My Tasks</span>
                <a href="{{ route('staff.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($myTasks as $task)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $task->title }}</div>
                                    <small class="text-muted">{{ Str::limit($task->description, 40) }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'Pending' ? 'bg-danger' : 'bg-info') }}">
                                        {{ $task->status }}
                                    </span>
                                </td>
                                <td><small>{{ $task->deadline?->format('Y-m-d') ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No tasks assigned.</td>
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

@endsection
