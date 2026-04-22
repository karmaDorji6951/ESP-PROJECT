@php($model = $leave ?? null)
<div class="col-md-6">
    <label class="form-label">Employee</label>
    <select name="employee_id" class="form-select">
        <option value="">Select employee (optional)</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" @selected(old('employee_id', $model->employee_id ?? '') == $employee->id)>{{ $employee->name }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">Leave Type</label>
    <input type="text" name="leave_type" value="{{ old('leave_type', $model->leave_type ?? '') }}" class="form-control" placeholder="Casual, Medical, etc." required>
</div>
<div class="col-md-6">
    <label class="form-label">Start Date</label>
    <input type="date" name="start_date" value="{{ old('start_date', isset($model) && $model->start_date ? $model->start_date->format('Y-m-d') : '') }}" class="form-control" required>
</div>
<div class="col-md-6">
    <label class="form-label">End Date</label>
    <input type="date" name="end_date" value="{{ old('end_date', isset($model) && $model->end_date ? $model->end_date->format('Y-m-d') : '') }}" class="form-control" required>
</div>
<div class="col-12">
    <label class="form-label">Reason</label>
    <textarea name="reason" rows="4" class="form-control" required>{{ old('reason', $model->reason ?? '') }}</textarea>
</div>
