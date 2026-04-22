@extends('layouts.app')

@section('title', 'Mark Attendance')
@section('page_title', 'Mark Attendance')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card card-soft">
            <div class="card-header bg-light">
                <h5 class="mb-0">Mark Employee Attendance</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('supervisor.attendance.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="attendance_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('attendance_date') is-invalid @enderror" 
                               id="attendance_date" name="attendance_date" value="{{ old('attendance_date', date('Y-m-d')) }}" required>
                        @error('attendance_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="">-- Select Status --</option>
                            <option value="Present" {{ old('status') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ old('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Leave" {{ old('status') == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                  id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Mark Attendance
                        </button>
                        <a href="{{ route('supervisor.attendance.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
