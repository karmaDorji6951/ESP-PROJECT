@extends('layouts.app')

@section('title', 'My Leave Requests')
@section('page_title', 'My Leave Requests')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All My Leave Requests</h5>
            <a href="{{ route('staff.leaves.create') }}" class="btn btn-success btn-sm">Request New Leave</a>
        </div>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Leave Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Requested On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td class="fw-semibold">{{ $leave->leave_type }}</td>
                        <td>{{ $leave->start_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $leave->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            @if($leave->start_date && $leave->end_date)
                                {{ $leave->start_date->diffInDays($leave->end_date) + 1 }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ 
                                $leave->status === 'Approved' ? 'bg-success' : 
                                ($leave->status === 'Rejected' ? 'bg-danger' : 'bg-warning') 
                            }}">
                                {{ $leave->status }}
                            </span>
                        </td>
                        <td><small>{{ Str::limit($leave->reason, 40) ?? '-' }}</small></td>
                        <td><small>{{ $leave->created_at?->format('Y-m-d') ?? '-' }}</small></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="mb-2">📭 No leave requests yet</div>
                            <small><a href="{{ route('staff.leaves.create') }}">Click here to request a leave</a></small>
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
