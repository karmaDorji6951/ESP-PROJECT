@extends('layouts.app')

@section('title', 'My Leave Requests')
@section('page_title', 'My Leave Requests')

@section('content')
<div class="leave-list-page container-fluid py-3 py-lg-4">
    <div class="leave-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase fw-semibold mb-2 leave-hero-kicker">Leave Requests</div>
                <h1 class="leave-hero-title mb-2">My Leave Requests</h1>
                <p class="leave-hero-subtitle mb-0">A clean overview of all leave requests, their working-day counts, and approval status.</p>
            </div>
            <a href="{{ route('staff.leaves.create') }}" class="btn btn-light leave-back-btn">Request New Leave</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Approved</div>
                    <div class="metric-value text-success">{{ $leaves->where('status', 'Approved')->count() }}</div>
                    <div class="metric-meta">completed requests</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Pending</div>
                    <div class="metric-value text-warning">{{ $leaves->where('status', 'Pending')->count() }}</div>
                    <div class="metric-meta">awaiting review</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Cancelled</div>
                    <div class="metric-value text-secondary">{{ $leaves->where('status', 'Cancelled')->count() }}</div>
                    <div class="metric-meta">withdrawn requests</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm section-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="section-title mb-1">History</div>
                <div class="text-muted small">Recent leave requests and their working-day totals.</div>
            </div>
            <span class="badge rounded-pill text-bg-light text-dark">{{ $leaves->count() }} shown</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 leave-table">
                <thead class="table-light">
                    <tr>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Requested On</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $leave->leave_type }}</div>
                            </td>
                            <td>{{ $leave->start_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ $leave->end_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>
                                <span class="days-pill">
                                    @if($leave->start_date && $leave->end_date)
                                        {{ \App\Models\LeaveRequest::workingDaysBetween($leave->start_date, $leave->end_date) }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="badge {{
                                    $leave->status === 'Approved' ? 'bg-success' :
                                    ($leave->status === 'Rejected' ? 'bg-danger' :
                                    ($leave->status === 'Cancelled' ? 'bg-secondary' : 'bg-warning'))
                                }}">
                                    {{ $leave->status }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($leave->reason, 52) ?? '-' }}</small></td>
                            <td><small class="text-muted">{{ $leave->created_at?->format('Y-m-d') ?? '-' }}</small></td>
                            <td class="text-end">
                                @if($leave->status === 'Pending')
                                    <form method="POST" action="{{ route('staff.leaves.destroy', $leave) }}" class="d-inline" onsubmit="return confirm('Cancel this pending leave request?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                    </form>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <div class="mb-2">No leave requests yet</div>
                                <small><a href="{{ route('staff.leaves.create') }}">Request your first leave</a></small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($leaves->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $leaves->links() }}
        </div>
    @endif
</div>

<style>
.leave-list-page {
    max-width: 1320px;
}

.leave-hero {
    background: linear-gradient(135deg, #0F2044 0%, #16345f 58%, #1D9E75 155%);
    color: #fff;
    border-radius: 20px;
    padding: 24px 28px;
    box-shadow: 0 18px 40px rgba(15, 32, 68, 0.18);
    overflow: hidden;
    position: relative;
}

.leave-hero::after {
    content: '';
    position: absolute;
    inset: auto -100px -100px auto;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
}

.leave-hero-kicker {
    letter-spacing: 0.14em;
    opacity: 0.85;
    font-size: 12px;
}

.leave-hero-title {
    font-weight: 800;
    font-size: clamp(1.8rem, 3vw, 2.5rem);
}

.leave-hero-subtitle {
    max-width: 760px;
    color: rgba(255, 255, 255, 0.9);
}

.leave-back-btn {
    border-radius: 999px;
    padding-inline: 18px;
    font-weight: 600;
    color: #0f2044;
    position: relative;
    z-index: 1;
}

.leave-back-btn:hover {
    color: #0f2044;
}

.metric-card {
    border-radius: 16px;
    background: #fcfbf5;
}

.metric-label {
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 700;
    margin-bottom: 6px;
}

.metric-value {
    font-size: clamp(1.6rem, 2.2vw, 2.2rem);
    font-weight: 800;
    line-height: 1;
    color: #111827;
}

.metric-meta {
    margin-top: 4px;
    color: #6b7280;
}

.section-card {
    border-radius: 18px;
    overflow: hidden;
}

.section-title {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #6b7280;
}

.leave-table thead th {
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
    border-bottom: 1px solid rgba(15, 32, 68, 0.08);
}

.leave-table tbody td {
    padding-top: 16px;
    padding-bottom: 16px;
}

.days-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    background: #eef2ff;
    color: #1e3a8a;
    font-weight: 700;
    min-width: 40px;
    justify-content: center;
}

@media (max-width: 991.98px) {
    .leave-hero {
        padding: 20px;
    }
}
</style>

@endsection
