@extends('layouts.app')

@section('title', 'Attendance Records')
@section('page_title', 'Attendance Records')

@section('content')
<div class="app-page-hero d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <div class="app-page-hero-kicker mb-2">Supervisor Workspace</div>
        <h1 class="app-page-hero-title mb-2">Attendance Records</h1>
        <p class="app-page-hero-subtitle">Track daily attendance entries for your team.</p>
    </div>
    @if($todayAttendanceMarked ?? false)
        <button type="button" class="btn btn-light app-page-hero-action" disabled>
            <i class="bi bi-check2-circle"></i> Attendance Marked Today
        </button>
    @else
        <a href="{{ route('supervisor.attendance.create') }}" class="btn btn-light app-page-hero-action">
            <i class="bi bi-plus-circle"></i> Mark Attendance
        </a>
    @endif
</div>

@if($todayAttendanceMarked ?? false)
    <div class="alert alert-info">
        Attendance has already been marked for today. A supervisor cannot mark attendance twice for the same day.
    </div>
@endif

<div class="nav nav-pills gap-2 mb-3" role="tablist">
    <a class="nav-link {{ empty($status) ? 'active' : '' }}" href="{{ route('supervisor.attendance.index', array_merge(request()->except('status', 'page'), [])) }}">All</a>
    <a class="nav-link {{ ($status ?? null) === 'Present' ? 'active' : '' }}" href="{{ route('supervisor.attendance.index', array_merge(request()->except('page'), ['status' => 'Present'])) }}">Present</a>
    <a class="nav-link {{ ($status ?? null) === 'Absent' ? 'active' : '' }}" href="{{ route('supervisor.attendance.index', array_merge(request()->except('page'), ['status' => 'Absent'])) }}">Absent</a>
    <a class="nav-link {{ ($status ?? null) === 'Leave' ? 'active' : '' }}" href="{{ route('supervisor.attendance.index', array_merge(request()->except('page'), ['status' => 'Leave'])) }}">On Leave</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-4 col-md-4">
        <div class="card card-soft h-100 border-start border-3 border-success">
            <div class="card-body">
                <div class="h4 mb-0 text-success">{{ $summary['present'] ?? 0 }}</div>
                <div class="text-muted">Present</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="card card-soft h-100 border-start border-3 border-danger">
            <div class="card-body">
                <div class="h4 mb-0 text-danger">{{ $summary['absent'] ?? 0 }}</div>
                <div class="text-muted">Absent</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4">
        <div class="card card-soft h-100 border-start border-3 border-info">
            <div class="card-body">
                <div class="h4 mb-0 text-info">{{ $summary['leave'] ?? 0 }}</div>
                <div class="text-muted">On Leave</div>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="card-body">
        <form class="row g-2 mb-3" method="GET">
            <div class="col-md-3">
                <input type="date" name="from_date" value="{{ request('from_date', optional($fromDate ?? null)->format('Y-m-d')) }}" class="form-control" placeholder="From">
            </div>
            <div class="col-md-3">
                <input type="date" name="to_date" value="{{ request('to_date', optional($toDate ?? null)->format('Y-m-d')) }}" class="form-control" placeholder="To">
            </div>
            <div class="col-md-4">
                <select name="employee_id" class="form-select">
                    <option value="">All employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" @selected($employeeId == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline-primary w-100">Filter</button>
                <a href="{{ route('supervisor.attendance.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
            @if(!empty($status))
                <input type="hidden" name="status" value="{{ $status }}">
            @endif
        </form>
        <div class="text-muted small">
            Showing attendance for the selected range (defaults to the past 1 month).
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <strong>{{ $attendance->employee->name }}</strong>
                            </td>
                            <td>{{ $attendance->attendance_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Absent' ? 'danger' : 'info') }}">
                                    {{ $attendance->status }}
                                </span>
                            </td>
                            <td>{{ $attendance->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top" style="border-color: var(--border-color) !important; background: #f5f1e8;">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
