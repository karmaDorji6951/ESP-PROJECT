@extends('layouts.app')

@section('title', 'Leaves')
@section('page_title', 'Leave Management')

@section('content')
<div class="app-page-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="app-page-hero-kicker mb-2">Workspace</div>
            <h1 class="app-page-hero-title mb-2">Leave Management</h1>
            <p class="app-page-hero-subtitle">Apply for leave and track approvals.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('leaves.create') }}" class="btn btn-light app-page-hero-action">Apply for Leave</a>
        </div>
    </div>
</div>

<form method="GET" class="d-flex flex-wrap gap-2 mb-3">
    <select name="status" class="form-select" style="max-width: 220px;">
        <option value="">All Status</option>
        @foreach(['Pending','Approved','Rejected'] as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
        @endforeach
    </select>
    <button class="btn btn-outline-primary">Filter</button>
</form>

    <div class="card card-soft">
    <div class="table-responsive">
        <table class="table align-middle mb-0 stackable-table">
            <thead>
                <tr>
                    <th>Requester</th><th>Leave Type</th><th>Period</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td data-label="Requester">{{ $leave->employee?->name ?? $leave->user?->name }}</td>
                        <td data-label="Leave Type">{{ $leave->leave_type }}</td>
                        <td data-label="Period">{{ $leave->start_date?->format('Y-m-d') }} to {{ $leave->end_date?->format('Y-m-d') }}</td>
                        <td data-label="Status"><span class="badge bg-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Rejected' ? 'danger' : 'secondary') }}">{{ $leave->status }}</span></td>
                        <td data-label="Actions" class="d-flex flex-wrap gap-2">
                            @if(auth()->user()->role?->slug !== 'staff' && $leave->status === 'Pending')
                                <form method="POST" action="{{ route('leaves.approve', $leave) }}">@csrf<button class="btn btn-sm btn-success">Approve</button></form>
                                <form method="POST" action="{{ route('leaves.reject', $leave) }}">@csrf<button class="btn btn-sm btn-danger">Reject</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted p-4">No leave requests.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $leaves->links() }}</div>
@endsection
