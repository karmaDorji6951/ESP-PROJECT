@extends('layouts.app')

@section('page_title', 'Task Details')
@section('title', 'Task: ' . $task->title)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div>
                <h5 class="mb-1">{{ $task->title }}</h5>
                <div class="text-muted small">
                    Assigned to: {{ $task->employee?->user?->name ?? $task->employee?->name ?? 'Unknown' }}
                </div>
            </div>
        </div>
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
                            <div class="text-muted small mb-1">Timetable Date</div>
                            <div>{{ \Illuminate\Support\Carbon::parse($task->timetable->date)->format('Y-m-d') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Timetable Status</div>
                            <span class="badge bg-secondary">{{ $task->timetable->status }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-footer bg-white d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                @if($task->status === 'Completed' && $task->latestSubmission)
                    <a href="{{ route('supervisor.tasks.evaluation.create', $task) }}" class="btn btn-success btn-sm">
                        {{ $task->evaluation ? 'Update Evaluation' : 'Evaluate' }}
                    </a>
                @endif
                <a href="{{ route('supervisor.tasks.edit', $task) }}" class="btn btn-primary btn-sm">Edit</a>
                <form action="{{ route('supervisor.tasks.destroy', $task) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                </form>
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
                        <div class="small">
                            {{ $task->latestSubmission->created_at?->diffForHumans() ?? '' }}
                        </div>
                    @else
                        <div class="text-muted small">No submissions yet</div>
                    @endif
                </div>

                <hr>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Evaluation</div>
                    @if($task->evaluation)
                        <div class="small">
                            Grade: <strong>{{ $task->evaluation->grade }}</strong> · Rating: <strong>{{ $task->evaluation->rating }}/5</strong>
                        </div>
                        <div class="text-muted small">
                            {{ $task->evaluation->evaluated_at?->diffForHumans() ?? '' }}
                        </div>
                    @else
                        <div class="text-muted small">Not evaluated yet</div>
                    @endif
                </div>

                <hr>

                <div class="mb-0">
                    <div class="text-muted small mb-1">Time Remaining</div>
                    @if($task->deadline)
                        @if($task->deadline->isPast() && $task->status !== 'Completed')
                            <div class="alert alert-danger mt-2 mb-0 py-2">
                                <small>Overdue by {{ $task->deadline->diffInDays(now()) }} days</small>
                            </div>
                        @else
                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                <small>{{ $task->deadline->diffInDays(now()) }} days remaining</small>
                            </div>
                        @endif
                    @else
                        <div class="text-muted small">No deadline set</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
