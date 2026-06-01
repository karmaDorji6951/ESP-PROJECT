@extends('layouts.app')

@section('title', 'My Tasks')
@section('page_title', 'My Tasks')

@section('content')
<div class="app-page-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="app-page-hero-kicker mb-2">Staff Workspace</div>
            <h1 class="app-page-hero-title mb-2">My Tasks</h1>
            <p class="app-page-hero-subtitle">Track assigned work, deadlines, and submission status.</p>
        </div>
        <a href="{{ route('staff.dashboard') }}" class="btn btn-light app-page-hero-action">Back to Dashboard</a>
    </div>
</div>

<div class="card card-soft">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th></th>
                    <th>Task Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Due</th>
                    <th>Priority</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td class="align-middle">
                            <div class="table-avatar">
                                <div class="user-avatar">{{ strtoupper(substr(optional($task->assigner)->name ?? auth()->user()->name, 0, 1)) }}</div>
                            </div>
                        </td>
                        <td class="fw-semibold">{{ $task->title }}</td>
                        <td>
                            <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                        </td>
                        <td>
                            <span class="badge {{ 
                                $task->status === 'Completed' ? 'bg-success' : 
                                ($task->status === 'In Progress' ? 'bg-info' : 'bg-danger') 
                            }}">
                                {{ $task->status }}
                            </span>
                        </td>
                        <td><small>{{ $task->deadline?->format('Y-m-d') ?? '-' }}</small></td>
                        <td class="align-middle"><small>
                            @if($task->deadline)
                                @if(\Carbon\Carbon::now()->gt($task->deadline) && $task->status !== 'Completed')
                                    <span class="text-danger">Overdue</span>
                                @else
                                    {{ $task->deadline->diffForHumans() }}
                                @endif
                            @else
                                -
                            @endif
                        </small></td>
                        <td>
                            <span class="badge {{ 
                                $task->priority === 'High' ? 'bg-danger' : 
                                ($task->priority === 'Medium' ? 'bg-warning' : 'bg-secondary') 
                            }}">
                                {{ $task->priority ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('staff.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">View</a>
                                @if($task->status !== 'Completed')
                                    <button type="button" class="btn btn-sm btn-success" onclick="startTask({{ $task->id }})">
                                        Perform Work
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <div class="mb-2">📭 No tasks assigned yet</div>
                            <small>Check back later for new task assignments</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($tasks->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $tasks->links() }}
</div>
@endif

@push('scripts')
<script>
function startTask(taskId) {
    // Redirect to task show page with modal trigger
    window.location.href = `/staff/tasks/${taskId}?perform=true`;
}

// Check if we need to open the modal on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('perform') === 'true') {
        // Trigger the modal after a short delay to ensure page is loaded
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('performWorkModal'));
            if (modal) {
                modal.show();
            }
        }, 500);
    }
});
</script>
@endpush

@endsection
