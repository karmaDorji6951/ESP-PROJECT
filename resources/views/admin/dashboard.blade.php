@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>Admin Panel</strong> - Manage all users, employees, and system operations.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- User Management Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Total Users</div>
                <div class="fs-2 fw-bold text-primary">{{ $summary['total_users'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Supervisors</div>
                <div class="fs-2 fw-bold text-info">{{ $summary['total_supervisors'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Staff</div>
                <div class="fs-2 fw-bold text-warning">{{ $summary['total_staff'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Total Employees</div>
                <div class="fs-2 fw-bold text-success">{{ $summary['total_employees'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Operational Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-soft h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Active Employees</div>
                <div class="fs-2 fw-bold">{{ $summary['active_employees'] }}</div>
            </div>
        </div>
    </div>
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
                <div class="text-muted mb-2">Pending Tasks</div>
                <div class="fs-2 fw-bold text-danger">{{ $summary['pending_tasks'] }}</div>
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

<!-- Management Tables -->
<div class="row g-4">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Recent Users</span>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-info">{{ $user->role?->name ?? 'N/A' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Employees -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Recent Employees</span>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>CID</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEmployees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->cid }}</td>
                                <td><span class="badge {{ $employee->status === 'Active' ? 'bg-success' : 'bg-danger' }}">{{ $employee->status }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Recent Attendance</span>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAttendance as $attendance)
                            <tr>
                                <td>{{ $attendance->employee?->name }}</td>
                                <td>{{ $attendance->attendance_date?->format('Y-m-d') }}</td>
                                <td><span class="badge {{ $attendance->status === 'Present' ? 'bg-success' : ($attendance->status === 'Absent' ? 'bg-danger' : 'bg-warning') }}">{{ $attendance->status }}</span></td>
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

    <!-- Recent Tasks -->
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Recent Tasks</span>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
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
                                <td colspan="3" class="text-center text-muted py-3">No tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Leave Requests -->
    <div class="col-lg-12">
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
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeaves as $leave)
                            <tr>
                                <td>{{ $leave->employee?->name ?? $leave->user?->name }}</td>
                                <td>{{ $leave->leave_type ?? 'N/A' }}</td>
                                <td>{{ $leave->from_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                <td>{{ $leave->to_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                <td><span class="badge bg-warning">{{ $leave->status }}</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No pending leave requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
