@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}!</h2>
        <p class="text-muted mb-0">Here's what's happening with your ESP management system today.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>Add User
        </a>
        <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-person-badge me-2"></i>Manage Employees
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-people text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Users</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['total_users'] }}</h3>
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
                            <i class="bi bi-shield-check text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Supervisors</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['total_supervisors'] }}</h3>
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
                            <i class="bi bi-person-workspace text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Staff</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['total_staff'] }}</h3>
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
                            <i class="bi bi-building text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Employees</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['total_employees'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Operational Summary -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-person-check text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Active Employees</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['active_employees'] }}</h3>
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
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Present Today</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $summary['present_today'] }}</h3>
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
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock-history text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending Tasks</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $summary['pending_tasks'] }}</h3>
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
                            <i class="bi bi-calendar-x text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending Leaves</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $summary['pending_leaves'] }}</h3>
                    </div>
                </div>
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
                <a href="{{ route('admin.leaves.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
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
                                <td>{{ $leave->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                <td>{{ $leave->end_date?->format('Y-m-d') ?? 'N/A' }}</td>
                                <td><span class="badge bg-warning">{{ $leave->status }}</span></td>
                                <td>
                                    <a href="{{ route('admin.leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">Review</a>
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
