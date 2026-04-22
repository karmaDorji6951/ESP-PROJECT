@extends('layouts.app')

@section('title', 'Leaves')
@section('page_title', 'Leave Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('leaves.create') }}" class="btn btn-primary">Apply for Leave</a>
    <form method="GET" class="d-flex gap-2">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            @foreach(['Pending','Approved','Rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button class="btn btn-outline-primary">Filter</button>
    </form>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Requester</th><th>Leave Type</th><th>Period</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->employee?->name ?? $leave->user?->name }}</td>
                        <td>{{ $leave->leave_type }}</td>
                        <td>{{ $leave->start_date?->format('Y-m-d') }} to {{ $leave->end_date?->format('Y-m-d') }}</td>
                        <td><span class="badge bg-{{ $leave->status === 'Approved' ? 'success' : ($leave->status === 'Rejected' ? 'danger' : 'secondary') }}">{{ $leave->status }}</span></td>
                        <td class="d-flex flex-wrap gap-2">
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
