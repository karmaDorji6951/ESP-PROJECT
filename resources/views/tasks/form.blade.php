@php($model = $task ?? null)
<div class="col-12 mb-3">
    <div class="row g-3 align-items-center border-bottom pb-3">
        <div class="col-md-3">
            <label class="form-label mb-0 fw-semibold">Assign to ESP <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-9">
            <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required style="border: 1px solid #ced4da;">
                <option value="">Select ESP</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected(old('employee_id', $model->employee_id ?? '') == $employee->id)>{{ $employee->name }} ({{ $employee->role_title }})</option>
                @endforeach
            </select>
            @error('employee_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 mb-3">
    <div class="row g-3 align-items-center border-bottom pb-3">
        <div class="col-md-3">
            <label class="form-label mb-0 fw-semibold">Assignment Mode <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-9">
            <select name="assignment_type" id="assignment_type" class="form-select @error('assignment_type') is-invalid @enderror" required style="border: 1px solid #ced4da;">
                @foreach(['date' => 'Specific Date', 'week' => 'One Week', 'month' => 'Month'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('assignment_type', $model->assignment_type ?? 'date') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('assignment_type')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 mb-3">
    <div class="row g-3 align-items-center border-bottom pb-3">
        <div class="col-md-3">
            <label class="form-label mb-0 fw-semibold">Start Date <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-9">
            <input type="date" name="start_date" id="start_date" value="{{ old('start_date', isset($model) && $model->schedule_start_date ? $model->schedule_start_date->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('start_date') is-invalid @enderror" required style="border: 1px solid #ced4da;">
            @error('start_date')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 mb-3">
    <div class="row g-3 align-items-center border-bottom pb-3">
        <div class="col-md-3">
            <label class="form-label mb-0 fw-semibold">Deadline (Optional)</label>
        </div>
        <div class="col-md-9">
            <input type="date" name="deadline" value="{{ old('deadline', isset($model) && $model->deadline ? $model->deadline->format('Y-m-d') : '') }}" class="form-control @error('deadline') is-invalid @enderror" style="border: 1px solid #ced4da;">
            @error('deadline')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 mb-3">
    <div class="row g-3 align-items-center border-bottom pb-3">
        <div class="col-md-3">
            <label class="form-label mb-0 fw-semibold">Work Title <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-9">
            <input type="text" name="title" value="{{ old('title', $model->title ?? '') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Enter work title" required style="border: 1px solid #ced4da;">
            @error('title')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="col-12 mb-3">
    <label class="form-label fw-semibold">Work Details</label>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Enter work details (optional)" style="border: 1px solid #ced4da;">{{ old('description', $model->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
<div class="col-12">
    <div class="row g-3 align-items-center">
        <div class="col-md-3">
            <label class="form-label mb-0 fw-semibold">Status <span class="text-danger">*</span></label>
        </div>
        <div class="col-md-9">
            <select name="status" class="form-select @error('status') is-invalid @enderror" required style="border: 1px solid #ced4da;">
                @foreach(['Pending', 'In Progress', 'Completed'] as $status)
                    <option value="{{ $status }}" @selected(old('status', $model->status ?? 'Pending') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            @error('status')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
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
