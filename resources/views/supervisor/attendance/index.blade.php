@extends('layouts.app')

@section('title', 'Attendance Records')
@section('page_title', 'Attendance Records')

@section('content')
<div class="mb-4">
    <a href="{{ route('supervisor.attendance.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Mark Attendance
    </a>
</div>

<div class="card card-soft">
    <div class="card-body">
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
                                <span class="badge bg-{{ $attendance->status === 'Present' ? 'success' : ($attendance->status === 'Absent' ? 'danger' : 'warning') }}">
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

        <div class="mt-3">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
