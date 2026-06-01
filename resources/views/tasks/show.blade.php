@extends('layouts.app')

@section('title', 'Task: ' . $task->title)
@section('page_title', 'Task Details')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h5 class="mb-1">{{ $task->title }}</h5>
            <div class="text-muted small">
                Assigned to: {{ $task->employee?->user?->name ?? $task->employee?->name ?? 'Unknown' }}
            </div>
        </div>
        @php
            $role = auth()->user()?->role?->slug ?? strtolower((string) auth()->user()?->role?->name);
            $backRoute = $role === 'supervisor' ? 'supervisor.tasks.index' : ($role === 'staff' ? 'staff.tasks.index' : 'timetables.index');
        @endphp
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary btn-sm">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Task Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Description</div>
                    <div>{{ $task->description ?: 'No description provided.' }}</div>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="text-muted small mb-1">Status</div>
                        <span class="badge {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'In Progress' ? 'bg-info' : 'bg-warning') }}">
                            {{ $task->status }}
                        </span>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small mb-1">Deadline</div>
                        <div>{{ $task->deadline?->format('Y-m-d') ?? 'No deadline' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small mb-1">Created</div>
                        <div>{{ $task->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
                    </div>
                </div>

                <hr>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Assigned By</div>
                        <div>{{ $task->assigner?->name ?? 'Unknown' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Task ID</div>
                        <div><code>{{ $task->id }}</code></div>
                    </div>
                </div>

                @if($task->timetable)
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Schedule Date</div>
                            <div>{{ $task->timetable->date->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Schedule Status</div>
                            <span class="badge bg-secondary">{{ $task->timetable->status }}</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('timetables.show', $task->timetable) }}" class="btn btn-outline-primary btn-sm">View Schedule</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Quick Info</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Progress</div>
                    <div class="progress" role="progressbar" style="height: 8px;">
                        <div class="progress-bar {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'In Progress' ? 'bg-info' : 'bg-warning') }}" style="width: {{ $task->status === 'Completed' ? 100 : ($task->status === 'In Progress' ? 50 : 0) }}%"></div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Last Submission</div>
                    @if($task->latestSubmission)
                        <div class="small">{{ $task->latestSubmission->created_at?->diffForHumans() ?? '' }}</div>
                    @else
                        <div class="text-muted small">No submissions yet</div>
                    @endif
                </div>

                <hr>

                <div class="mb-0">
                    <div class="text-muted small mb-1">Evaluation</div>
                    @if($task->latestEvaluation || $task->evaluation)
                        @php $evaluation = $task->latestEvaluation ?? $task->evaluation; @endphp
                        <div class="small">
                            Grade: <strong>{{ $evaluation->grade }}</strong> · Rating: <strong>{{ $evaluation->rating }}/5</strong>
                        </div>
                        <div class="text-muted small">
                            {{ $evaluation->evaluated_at?->diffForHumans() ?? '' }}
                        </div>
                    @else
                        <div class="text-muted small">Not evaluated yet</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection