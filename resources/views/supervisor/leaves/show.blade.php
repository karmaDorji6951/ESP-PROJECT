@extends('layouts.app')

@section('title', 'Leave Request Details')
@section('page_title', 'Leave Request Details')

@section('content')
<div class="leave-detail-page container-fluid py-3 py-lg-4">
    <div class="leave-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase fw-semibold mb-2 leave-hero-kicker">Leave Request</div>
                <h1 class="leave-hero-title mb-2">Leave Request Details</h1>
                <p class="leave-hero-subtitle mb-0">Review the staff leave details, balance context, and decision history in one place.</p>
            </div>
            <a href="{{ route('supervisor.leaves.index') }}" class="btn btn-light leave-back-btn">Back to Leave Requests</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Employee</div>
                    <div class="metric-value metric-value-sm">{{ $leaf->employee?->name ?? $leaf->user?->name }}</div>
                    <div class="metric-meta">request owner</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Duration</div>
                    <div class="metric-value">{{ $leaf->start_date && $leaf->end_date ? \App\Models\LeaveRequest::workingDaysBetween($leaf->start_date, $leaf->end_date) : 0 }}</div>
                    <div class="metric-meta">working days</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Status</div>
                    <div class="metric-value {{ $leaf->status === 'Approved' ? 'text-success' : ($leaf->status === 'Rejected' ? 'text-danger' : 'text-warning') }}">{{ $leaf->status ?: 'Pending' }}</div>
                    <div class="metric-meta">current decision</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="metric-label">Submitted</div>
                    <div class="metric-value metric-value-sm">{{ $leaf->created_at?->format('d M') ?? '-' }}</div>
                    <div class="metric-meta">request date</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm section-card h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="section-title mb-3">Leave request overview</div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="detail-block">
                                <div class="detail-label">Employee</div>
                                <div class="detail-value">{{ $leaf->employee?->name ?? $leaf->user?->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-block">
                                <div class="detail-label">Leave Type</div>
                                <div class="detail-value"><span class="badge bg-info">{{ $leaf->leave_type }}</span></div>
                            </div>
                        </div>
                        @isset($balance)
                        <div class="col-12">
                            <div class="balance-box">
                                <div class="section-title mb-2">Leave balance</div>
                                <div class="row g-3 small">
                                    <div class="col-md-3">
                                        <div class="balance-item"><span>Allowance</span><strong>{{ $balance['allowance'] }} days</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="balance-item"><span>Used</span><strong class="text-success">{{ $balance['approved'] }} days</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="balance-item"><span>Pending</span><strong class="text-warning">{{ $balance['pending'] }} days</strong></div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="balance-item"><span>Remaining</span><strong class="text-danger">{{ $balance['remaining'] }} days</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endisset
                        <div class="col-md-6">
                            <div class="detail-block">
                                <div class="detail-label">Start Date</div>
                                <div class="detail-value">{{ $leaf->start_date?->format('Y-m-d') ?? 'Not specified' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-block">
                                <div class="detail-label">End Date</div>
                                <div class="detail-value">{{ $leaf->end_date?->format('Y-m-d') ?? 'Not specified' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-block">
                                <div class="detail-label">Reason</div>
                                <div class="detail-value text-muted">{{ $leaf->reason ?? 'No reason provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-block">
                                <div class="detail-label">Submitted On</div>
                                <div class="detail-value">{{ $leaf->created_at?->format('Y-m-d H:i') ?? 'Unknown' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($leaf->reviewed_at)
                        <div class="review-box mt-4">
                            <div class="section-title mb-3">Review history</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="detail-label">Reviewed By</div>
                                    <div class="detail-value">{{ $leaf->reviewer?->name ?? 'Unknown' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">Reviewed On</div>
                                    <div class="detail-value">{{ $leaf->reviewed_at?->format('Y-m-d H:i') }}</div>
                                </div>
                                @if($leaf->remarks)
                                    <div class="col-12">
                                        <div class="detail-label">Review Remarks</div>
                                        <div class="detail-value text-muted">{{ $leaf->remarks }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm section-card h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="section-title mb-3">Actions</div>
                    @if(! in_array($leaf->status, ['Approved', 'Rejected'], true))
                        <form method="POST" action="{{ route('supervisor.leaves.update', $leaf) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold">Decision</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">Select Action</option>
                                    <option value="Approved">Approve</option>
                                    <option value="Rejected">Reject</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label fw-semibold">Remarks (Optional)</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="4" placeholder="Add any comments..."></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Submit Decision</button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <strong>Already Processed</strong><br>
                            This leave request has been {{ strtolower($leaf->status) }}.
                        </div>

                        @if($leaf->remarks)
                            <div class="mt-3">
                                <h6 class="fw-semibold">Remarks:</h6>
                                <p class="text-muted">{{ $leaf->remarks }}</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.leave-detail-page {
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

.metric-value-sm {
    font-size: clamp(1.1rem, 1.8vw, 1.5rem);
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

.detail-block,
.balance-box,
.review-box {
    padding: 16px 18px;
    border-radius: 16px;
    border: 1px solid rgba(15, 32, 68, 0.08);
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
}

.detail-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
    margin-bottom: 6px;
}

.detail-value {
    font-weight: 600;
    color: #0f172a;
}

.balance-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

@media (max-width: 991.98px) {
    .leave-hero {
        padding: 20px;
    }
}
</style>

@endsection
