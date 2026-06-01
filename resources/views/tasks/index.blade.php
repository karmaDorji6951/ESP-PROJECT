@extends('layouts.app')

@section('title', 'ESP Timetable')
@section('page_title', 'ESP Work Timetable')

@push('styles')
<style>
    .timetable-wrapper {
        overflow: auto;
        max-height: 72vh;
    }
    .timetable-table {
        min-width: 1200px;
    }
    .timetable-table .sticky-col {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 2;
        min-width: 220px;
    }
    .timetable-table tbody .sticky-col {
        background: #f8fafc;
    }
    .day-cell {
        min-width: 130px;
        vertical-align: top;
    }
    .assignment-chip {
        border-left: 3px solid #0d6efd;
        background: #eef4ff;
        padding: .4rem .45rem;
        border-radius: .35rem;
        margin-bottom: .4rem;
        font-size: .8rem;
        line-height: 1.2;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h6 class="mb-1">{{ $monthLabel }}</h6>
        <small class="text-muted">{{ $canManage ? 'Supervisor/Admin view' : 'My timetable view' }}</small>
    </div>
    @if($canManage)
        <a href="{{ route('timetables.create') }}" class="btn btn-primary">Assign Work</a>
    @endif
</div>

<form method="GET" action="{{ route('tasks.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
        <label class="form-label">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="form-control">
    </div>
    @if($canManage)
        <div class="col-md-4">
            <label class="form-label">ESP</label>
            <select name="employee_id" class="form-select">
                <option value="">All ESP</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
    <div class="col-md-2 align-self-end">
        <button class="btn btn-outline-primary w-100">Apply</button>
    </div>
</form>

<div class="card card-soft">
    <div class="timetable-wrapper">
        <table class="table table-bordered align-middle mb-0 timetable-table">
            <thead>
                <tr>
                    <th class="sticky-col">ESP</th>
                    @foreach($days as $day)
                        <th class="text-center">{{ $day->format('d D') }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td class="sticky-col">
                            <div class="fw-semibold">{{ $employee->name }}</div>
                            <small class="text-muted">{{ $employee->role_title }}</small>
                        </td>
                        @foreach($days as $day)
                            @php
                                $dayAssignments = $tasks->filter(function ($task) use ($employee, $day) {
                                    return $task->employee_id === $employee->id
                                        && $task->schedule_start_date
                                        && $task->schedule_end_date
                                        && $task->schedule_start_date->lte($day)
                                        && $task->schedule_end_date->gte($day);
                                });
                            @endphp
                            <td class="day-cell">
                                @foreach($dayAssignments as $task)
                                    <div class="assignment-chip">
                                        <div class="fw-semibold">{{ $task->title }}</div>
                                        <div>{{ $task->status }}</div>
                                        @if($day->isSameDay($task->schedule_start_date))
                                            <small>Start</small>
                                        @endif
                                        @if($day->isSameDay($task->schedule_end_date) && !$day->isSameDay($task->schedule_start_date))
                                            <small>End</small>
                                        @endif
                                        @if($canManage)
                                            <div class="mt-1 d-flex gap-1">
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-primary py-0 px-1">Edit</a>
                                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this assignment?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger py-0 px-1">X</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="32" class="text-center text-muted p-4">No timetable records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
