@extends('layouts.app')

@section('title', 'Leave Request Details')
@section('page_title', 'Leave Request Details')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Leave Request Details</h5>
            <a href="{{ route('supervisor.leaves.index') }}" class="btn btn-outline-secondary btn-sm">Back to Leave Requests</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Leave Details -->
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Employee</h6>
                        <p class="fw-semibold">{{ $leaf->employee?->name ?? $leaf->user?->name }}</p>
                    </div>
                    @isset($balance)
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Leave Balance (Year)</h6>
                        <p>
                            Allowance: <strong>{{ $balance['allowance'] }} days</strong><br>
                            Used (Approved): <strong class="text-success">{{ $balance['approved'] }} days</strong><br>
                            Pending: <strong class="text-warning">{{ $balance['pending'] }} days</strong><br>
                            Remaining: <strong class="text-danger">{{ $balance['remaining'] }} days</strong>
                        </p>
                    </div>
                    @endisset
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Leave Type</h6>
                        <p><span class="badge bg-info">{{ $leaf->leave_type }}</span></p>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Start Date</h6>
                        <p>{{ $leaf->start_date?->format('Y-m-d') ?? 'Not specified' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">End Date</h6>
                        <p>{{ $leaf->end_date?->format('Y-m-d') ?? 'Not specified' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Duration</h6>
                        <p>
                            @if($leaf->start_date && $leaf->end_date)
                                {{ $leaf->start_date->diffInDays($leaf->end_date) + 1 }} days
                            @else
                                Not specified
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Current Status</h6>
                        <p><span class="badge {{ 
                            $leaf->status === 'Approved' ? 'bg-success' : 
                            ($leaf->status === 'Rejected' ? 'bg-danger' : 'bg-warning') 
                        }}">{{ $leaf->status ?: 'Pending' }}</span></p>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Reason</h6>
                    <p class="text-muted">{{ $leaf->reason ?? 'No reason provided' }}</p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-2">Submitted On</h6>
                    <p>{{ $leaf->created_at?->format('Y-m-d H:i') ?? 'Unknown' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="col-lg-4">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Actions</div>
            <div class="card-body">
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
                            <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Add any comments..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                ✓ Submit Decision
                            </button>
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

@endsection
