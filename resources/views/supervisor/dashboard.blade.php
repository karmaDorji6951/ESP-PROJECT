@extends('layouts.app')

@section('title', 'Supervisor Dashboard')
@section('page_title', 'Supervisor Dashboard')

@section('content')
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>Supervisor Panel</strong> - Manage staff, attendance, tasks, and leave requests.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Team Overview -->
<div class="row g-4 mb-4 justify-content-center">
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-people text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total Staff</h6>
                        <h3 class="fw-bold mb-0">{{ $summary['total_staff'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-building text-info fs-4"></i>
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
    <div class="col-md-4 col-sm-6">
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
</div>

<!-- Attendance Summary -->
<div class="row g-4 mb-4 justify-content-center">
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
                            <i class="bi bi-calendar-x text-danger fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Absent Today</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $summary['absent_today'] }}</h3>
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
                            <i class="bi bi-calendar-range text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">On Leave</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $summary['on_leave_today'] ?? 0 }}</h3>
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
                            <i class="bi bi-list-task text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Pending Tasks</h6>
                        <h3 class="fw-bold mb-0 text-info">{{ $summary['pending_tasks'] }}</h3>
                    </div>
                </div>
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
    <!-- Assigned Tasks -->
    <div class="col-lg-12">
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
