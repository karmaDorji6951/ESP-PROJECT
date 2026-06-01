@extends('layouts.app')

@section('title', 'Feedback')
@section('page_title', 'Feedback')

@section('content')
    <div class="app-page-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
            <div>
                <div class="app-page-hero-kicker mb-2">Workspace</div>
                <h1 class="app-page-hero-title mb-2">Share Feedback</h1>
                <p class="app-page-hero-subtitle">Send constructive feedback to a user, building, or area.</p>
            </div>
            <a href="{{ route('feedback.index') }}" class="btn btn-light app-page-hero-action">Back</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">🗣️ Share feedback</h4>
                    <p class="text-muted mb-4">Tell us what’s working well and what we can improve.</p>

                    <form method="POST" action="{{ route('feedback.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Location (optional)</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="building_department_id" class="form-label small text-muted mb-1">Building</label>
                                    <select id="building_department_id" name="building_department_id" class="form-select">
                                        <option value="" {{ old('building_department_id') ? '' : 'selected' }}>Select a building</option>
                                        @foreach(($departments ?? []) as $parent)
                                            <option value="{{ $parent->id }}" {{ (string) old('building_department_id') === (string) $parent->id ? 'selected' : '' }}>
                                                {{ $parent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="area_department_id" class="form-label small text-muted mb-1">Area</label>
                                    <select id="area_department_id" name="area_department_id" class="form-select" disabled>
                                        <option value="">Select an area</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-text">When selected, feedback will be delivered to assigned staff in that building/area. You can adjust recipients below.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Or send to specific user</label>
                            <select id="recipient_user_id" name="recipient_user_id" class="form-select">
                                <option value="" disabled {{ old('recipient_user_id') ? '' : 'selected' }}>Select a user</option>
                                @foreach(($users ?? []) as $user)
                                    <option value="{{ $user->id }}" {{ (string) old('recipient_user_id') === (string) $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="department-tasks" class="mb-3" style="display:none;">
                            <label class="form-label">Assigned staff & recent tasks</label>
                            <div id="tasks-list" class="mb-2"></div>
                            <div id="recipients-list" class="mb-2"></div>
                            <div class="form-text">Select which assigned staff should receive this feedback. By default, all staff in the selected building or area are preselected.</div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Feedback Title</label>
                            <input
                                type="text"
                                id="subject"
                                name="subject"
                                value="{{ old('subject') }}"
                                class="form-control"
                                maxlength="120"
                                required
                                placeholder="Short summary (e.g. Cleanliness issue in corridor)"
                            />
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feedback_type" class="form-label">Feedback Type</label>
                                    <select id="feedback_type" name="feedback_type" class="form-select" required>
                                        <option value="" disabled {{ old('feedback_type') ? '' : 'selected' }}>Select type</option>
                                        @foreach(['Complaint','Suggestion','Appreciation'] as $type)
                                            <option value="{{ $type }}" {{ old('feedback_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select id="priority" name="priority" class="form-select" required>
                                        <option value="" disabled {{ old('priority') ? '' : 'selected' }}>Select priority</option>
                                        @foreach(['Low','Medium','High'] as $p)
                                            <option value="{{ $p }}" {{ old('priority') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Description / Message</label>
                            <textarea
                                id="message"
                                name="message"
                                class="form-control"
                                rows="6"
                                required
                                maxlength="5000"
                                placeholder="Write your feedback here..."
                            >{{ old('message') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_anonymous" name="is_anonymous" {{ old('is_anonymous') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_anonymous">Submit anonymously</label>
                            </div>
                            <div class="form-text">If enabled, the recipient will not see who submitted this feedback.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const buildingSelect = document.getElementById('building_department_id');
            const areaSelect = document.getElementById('area_department_id');
            const tasksWrap = document.getElementById('department-tasks');
            const tasksList = document.getElementById('tasks-list');
            const recipientsList = document.getElementById('recipients-list');

            const departments = @json($departmentsForJs ?? []);

            function clearLists() {
                tasksList.innerHTML = '';
                recipientsList.innerHTML = '';
            }

            function getTargetDepartmentId() {
                return areaSelect && areaSelect.value ? areaSelect.value : (buildingSelect ? buildingSelect.value : '');
            }

            function rebuildAreas(buildingId) {
                if (!areaSelect) {
                    return;
                }

                areaSelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Select an area';
                areaSelect.appendChild(placeholder);

                const building = departments.find(d => String(d.id) === String(buildingId));
                const children = building && building.children ? building.children : [];

                if (!buildingId || children.length === 0) {
                    areaSelect.disabled = true;
                    return;
                }

                children.forEach(child => {
                    const opt = document.createElement('option');
                    opt.value = String(child.id);
                    opt.textContent = child.name;
                    areaSelect.appendChild(opt);
                });

                areaSelect.disabled = false;
            }

            async function loadForDepartment(dept) {
                if (!dept) {
                    tasksWrap.style.display = 'none';
                    clearLists();
                    return;
                }
                tasksWrap.style.display = 'block';
                clearLists();
                try {
                    const res = await fetch("{{ route('feedback.department.tasks') }}?department_id=" + encodeURIComponent(dept), { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    const tasks = data.tasks || [];
                    const staff = data.staff || [];

                    if (staff.length === 0) {
                        recipientsList.innerHTML = '<div class="text-muted">No assigned staff found for this building or area.</div>';
                    } else {
                        const rl = document.createElement('div');
                        staff.forEach(worker => {
                            if (!worker.user_id) {
                                return;
                            }

                            const id = 'recip_' + worker.user_id;
                            const wrapper = document.createElement('div');
                            wrapper.className = 'form-check';
                            wrapper.innerHTML = `<input class="form-check-input" type="checkbox" value="${worker.user_id}" id="${id}" name="recipient_user_ids[]" checked>
                                <label class="form-check-label" for="${id}">${worker.name} <span class="text-muted">(${worker.role_title || 'Assigned staff'})</span></label>`;
                            rl.appendChild(wrapper);
                        });
                        recipientsList.appendChild(rl);
                    }

                    if (tasks.length === 0) {
                        tasksList.innerHTML = '<div class="text-muted">No recent tasks found for this building or area.</div>';
                        return;
                    }

                    // Tasks
                    const tl = document.createElement('div');
                    tasks.forEach(t => {
                        const el = document.createElement('div');
                        el.className = 'small mb-1';
                        el.innerHTML = `<strong>${t.title}</strong> — <em>${t.employee_name}</em> <span class="text-muted">(${t.submissions_count} submissions)</span>`;
                        tl.appendChild(el);
                    });
                    tasksList.appendChild(tl);

                } catch (err) {
                    tasksList.innerHTML = '<div class="text-danger">Failed to load tasks.</div>';
                    recipientsList.innerHTML = '';
                }
            }

            function refreshDepartmentContext() {
                loadForDepartment(getTargetDepartmentId());
            }

            buildingSelect.addEventListener('change', function (e) {
                rebuildAreas(e.target.value);
                // reset area when building changes
                if (areaSelect) {
                    areaSelect.value = '';
                }
                refreshDepartmentContext();
            });

            areaSelect.addEventListener('change', function () {
                refreshDepartmentContext();
            });

            // Preload if old value exists
            const oldBuilding = "{{ old('building_department_id') }}";
            const oldArea = "{{ old('area_department_id') }}";
            if (oldBuilding) {
                rebuildAreas(oldBuilding);
                if (oldArea) {
                    areaSelect.value = oldArea;
                }
                refreshDepartmentContext();
            }
        })();
    </script>
@endsection
