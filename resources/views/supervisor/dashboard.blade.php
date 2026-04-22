@extends('layouts.app')

@section('title', 'Supervisor Dashboard')
@section('page_title', 'Supervisor Dashboard')

@section('content')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>Supervisor Panel</strong> - Manage staff, attendance, tasks, and leave requests.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Team Overview -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Total Staff</div>
                <div class="fs-2 fw-bold text-primary">{{ $summary['total_staff'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Total Employees</div>
                <div class="fs-2 fw-bold text-info">{{ $summary['total_employees'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Active Employees</div>
                <div class="fs-2 fw-bold text-success">{{ $summary['active_employees'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Present Today</div>
                <div class="fs-2 fw-bold text-success">{{ $summary['present_today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Absent Today</div>
                <div class="fs-2 fw-bold text-danger">{{ $summary['absent_today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">On Leave Today</div>
                <div class="fs-2 fw-bold text-warning">{{ $summary['on_leave_today'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Pending Leaves</div>
                <div class="fs-2 fw-bold text-warning">{{ $summary['pending_leaves'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Task & Activities Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Pending Tasks</div>
                <div class="fs-2 fw-bold text-danger">{{ $summary['pending_tasks'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">In Progress</div>
                <div class="fs-2 fw-bold text-info">{{ $summary['in_progress_tasks'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Staff & Management Tables -->
<div class="row g-4">
    <!-- Staff Members -->
    <div class="col-lg-4">
        <div class="card card-soft h-100">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Your Staff</span>
                <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffUsers as $staff)
                            <tr>
                                <td>{{ $staff->name }}</td>
                                <td><small>{{ $staff->email }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">No staff members.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Recent Attendance</span>
                <a href="{{ route('supervisor.attendance.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->employee?->name }}</td>
                                <td>{{ $attendance->attendance_date?->format('Y-m-d') }}</td>
                                <td><span class="badge {{ $attendance->status === 'Present' ? 'bg-success' : ($attendance->status === 'Absent' ? 'bg-danger' : 'bg-warning') }}">{{ $attendance->status }}</span></td>
                                <td><small>{{ $attendance->remarks ?? '-' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No attendance records.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Assigned Tasks -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Assigned Tasks</span>
                <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Employee</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->employee?->name }}</td>
                                <td><span class="badge {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'Pending' ? 'bg-danger' : 'bg-info') }}">{{ $task->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No tasks.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pending Leave Requests -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Pending Leave Requests</span>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Type</th>
                            <th>Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeaves as $leave)
                            <tr>
                                <td>{{ $leave->employee?->name ?? $leave->user?->name }}</td>
                                <td><small>{{ $leave->leave_type ?? 'N/A' }}</small></td>
                                <td><small>{{ $leave->from_date?->format('m/d') ?? 'N/A' }} - {{ $leave->to_date?->format('m/d') ?? 'N/A' }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No pending requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Employees List -->
    <div class="col-lg-12">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Employees Overview</span>
                <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>CID</th>
                            <th>Role Title</th>
                            <th>Joining Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEmployees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->cid }}</td>
                                <td>{{ $employee->role_title }}</td>
                                <td>{{ $employee->joining_date?->format('Y-m-d') }}</td>
                                <td><span class="badge {{ $employee->status === 'Active' ? 'bg-success' : 'bg-danger' }}">{{ $employee->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No employees.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
