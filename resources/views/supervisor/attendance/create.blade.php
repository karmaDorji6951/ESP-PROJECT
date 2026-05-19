@extends('layouts.app')

@section('title', 'Mark Attendance')
@section('page_title', 'Mark Attendance')

@section('content')
@php
    $isWeekend = ($date ?? today())->isWeekend();
    $activeTab = request('tab', 'all');
@endphp

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div>
        <h1 class="h3 mb-1">Mark Attendance</h1>
        <p class="text-muted mb-0">Mark daily attendance for employees (weekdays only)</p>
    </div>
    <button type="button" class="btn btn-warning" id="markAllPresentBtn" @disabled($isWeekend)>
        <i class="bi bi-check2-square"></i> Mark All Present
    </button>
</div>

@if($isWeekend)
    <div class="alert alert-info">
        Weekend (Saturday/Sunday) is not a working day. Attendance can be marked only Monday to Friday.
    </div>
@endif

<div class="nav nav-pills gap-2 mb-3" role="tablist">
    <a class="nav-link {{ $activeTab === 'all' ? 'active' : '' }}" href="{{ route('supervisor.attendance.create', ['attendance_date' => optional($date ?? null)->format('Y-m-d'), 'tab' => 'all']) }}">All</a>
    <a class="nav-link {{ $activeTab === 'present' ? 'active' : '' }}" href="{{ route('supervisor.attendance.create', ['attendance_date' => optional($date ?? null)->format('Y-m-d'), 'tab' => 'present']) }}">Present</a>
    <a class="nav-link {{ $activeTab === 'absent' ? 'active' : '' }}" href="{{ route('supervisor.attendance.create', ['attendance_date' => optional($date ?? null)->format('Y-m-d'), 'tab' => 'absent']) }}">Absent</a>
    <a class="nav-link {{ $activeTab === 'leave' ? 'active' : '' }}" href="{{ route('supervisor.attendance.create', ['attendance_date' => optional($date ?? null)->format('Y-m-d'), 'tab' => 'leave']) }}">On Leave</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="card card-soft h-100 border-start border-3 border-success">
            <div class="card-body">
                <div class="h4 mb-0 text-success" id="countPresent">{{ $counts['present'] ?? 0 }}</div>
                <div class="text-muted">Present</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card card-soft h-100 border-start border-3 border-danger">
            <div class="card-body">
                <div class="h4 mb-0 text-danger" id="countAbsent">{{ $counts['absent'] ?? 0 }}</div>
                <div class="text-muted">Absent</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card card-soft h-100 border-start border-3 border-info">
            <div class="card-body">
                <div class="h4 mb-0 text-info" id="countLeave">{{ $counts['leave'] ?? 0 }}</div>
                <div class="text-muted">On Leave</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card card-soft h-100">
            <div class="card-body">
                <div class="h4 mb-0" id="countAll">{{ ($counts['present'] ?? 0) + ($counts['absent'] ?? 0) + ($counts['leave'] ?? 0) }}</div>
                <div class="text-muted">Marked</div>
            </div>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="card-body">
        <form method="POST" action="{{ route('supervisor.attendance.store') }}" id="attendanceForm">
            @csrf

            <div class="row g-2 align-items-end mb-3">
                <div class="col-md-4">
                    <label class="form-label">Attendance Date</label>
                    <input type="date" name="attendance_date" value="{{ old('attendance_date', ($date ?? today())->format('Y-m-d')) }}" class="form-control @error('attendance_date') is-invalid @enderror" required @disabled($isWeekend)>
                    <div class="form-text">Attendance can be marked only Monday to Friday.</div>
                    @error('attendance_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8 text-md-end">
                    <a class="btn btn-outline-secondary" href="{{ route('supervisor.attendance.index') }}">View Records</a>
                    <button class="btn btn-primary" type="submit" @disabled($isWeekend)>
                        <i class="bi bi-save"></i> Save Attendance
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width:36px;"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                            <th>Staff Member</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th style="min-width:260px;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            @php
                                $existing = $todayRecords[$employee->id] ?? null;
                                $defaultStatus = old('statuses.' . $employee->id, $existing->status ?? 'Present');
                                $rowStatusKey = strtolower($defaultStatus === 'Leave' ? 'leave' : $defaultStatus);
                                $rowStatusKey = $rowStatusKey === 'present' ? 'present' : ($rowStatusKey === 'absent' ? 'absent' : 'leave');
                            @endphp
                            <tr class="attendance-row" data-status="{{ $rowStatusKey }}">
                                <td>
                                    <input type="checkbox" class="form-check-input row-check">
                                    <input type="hidden" name="employee_ids[]" value="{{ $employee->id }}">
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $employee->name }}</div>
                                    <div class="text-muted small">{{ $employee->role_title }}</div>
                                </td>
                                <td>{{ $employee->department ?? '-' }}</td>
                                <td style="min-width:160px;">
                                    <select name="statuses[{{ $employee->id }}]" class="form-select form-select-sm status-select" @disabled($isWeekend)>
                                        @foreach(['Present' => 'Present', 'Absent' => 'Absent', 'Leave' => 'On Leave'] as $value => $label)
                                            <option value="{{ $value }}" @selected($defaultStatus === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="remarks[{{ $employee->id }}]" value="{{ old('remarks.' . $employee->id, $existing->remarks ?? '') }}" class="form-control form-control-sm" placeholder="Remarks (optional)" @disabled($isWeekend)>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
(() => {
    const activeTab = @json($activeTab);
    const rows = Array.from(document.querySelectorAll('.attendance-row'));
    const selectAll = document.getElementById('selectAll');
    const markAllPresentBtn = document.getElementById('markAllPresentBtn');

    function normalizeStatus(value) {
        if (value === 'Leave') return 'leave';
        if (value === 'Absent') return 'absent';
        return 'present';
    }

    function applyTabFilter() {
        rows.forEach((row) => {
            const rowStatus = row.getAttribute('data-status');
            const show = (activeTab === 'all') || (rowStatus === activeTab);
            row.classList.toggle('d-none', !show);
        });
    }

    function updateCounts() {
        let present = 0, absent = 0, leave = 0;
        rows.forEach((row) => {
            const status = row.getAttribute('data-status');
            if (status === 'present') present++;
            else if (status === 'absent') absent++;
            else if (status === 'leave') leave++;
        });
        const elPresent = document.getElementById('countPresent');
        const elAbsent = document.getElementById('countAbsent');
        const elLeave = document.getElementById('countLeave');
        const elAll = document.getElementById('countAll');
        if (elPresent) elPresent.textContent = present;
        if (elAbsent) elAbsent.textContent = absent;
        if (elLeave) elLeave.textContent = leave;
        if (elAll) elAll.textContent = present + absent + leave;
    }

    rows.forEach((row) => {
        const select = row.querySelector('.status-select');
        if (!select) return;
        select.addEventListener('change', () => {
            row.setAttribute('data-status', normalizeStatus(select.value));
            applyTabFilter();
            updateCounts();
        });
    });

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            const checked = selectAll.checked;
            rows.forEach((row) => {
                if (row.classList.contains('d-none')) return;
                const box = row.querySelector('.row-check');
                if (box) box.checked = checked;
            });
        });
    }

    if (markAllPresentBtn) {
        markAllPresentBtn.addEventListener('click', () => {
            const checkedRows = rows.filter(r => (r.querySelector('.row-check')?.checked));
            const targetRows = checkedRows.length ? checkedRows : rows;
            targetRows.forEach((row) => {
                const select = row.querySelector('.status-select');
                if (!select) return;
                select.value = 'Present';
                row.setAttribute('data-status', 'present');
            });
            applyTabFilter();
            updateCounts();
        });
    }

    applyTabFilter();
    updateCounts();
})();
</script>
@endsection
