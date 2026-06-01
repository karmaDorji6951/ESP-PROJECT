@extends('layouts.app')

@section('page_title', 'Edit Schedule')
@section('topbar_title', 'Edit Schedule')

@section('content')
<div class="timetable-form-container">
    <div class="form-header">
        <h1>Edit Schedule</h1>
        <p class="text-muted">Update schedule entry</p>
    </div>

    <div class="form-wrapper">
        <form method="POST" action="{{ route('timetables.update', $timetable) }}" class="timetable-form">
            @csrf
            @method('PUT')
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="{{ old('title', $timetable->title) }}" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="priority" class="form-label">Priority *</label>
                    <select id="priority" name="priority" class="form-control" required>
                        <option value="">Select Priority</option>
                        <option value="low" {{ old('priority', $timetable->priority) == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $timetable->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $timetable->priority) == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $timetable->description) }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="date" class="form-label">Date *</label>
                <input type="date" id="date" name="date" class="form-control" 
                       value="{{ old('date', $timetable->date->format('Y-m-d')) }}" required>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_time" class="form-label">Start Time *</label>
                    <input type="time" id="start_time" name="start_time" class="form-control" 
                           value="{{ old('start_time', $timetable->start_time->format('H:i')) }}" required>
                    @error('start_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_time" class="form-label">End Time *</label>
                    <input type="time" id="end_time" name="end_time" class="form-control" 
                           value="{{ old('end_time', $timetable->end_time->format('H:i')) }}" required>
                    @error('end_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="scheduled" {{ old('status', $timetable->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status', $timetable->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $timetable->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $timetable->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                @php
                    $employeeRequired = auth()->user()?->role?->slug === 'supervisor';
                @endphp
                <div class="form-group">
                    <label for="employee_id" class="form-label">Assign to Employee{{ $employeeRequired ? ' *' : '' }}</label>
                    <select id="employee_id" name="employee_id" class="form-control" @required($employeeRequired)>
                        <option value="">{{ $employeeRequired ? 'Select Employee' : 'Select Employee (Optional)' }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id', $timetable->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Assign to Role removed from edit form -->

            <div class="form-actions">
                <a href="{{ route('timetables.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // role selection removed; leave employee select safe
    const employeeSelect = document.getElementById('employee_id');
    if (employeeSelect) {
        employeeSelect.addEventListener('change', function() {
            // placeholder for future behavior
        });
    }
});
</script>
@endsection

@push('styles')
<style>
.timetable-form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-header {
    margin-bottom: 24px;
}

.form-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.form-header p {
    color: var(--text-muted);
    font-size: 14px;
}

.form-wrapper {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 24px;
}

.timetable-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 6px;
}

.form-control {
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: var(--supervisor-accent);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control.error {
    border-color: var(--danger);
}

.error-message {
    color: var(--danger);
    font-size: 12px;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary {
    background-color: var(--supervisor-accent);
    color: white;
}

.btn-primary:hover {
    background-color: var(--supervisor-dark);
}

.btn-secondary {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background-color: var(--border-color);
}

.text-muted {
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .form-wrapper {
        padding: 16px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
@endpush
