@extends('layouts.app')

@section('page_title', 'Add Schedule')
@section('topbar_title', 'Add New Schedule')

@section('content')
@php
    $defaultStartTime = now();
    $defaultEndTime = $defaultStartTime->copy()->addHour();
    $defaultEndValue = $defaultEndTime->isSameDay($defaultStartTime) ? $defaultEndTime->format('H:i') : '23:59';
@endphp
<div class="timetable-form-container">
    <div class="form-header">
        <h1>Add New Schedule</h1>
        <p class="text-muted">Create a new schedule entry</p>
    </div>

    <div class="form-wrapper">
        <form method="POST" action="{{ route('timetables.store') }}" class="timetable-form">
            @csrf
            
            <div class="form-row">
                <div class="form-group">
                    <label for="title" class="form-label">Title *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="{{ old('title') }}" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="priority" class="form-label">Priority *</label>
                    <select id="priority" name="priority" class="form-control" required>
                        <option value="">Select Priority</option>
                        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', 'medium') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="date" class="form-label">Date *</label>
                      <input type="date" id="date" name="date" class="form-control" 
                          value="{{ old('date', request('date', now()->toDateString())) }}" required>
                @error('date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_time" class="form-label">Start Time *</label>
                          <input type="time" id="start_time" name="start_time" class="form-control" 
                              value="{{ old('start_time', $defaultStartTime->format('H:i')) }}" required>
                    @error('start_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_time" class="form-label">End Time *</label>
                          <input type="time" id="end_time" name="end_time" class="form-control" 
                              value="{{ old('end_time', $defaultEndValue) }}" required>
                    @error('end_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @php
                $selectedAreaId = old('department');
                $selectedBuildingId = old('building_id');
                if (! $selectedBuildingId && $selectedAreaId) {
                    foreach (($departments ?? collect()) as $building) {
                        if ((string) $building->id === (string) $selectedAreaId) {
                            $selectedBuildingId = $building->id;
                            break;
                        }

                        foreach ($building->children as $area) {
                            if ((string) $area->id === (string) $selectedAreaId) {
                                $selectedBuildingId = $building->id;
                                break 2;
                            }
                        }
                    }
                }

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

            <div class="form-row">
                <div class="form-group">
                    <label for="building_id" class="form-label">Building</label>
                    <select id="building_id" name="building_id" class="form-control">
                        <option value="">-- Choose a building --</option>
                        @foreach(($departments ?? collect()) as $building)
                            <option value="{{ $building->id }}" {{ (string) $selectedBuildingId === (string) $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">Area {{ ($departments ?? collect())->isNotEmpty() ? '*' : '' }}</label>
                    <select id="department" name="department" class="form-control" data-selected="{{ $selectedAreaId }}" {{ ($departments ?? collect())->isNotEmpty() ? 'required' : '' }}>
                        <option value="">-- Choose an area --</option>
                    </select>
                    @error('department')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="employee_id" class="form-label">Assign to Employee *</label>
                    <select id="employee_id" name="employee_id" class="form-control" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" data-department-id="{{ $employee->department_id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->area ?? 'No area' }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            
                <!-- Assign to Role removed from supervisor tasks form -->
            </div>

            <div class="form-actions">
                <a href="{{ route('timetables.index', ['date' => request('date'), 'view' => 'day']) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Schedule</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buildings = @json($buildingsForScript);
    const hasOldStartTime = @json(old('start_time') !== null);
    const buildingSelect = document.getElementById('building_id');
    const employeeSelect = document.getElementById('employee_id');
    const departmentSelect = document.getElementById('department');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');

    function formatTime(date) {
        return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
    }

    if (!hasOldStartTime && startTimeInput && endTimeInput) {
        const now = new Date();
        const end = new Date(now.getTime() + 60 * 60 * 1000);
        startTimeInput.value = formatTime(now);
        endTimeInput.value = formatTime(end);
    }

    function refreshAreas() {
        const selectedArea = departmentSelect.dataset.selected || '';
        const building = buildings.find(item => String(item.id) === String(buildingSelect.value));
        departmentSelect.innerHTML = '<option value="">-- Choose an area --</option>';

        if (!building) {
            filterEmployeesByDepartment('');
            return;
        }

        building.areas.forEach(area => {
            const option = document.createElement('option');
            option.value = area.id;
            option.textContent = area.name;
            option.selected = String(area.id) === String(selectedArea);
            departmentSelect.appendChild(option);
        });

        filterEmployeesByDepartment(departmentSelect.value);
    }

    function filterEmployeesByDepartment(deptId) {
        for (const opt of employeeSelect.options) {
            const optDept = opt.dataset.departmentId || '';
            if (!deptId) {
                opt.style.display = '';
            } else {
                opt.style.display = optDept === String(deptId) ? '' : 'none';
            }
        }
    }

    buildingSelect.addEventListener('change', function() {
        departmentSelect.dataset.selected = '';
        refreshAreas();
    });

    departmentSelect.addEventListener('change', function() {
        departmentSelect.dataset.selected = this.value;
        filterEmployeesByDepartment(this.value);
    });

    refreshAreas();
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
