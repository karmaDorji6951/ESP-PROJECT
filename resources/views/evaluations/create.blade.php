@extends('layouts.app')

@section('content')
<div class="evaluation-shell container-fluid py-3 py-lg-4">
    <div class="evaluation-hero mb-4">
        <div>
            <div class="evaluation-kicker">Performance Review</div>
            <h1 class="evaluation-title mb-2">Submit Evaluation</h1>
            <p class="evaluation-subtitle mb-0">Choose a completed task, review it by role, and submit a structured scorecard.</p>
        </div>
        <a href="{{ url()->previous() }}" class="btn btn-outline-light evaluation-back-btn">Back</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success evaluation-alert">{{ session('success') }}</div>
    @endif

    @if(!empty($evaluationTaskGroups) && $evaluationTaskGroups->isNotEmpty())
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <div class="evaluation-panel card shadow-sm border-0">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase text-muted small fw-semibold">Task Library</div>
                                <div class="fw-bold">Select by Role</div>
                            </div>
                            <span class="badge rounded-pill text-bg-light">{{ $evaluationTaskGroups->count() }} roles</span>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        @foreach($evaluationTaskGroups as $index => $group)
                            <details class="role-group mb-3" {{ (int) ($selectedRoleId ?? 0) === (int) $group['role']->id ? 'open' : ($index === 0 ? 'open' : '') }}>
                                <summary class="role-summary">
                                    <div>
                                        <div class="role-name">{{ $group['role']->name }}</div>
                                        <div class="role-meta">{{ $group['tasks']->count() }} completed task{{ $group['tasks']->count() === 1 ? '' : 's' }}</div>
                                    </div>
                                    <span class="role-count">{{ $group['tasks']->count() }}</span>
                                </summary>
                                <div class="role-body">
                                    <div class="task-list">
                                        @foreach($group['tasks'] as $task)
                                            @php $isSelectedTask = $evaluatedTask?->id === $task->id; @endphp
                                            <button
                                                type="button"
                                                class="task-item evaluation-task-item {{ $isSelectedTask ? 'active' : '' }}"
                                                data-task-id="{{ $task->id }}"
                                                data-title="{{ $task->title }}"
                                                data-employee="{{ $task->employee?->name ?? 'Unknown' }}"
                                                data-status="{{ $task->status }}"
                                                data-deadline="{{ $task->deadline?->format('Y-m-d') ?? 'No deadline' }}"
                                                data-priority="{{ ucfirst($task->timetable?->priority ?? 'N/A') }}"
                                                data-description="{{ $task->description ?? '' }}"
                                            >
                                                <div class="task-item-title">{{ $task->title }}</div>
                                                <div class="task-item-subtitle">
                                                    {{ $task->employee?->name ?? 'Unknown' }} · {{ $task->deadline?->format('Y-m-d') ?? 'No deadline' }}
                                                </div>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                @if($evaluatedTask)
                    @php
                        $roleSlug = strtolower((string) (auth()->user()?->role?->slug ?? ''));
                        $taskDetailsRoute = $roleSlug === 'supervisor' && Route::has('supervisor.tasks.show')
                            ? route('supervisor.tasks.show', $evaluatedTask)
                            : route('tasks.show', $evaluatedTask);
                    @endphp
                    <div class="card shadow-sm border-0 mb-4 selected-task-card">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                <div>
                                    <div class="text-uppercase text-muted small fw-semibold mb-2">Selected Task</div>
                                    <h2 class="h4 mb-2" id="selected_task_title">{{ $evaluatedTask->title }}</h2>
                                    <div class="text-muted mb-1">Assigned to <span id="selected_task_employee">{{ $evaluatedTask->employee?->name ?? 'Unknown' }}</span></div>
                                    <div class="text-muted mb-1">Status: <span id="selected_task_status">{{ $evaluatedTask->status }}</span></div>
                                    <div class="text-muted mb-1">Priority: <span id="selected_task_priority">{{ ucfirst($evaluatedTask->timetable?->priority ?? 'N/A') }}</span></div>
                                    <div class="text-muted">Deadline: <span id="selected_task_deadline">{{ $evaluatedTask->deadline?->format('Y-m-d') ?? 'No deadline' }}</span></div>
                                </div>

                                <!-- badges removed for cleaner evaluations UI -->
                            </div>

                            @if($evaluatedTask->description)
                                <div class="task-description-box mt-4" id="selected_task_description">{{ $evaluatedTask->description }}</div>
                            @endif

                            <div class="mt-4">
                                <a href="{{ $taskDetailsRoute }}" class="btn btn-outline-primary btn-sm">View Task Details</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning evaluation-alert">No completed tasks are available for evaluation.</div>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-warning evaluation-alert">No completed tasks are available for evaluation.</div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectedTitle = document.getElementById('selected_task_title');
    const selectedEmployee = document.getElementById('selected_task_employee');
    const selectedStatus = document.getElementById('selected_task_status');
    const selectedPriority = document.getElementById('selected_task_priority');
    const selectedDeadline = document.getElementById('selected_task_deadline');
    const selectedDescription = document.getElementById('selected_task_description');
    const taskButtons = document.querySelectorAll('.evaluation-task-item');

    if (!taskButtons.length) {
        return;
    }

    const setSelectedTask = (button) => {
        if (!button) {
            return;
        }

        const openDetails = button.closest('details');
        if (openDetails) {
            openDetails.open = true;
            openDetails.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        if (selectedTitle) selectedTitle.textContent = button.dataset.title || '';
        if (selectedEmployee) selectedEmployee.textContent = button.dataset.employee || '';
        if (selectedStatus) selectedStatus.textContent = button.dataset.status || '';
        if (selectedPriority) selectedPriority.textContent = button.dataset.priority || '';
        if (selectedDeadline) selectedDeadline.textContent = button.dataset.deadline || '';
        if (selectedDescription) selectedDescription.textContent = button.dataset.description || '';

        taskButtons.forEach((item) => item.classList.remove('active'));
        button.classList.add('active');
    };

    taskButtons.forEach((button) => {
        button.addEventListener('click', function () {
            setSelectedTask(button);
        });
    });

    setSelectedTask(document.querySelector('.evaluation-task-item.active') || taskButtons[0]);
});
</script>

<style>
.evaluation-shell {
    max-width: 1480px;
}

.evaluation-hero {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 24px 28px;
    border-radius: 20px;
    background: linear-gradient(135deg, #0f2044 0%, #12325f 55%, #1d9e75 160%);
    color: #fff;
    box-shadow: 0 18px 40px rgba(15, 32, 68, 0.18);
    position: relative;
    overflow: hidden;
}

.evaluation-hero::after {
    content: '';
    position: absolute;
    right: -70px;
    bottom: -70px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
}

.evaluation-kicker {
    font-size: 12px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    font-weight: 700;
    opacity: 0.82;
    margin-bottom: 10px;
}

.evaluation-title {
    font-size: clamp(2rem, 3vw, 2.6rem);
    font-weight: 800;
    line-height: 1.1;
    margin: 0;
}

.evaluation-subtitle {
    max-width: 720px;
    color: rgba(255, 255, 255, 0.88);
}

.evaluation-back-btn {
    border-color: rgba(255, 255, 255, 0.45);
    color: #fff;
    position: relative;
    z-index: 1;
}

.evaluation-back-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
}

.evaluation-alert {
    border-radius: 14px;
}

.evaluation-panel,
.selected-task-card {
    border-radius: 18px;
}

.role-group {
    border: 1px solid rgba(15, 32, 68, 0.08);
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
}

.role-summary {
    list-style: none;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 16px 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid rgba(15, 32, 68, 0.06);
}

.role-summary::-webkit-details-marker {
    display: none;
}

.role-name {
    font-weight: 800;
    color: #0f2044;
}

.role-meta {
    margin-top: 2px;
    font-size: 12px;
    color: #6b7280;
}

.role-count {
    min-width: 34px;
    height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef2ff;
    color: #334155;
    font-size: 12px;
    font-weight: 700;
}

.role-body {
    padding: 12px;
}

.task-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.task-item {
    width: 100%;
    text-align: left;
    border: 1px solid rgba(15, 32, 68, 0.08);
    border-radius: 14px;
    background: #fff;
    padding: 14px 14px 13px;
    transition: all 0.2s ease;
}

.task-item:hover {
    transform: translateY(-1px);
    border-color: rgba(29, 158, 117, 0.45);
    box-shadow: 0 10px 22px rgba(15, 32, 68, 0.08);
}

.task-item.active {
    background: linear-gradient(135deg, #0f2044 0%, #1d9e75 160%);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 12px 28px rgba(15, 32, 68, 0.18);
}

.task-item-title {
    font-weight: 700;
    line-height: 1.3;
}

.task-item-subtitle {
    margin-top: 4px;
    font-size: 12px;
    color: inherit;
    opacity: 0.8;
}

/* badges removed from selected task summary to simplify mobile UI */

.task-description-box {
    padding: 16px 18px;
    border-radius: 14px;
    background: #f8fafc;
    border: 1px solid rgba(15, 32, 68, 0.08);
    color: #334155;
}

@media (max-width: 991.98px) {
    .evaluation-hero {
        padding: 20px;
    }

    .evaluation-panel,
    .selected-task-card {
        border-radius: 16px;
    }
}
</style>
@endsection
