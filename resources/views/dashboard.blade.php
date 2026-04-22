@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card card-soft"><div class="card-body"><div class="text-muted">Total Staff</div><div class="fs-3 fw-bold">{{ $summary['total_staff'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card card-soft"><div class="card-body"><div class="text-muted">Present Today</div><div class="fs-3 fw-bold">{{ $summary['present_today'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card card-soft"><div class="card-body"><div class="text-muted">Pending Tasks</div><div class="fs-3 fw-bold">{{ $summary['pending_tasks'] }}</div></div></div></div>
    <div class="col-md-3"><div class="card card-soft"><div class="card-body"><div class="text-muted">Pending Leaves</div><div class="fs-3 fw-bold">{{ $summary['pending_leaves'] }}</div></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-soft h-100">
            <div class="card-header bg-white fw-semibold">Notifications</div>
            <div class="card-body">
                @forelse($notifications as $notification)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="fw-semibold">{{ $notification->data['title'] ?? 'Notification' }}</div>
                        <div class="text-muted small">{{ $notification->data['message'] ?? '' }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">No notifications.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card card-soft mb-4">
            <div class="card-header bg-white fw-semibold">Recent Attendance</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Employee</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($recentAttendance as $attendance)
                        <tr>
                            <td>{{ $attendance->employee?->name }}</td>
                            <td>{{ $attendance->attendance_date?->format('Y-m-d') }}</td>
                            <td>{{ $attendance->status }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">No attendance records.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Recent Tasks & Leaves</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6>Tasks</h6>
                        @forelse($recentTasks as $task)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="fw-semibold">{{ $task->title }}</div>
                                <div class="small text-muted">{{ $task->employee?->name }} · {{ $task->status }}</div>
                            </div>
                        @empty
                            <p class="text-muted">No tasks.</p>
                        @endforelse
                    </div>
                    <div class="col-md-6">
                        <h6>Leaves</h6>
                        @forelse($recentLeaves as $leave)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="fw-semibold">{{ $leave->employee?->name ?? $leave->user?->name }}</div>
                                <div class="small text-muted">{{ $leave->leave_type }} · {{ $leave->status }}</div>
                            </div>
                        @empty
                            <p class="text-muted">No leave requests.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
