@extends('layouts.app')

@section('title', 'Supervisor Dashboard')
@section('page_title', 'Supervisor Dashboard')

@push('styles')
<style>
    .dashboard-hero {
        background: linear-gradient(135deg, #0F2044 0%, #173b6b 60%, #1D9E75 160%);
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
    }

    .stat-card .card-body {
        padding: 18px;
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
</style>
@endpush

@section('content')
<div class="dashboard-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="text-uppercase fw-semibold mb-2" style="letter-spacing: 0.14em; opacity: 0.85; font-size: 12px;">Supervisor Control Center</div>
            <h2 class="fw-bold mb-2">Manage the team with a clear, calm overview.</h2>
            <p class="mb-0" style="max-width: 720px; opacity: 0.92;">Monitor staffing, attendance, tasks, and leave requests from one focused workspace.</p>
        </div>
    </div>
</div>

<!-- Team Overview -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(29, 158, 117, 0.12); color: #1D9E75;"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Total Staff</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['total_staff'] }}</div>
                        <div class="text-muted small">Team members in scope</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(15, 32, 68, 0.08); color: #0F2044;"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Total Employees</div>
                        <div class="fs-3 fw-bold mb-0">{{ $summary['total_employees'] }}</div>
                        <div class="text-muted small">Registered workforce</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
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
</div>

<!-- Attendance Summary -->
<div class="row g-4 mb-4 justify-content-center">
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
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;"><i class="bi bi-calendar-x"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Absent Today</div>
                        <div class="fs-3 fw-bold mb-0 text-danger">{{ $summary['absent_today'] }}</div>
                        <div class="text-muted small">Needs follow-up</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #D97706;"><i class="bi bi-calendar-range"></i></div>
                    <div>
                        <div class="text-muted small mb-1">On Leave</div>
                        <div class="fs-3 fw-bold mb-0 text-warning">{{ $summary['on_leave_today'] ?? 0 }}</div>
                        <div class="text-muted small">Currently away</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: rgba(15, 32, 68, 0.08); color: #0F2044;"><i class="bi bi-list-task"></i></div>
                    <div>
                        <div class="text-muted small mb-1">Pending Tasks</div>
                        <div class="fs-3 fw-bold mb-0 text-info">{{ $summary['pending_tasks'] }}</div>
                        <div class="text-muted small">Requires action</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task & Activities Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card section-card h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">Pending Tasks</div>
                <div class="fs-2 fw-bold text-danger">{{ $summary['pending_tasks'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card section-card h-100">
            <div class="card-body text-center">
                <div class="text-muted mb-2">In Progress</div>
                <div class="fs-2 fw-bold text-info">{{ $summary['in_progress_tasks'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Staff & Management Tables -->
<div class="row g-4">
    <!-- Assigned Tasks -->
    <div class="col-lg-12">
        <div class="card section-card">
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
                                <td>{{ $task->employee?->name ?? 'Unknown' }}</td>
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

    <!-- Employees List -->
    <div class="col-lg-12">
        <div class="card section-card">
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

<!-- Evaluations moved to dedicated page or task action; embedded widget removed -->
@endsection
