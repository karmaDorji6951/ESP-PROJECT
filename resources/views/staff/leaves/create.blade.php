@extends('layouts.app')

@section('title', 'Request Leave')
@section('page_title', 'Request Leave')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>Request a Leave</span>
                <a href="{{ route('staff.leaves.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
            </div>

            <div class="card-body">
                @isset($balance)
                    <div class="mb-3">
                        <div class="d-flex gap-3">
                            <div class="card p-2">
                                <div class="fw-semibold">Annual Allowance</div>
                                <div class="fs-4">{{ $balance['allowance'] }} days</div>
                            </div>
                            <div class="card p-2">
                                <div class="fw-semibold">Used (Approved)</div>
                                <div class="fs-4 text-success">{{ $balance['approved'] }} days</div>
                            </div>
                            <div class="card p-2">
                                <div class="fw-semibold">Pending</div>
                                <div class="fs-4 text-warning">{{ $balance['pending'] }} days</div>
                            </div>
                            <div class="card p-2">
                                <div class="fw-semibold">Remaining</div>
                                <div class="fs-4 text-danger">{{ $balance['remaining'] }} days</div>
                            </div>
                            <div class="card p-2">
                                <div class="fw-semibold">Per Month / Week</div>
                                <div class="fs-6">{{ $balance['per_month'] }} / {{ $balance['per_week'] }} days</div>
                            </div>
                        </div>
                    </div>
                @endisset
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('staff.leaves.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="leave_type">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type" id="leave_type" class="form-control @error('leave_type') is-invalid @enderror" required>
                            <option value="">-- Select Leave Type --</option>
                            <option value="Sick Leave" {{ old('leave_type') === 'Sick Leave' ? 'selected' : '' }}>Sick Leave</option>
                            <option value="Casual Leave" {{ old('leave_type') === 'Casual Leave' ? 'selected' : '' }}>Casual Leave</option>
                            <option value="Earned Leave" {{ old('leave_type') === 'Earned Leave' ? 'selected' : '' }}>Earned Leave</option>
                            <option value="Maternity Leave" {{ old('leave_type') === 'Maternity Leave' ? 'selected' : '' }}>Maternity Leave</option>
                            <option value="Paternity Leave" {{ old('leave_type') === 'Paternity Leave' ? 'selected' : '' }}>Paternity Leave</option>
                            <option value="Other" {{ old('leave_type') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('leave_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="from_date">From Date <span class="text-danger">*</span></label>
                                <input type="date" name="from_date" id="from_date" 
                                    class="form-control @error('from_date') is-invalid @enderror" 
                                    value="{{ old('from_date') }}" required>
                                @error('from_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="to_date">To Date <span class="text-danger">*</span></label>
                                <input type="date" name="to_date" id="to_date" 
                                    class="form-control @error('to_date') is-invalid @enderror" 
                                    value="{{ old('to_date') }}" required>
                                @error('to_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="reason">Reason</label>
                        <textarea name="reason" id="reason" 
                            class="form-control @error('reason') is-invalid @enderror" 
                            rows="4" placeholder="Enter reason for leave request (optional)">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                        <a href="{{ route('staff.leaves.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
