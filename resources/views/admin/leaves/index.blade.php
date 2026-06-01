@extends('layouts.app')

@section('title', 'Leave Requests Management')
@section('page_title', 'Leave Requests Management')

@section('content')
<div class="app-page-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="app-page-hero-kicker mb-2">Admin Workspace</div>
            <h1 class="app-page-hero-title mb-2">Leave Requests</h1>
            <p class="app-page-hero-subtitle">Review staff leave requests and track approval status.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-light app-page-hero-action">Back to Dashboard</a>
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
                    <th>Status</th>
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
                            <span class="badge {{ 
                                $leave->status === 'Approved' ? 'bg-success' : 
                                ($leave->status === 'Rejected' ? 'bg-danger' : 'bg-warning') 
                            }}">{{ $leave->status }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.leaves.show', $leave) }}" class="btn btn-sm btn-outline-primary">Review</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <div class="mb-2">📋 No leave requests found</div>
                            <small>No leave requests have been submitted yet</small>
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
