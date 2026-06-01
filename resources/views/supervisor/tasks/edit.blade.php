@extends('layouts.app')

@section('page_title', 'Edit Task')
@section('title', 'Edit Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-body p-4">
        <form action="{{ route('supervisor.tasks.update', $task) }}" method="POST" class="row g-4">
            @csrf
            @method('PUT')

            @php
                $selectedAreaId = old('department', $task->employee->department_id ?? null);
                $selectedBuildingId = old('building_id', $task->employee->departmentRelation?->parent_id ?? $task->employee->department_id ?? null);
                $buildingsForScript = ($departments ?? collect())->map(function ($building) {
                    return [
                        'id' => $building->id,
                        'name' => $building->name,
                        'areas' => $building->children->isNotEmpty()
                            ? $building->children->map(fn ($area) => ['id' => $area->id, 'name' => $area->name])->values()
                            : collect([['id' => $building->id, 'name' => $building->name]]),
                    ];
                })->values();
            @endphp

            <!-- Building Selection -->
            <div class="col-md-6">
                <label for="building_id" class="form-label">Select Building {{ ($departments ?? collect())->isNotEmpty() ? '*' : '' }}</label>
                <select name="building_id" id="building_id" class="form-select">
                    <option value="">-- Choose a building --</option>
                    @foreach(($departments ?? collect()) as $building)
                        <option value="{{ $building->id }}" {{ (string) $selectedBuildingId === (string) $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Area Selection -->
            <div class="col-md-6">
                <label for="department" class="form-label">Select Area {{ ($departments ?? collect())->isNotEmpty() ? '*' : '' }}</label>
                <select name="department" id="department" class="form-select @error('department') is-invalid @enderror" {{ ($departments ?? collect())->isNotEmpty() ? 'required' : '' }}>
                    <option value="">-- Choose an area --</option>
                </select>
                @error('department')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Employee Selection -->
            <div class="col-12">
                <label for="employee_id" class="form-label">Select Employee *</label>
                <select name="employee_id" id="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                    <option value="">-- Choose an employee --</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" data-department-id="{{ $employee->department_id }}" {{ old('employee_id', $task->employee_id) == $employee->id ? 'selected' : '' }}>
                        {{ $employee->name }} ({{ $employee->area ?? 'No area' }})
                    </option>
                    @endforeach
                </select>
                @error('employee_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Task Title -->
            <div class="col-12">
                <label for="title" class="form-label">Task Title *</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                       placeholder="Enter task title" value="{{ old('title', $task->title) }}" required>
                @error('title')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Task Description -->
            <div class="col-12">
                <label for="description" class="form-label">Task Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="4" placeholder="Enter task description (optional)">{{ old('description', $task->description) }}</textarea>
                @error('description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label for="status" class="form-label">Status *</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="Pending" {{ old('status', $task->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="In Progress" {{ old('status', $task->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Completed" {{ old('status', $task->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <!-- Deadline -->
            <div class="col-md-6">
                <label for="deadline" class="form-label">Deadline</label>
                <input type="date" name="deadline" id="deadline" class="form-control @error('deadline') is-invalid @enderror" 
                       value="{{ old('deadline', $task->deadline ? $task->deadline->format('Y-m-d') : '') }}">
                @error('deadline')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-12 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Update Task</button>
                <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buildings = @json($buildingsForScript);
    const selectedAreaId = @json((string) $selectedAreaId);
    const buildingSelect = document.getElementById('building_id');
    const departmentSelect = document.getElementById('department');
    const employeeSelect = document.getElementById('employee_id');

    if (!buildingSelect || !departmentSelect || !employeeSelect) {
        return;
    }

    const refreshAreas = () => {
        const selectedArea = departmentSelect.dataset.selected || selectedAreaId;
        const building = buildings.find(item => String(item.id) === String(buildingSelect.value));
        departmentSelect.innerHTML = '<option value="">-- Choose an area --</option>';

        if (!building) {
            return;
        }

        building.areas.forEach(area => {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.name;
            option.selected = String(area.id) === String(selectedArea);
            departmentSelect.appendChild(option);
        });
    };

    const applyEmployeeFilter = () => {
        const selectedDepartment = departmentSelect.value;

        Array.from(employeeSelect.options).forEach((option, index) => {
            if (index === 0) {
                return; // placeholder
            }

            const optionDepartment = option.dataset.departmentId || '';
            const shouldShow = !selectedDepartment || optionDepartment === selectedDepartment;

            option.hidden = !shouldShow;
            option.disabled = !shouldShow;
        });

        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        if (selectedOption && (selectedOption.hidden || selectedOption.disabled)) {
            employeeSelect.value = '';
        }
    };

    buildingSelect.addEventListener('change', function () {
        departmentSelect.dataset.selected = '';
        refreshAreas();
        applyEmployeeFilter();
    });

    departmentSelect.addEventListener('change', function () {
        departmentSelect.dataset.selected = this.value;
        applyEmployeeFilter();
    });

    refreshAreas();
    applyEmployeeFilter();
});
</script>
@endpush
