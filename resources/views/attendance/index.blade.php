@extends('layouts.app')

@section('title', 'Attendance')
@section('page_title', 'Attendance Management')

@section('content')
@php
    $isWeekend = $date->isWeekend();
@endphp

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card card-soft mb-4">
            <div class="card-header bg-white fw-semibold">Mark Daily Attendance</div>
            <div class="card-body">
                @if($isWeekend)
                    <div class="alert alert-info mb-3">
                        Weekend (Saturday/Sunday) is not a working day. Attendance can be marked only Monday to Friday.
                    </div>
                @endif
                <form method="POST" action="{{ route('attendance.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Attendance Date</label>
                        <input type="date" name="attendance_date" value="{{ $date->format('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div class="table-responsive" style="max-height: 520px; overflow:auto;">
                        <table class="table table-sm align-middle">
                            <thead><tr><th>Employee</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="employee_ids[]" value="{{ $employee->id }}">
                                            <div class="fw-semibold">{{ $employee->name }}</div>
                                            <small class="text-muted">{{ $employee->role_title }}</small>
                                        </td>
                                        <td>
                                            <select name="statuses[{{ $employee->id }}]" class="form-select form-select-sm">
                                                @foreach(['Present', 'Absent', 'Leave'] as $status)
                                                    <option value="{{ $status }}" @selected((optional($todayRecords[$employee->id] ?? null)->status ?? 'Present') === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="text" name="remarks[{{ $employee->id }}]" value="{{ optional($todayRecords[$employee->id] ?? null)->remarks ?? '' }}" class="form-control form-control-sm" placeholder="Remarks (optional)">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary mt-3" @disabled($isWeekend)>Save Attendance</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Attendance Records</div>
            <div class="card-body">
                <form class="row g-2 mb-3" method="GET">
                    <div class="col-md-5">
                        <input type="date" name="attendance_date" value="{{ request('attendance_date') }}" class="form-control" placeholder="Filter by date">
                    </div>
                    <div class="col-md-5">
                        <select name="employee_id" class="form-select">
                            <option value="">All employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" @selected($employeeId == $employee->id)>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100">Filter</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Employee</th><th>Date</th><th>Status</th><th>Remarks</th><th>Marked By</th></tr></thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->employee?->name }}</td>
                                    <td>{{ $record->attendance_date?->format('Y-m-d') }}</td>
                                    <td>{{ $record->status }}</td>
                                    <td>{{ $record->remarks }}</td>
                                    <td>{{ $record->marker?->name }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No attendance records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
