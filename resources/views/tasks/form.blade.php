@php($model = $task ?? null)
<div class="col-md-6">
    <label class="form-label">Assign to ESP</label>
    <select name="employee_id" class="form-select" required>
        <option value="">Select ESP</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}" @selected(old('employee_id', $model->employee_id ?? '') == $employee->id)>{{ $employee->name }} ({{ $employee->role_title }})</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">Assignment Mode</label>
    <select name="assignment_type" id="assignment_type" class="form-select" required>
        @foreach(['date' => 'Specific Date', 'week' => 'One Week', 'month' => 'Month'] as $value => $label)
            <option value="{{ $value }}" @selected(old('assignment_type', $model->assignment_type ?? 'date') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-md-6">
    <label class="form-label">Start Date</label>
    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', isset($model) && $model->schedule_start_date ? $model->schedule_start_date->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control" required>
</div>
<div class="col-md-6">
    <label class="form-label">Deadline (Optional)</label>
    <input type="date" name="deadline" value="{{ old('deadline', isset($model) && $model->deadline ? $model->deadline->format('Y-m-d') : '') }}" class="form-control">
</div>
<div class="col-12">
    <label class="form-label">Work Title</label>
    <input type="text" name="title" value="{{ old('title', $model->title ?? '') }}" class="form-control" required>
</div>
<div class="col-12">
    <label class="form-label">Work Details</label>
    <textarea name="description" rows="4" class="form-control">{{ old('description', $model->description ?? '') }}</textarea>
</div>
<div class="col-md-6">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
        @foreach(['Pending', 'In Progress', 'Completed'] as $status)
            <option value="{{ $status }}" @selected(old('status', $model->status ?? 'Pending') === $status)>{{ $status }}</option>
        @endforeach
    </select>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const assignmentType = document.getElementById('assignment_type');
    const startDate = document.getElementById('start_date');

    if (!assignmentType || !startDate) {
        return;
    }

    assignmentType.addEventListener('change', function () {
        if (!startDate.value) {
            startDate.value = new Date().toISOString().split('T')[0];
        }
    });
});
</script>
@endpush
