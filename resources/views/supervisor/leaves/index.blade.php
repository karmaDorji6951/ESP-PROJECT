@extends('layouts.app')

@section('title', 'Leave Requests')
@section('page_title', 'Leave Requests Management')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pending Leave Requests</h5>
            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td class="fw-semibold">{{ $leave->employee?->name ?? $leave->user?->name }}</td>
                        <td>
                            <span class="badge bg-info">{{ $leave->leave_type }}</span>
                        </td>
                        <td>{{ $leave->start_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $leave->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            @if($leave->start_date && $leave->end_date)
                                {{ $leave->start_date->diffInDays($leave->end_date) + 1 }} days
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($leave->reason, 50) }}</small>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('supervisor.leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="mb-2">📋 No pending leave requests</div>
                            <small>All leave requests have been processed</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($leaves->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $leaves->links() }}
</div>
@endif

@endsection
