@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Dashboard')

@push('styles')
<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #0F2044 0%, #12325f 55%, #1D9E75 160%);
        color: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 18px 40px rgba(15, 32, 68, 0.18);
        position: relative;
        overflow: hidden;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: auto -80px -80px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .dashboard-hero h2,
    .dashboard-hero p {
        color: #fff;
        position: relative;
        z-index: 1;
    }

    }

    .section-card {
        border: 1px solid rgba(15, 32, 68, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 32, 68, 0.05);
        overflow: hidden;
    }

    .section-card .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(15, 32, 68, 0.08);
        padding: 16px 20px;
    }

    .section-card .table thead th {
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.04em;
        color: #526173;
        border-bottom: 1px solid rgba(15, 32, 68, 0.08);
    }
    .user-avatar { width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #1D9E75 0%, #60a5fa 100%); display: grid; place-items: center; color: #ffffff; font-weight: 700; font-size: 14px; }

</style>
@endpush

@section('content')
<div class="dashboard-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="text-uppercase fw-semibold mb-2" style="letter-spacing: 0.14em; opacity: 0.85; font-size: 12px;">ESP Management Overview</div>
            <h2 class="fw-bold mb-2">Welcome back, {{ auth()->user()->name }}.</h2>
            <p class="mb-0" style="max-width: 720px; opacity: 0.92;">Track the current state of users, employees, attendance, tasks, and leave requests from one clear command center.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 position-relative" style="z-index:1;">
            <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                <i class="bi bi-person-plus me-2"></i>Add User
            </a>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-light">
                <i class="bi bi-person-badge me-2"></i>Manage Employees
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Total Users</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['total_users'] }}</div>
                        <div class="text-muted small">All system accounts</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(15, 32, 68, 0.08); color: #0F2044;"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Supervisors</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['total_supervisors'] }}</div>
                        <div class="text-muted small">Active management team</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #D97706;"><i class="bi bi-person-workspace"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Staff</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['total_staff'] }}</div>
                        <div class="text-muted small">Field personnel</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Total Employees</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['total_employees'] }}</div>
                        <div class="text-muted small">Registered workforce</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Operational Summary -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-person-check"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Active Employees</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['active_employees'] }}</div>
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
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Present Today</div>
                        <div class="fs-3 fw-bold mb-0 text-success">{{ $summary['present_today'] }}</div>
                        <div class="text-muted small">Attendance snapshot</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(15, 32, 68, 0.08); color: #0F2044;"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Pending Tasks</div>
                        <div class="fs-3 fw-bold mb-0 text-danger">{{ $summary['pending_tasks'] }}</div>
                        <div class="text-muted small">Needs attention</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #D97706;"><i class="bi bi-calendar-x"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Pending Leaves</div>
                        <div class="fs-3 fw-bold mb-0 text-warning">{{ $summary['pending_leaves'] }}</div>
                        <div class="text-muted small">Awaiting review</div>
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
        <div class="card section-card">
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
        <div class="card section-card">
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
    <!-- Recent Leave Requests -->
    <div class="col-lg-12">
        <div class="card section-card">
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
