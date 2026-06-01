@extends('layouts.app')

@section('title', 'Reviewed')
@section('page_title', 'Reviewed')

@section('content')
<div class="container-fluid py-3 py-lg-4">
    <div class="app-page-hero d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="app-page-hero-kicker mb-2">Reviewed Work</div>
            <h1 class="app-page-hero-title mb-2">Reviewed</h1>
            <p class="app-page-hero-subtitle">Submitted evaluations, task details, and grading breakdowns.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" id="toggle_reviews" class="btn btn-light app-page-hero-action">Expand all</button>
            <a href="{{ route('evaluations.create') }}" class="btn btn-light app-page-hero-action">New Evaluation</a>
        </div>
    </div>

    <div class="row g-4">
        @forelse($evaluations as $evaluation)
            @php
                $task = $evaluation->evaluated;
                $scores = (array) ($evaluation->scores ?? []);
                $criteriaLabels = [
                    'communication' => 'Communication',
                    'quality' => 'Work Quality',
                    'timeliness' => 'Timeliness',
                    'initiative' => 'Initiative',
                ];
            @endphp
            <div class="col-12">
                <details class="reviewed-item shadow-sm">
                    <summary class="reviewed-summary">
                        <div class="reviewed-summary-left">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <span class="badge text-bg-light text-dark">Reviewed</span>
                                <span class="badge bg-success-subtle text-success text-uppercase">{{ $evaluation->status ?? 'submitted' }}</span>
                            </div>
                            <h2 class="h5 mb-1">{{ $task?->title ?? 'No linked task' }}</h2>
                            <div class="text-muted">Submitted by {{ $evaluation->user?->name ?? 'Unknown' }} on {{ $evaluation->created_at?->format('Y-m-d H:i') }}</div>
                        </div>

                        <div class="reviewed-summary-right">
                            <div class="small text-muted text-uppercase fw-semibold">Overall</div>
                            <div class="d-flex gap-2 align-items-center justify-content-lg-end">
                                <span class="reviewed-score">{{ $evaluation->rating }}</span>
                                <span class="badge rounded-pill text-bg-primary px-3 py-2">Grade {{ $evaluation->grade }}</span>
                            </div>
                        </div>
                    </summary>

                    <div class="reviewed-body">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="reviewed-info-box h-100">
                                    <div class="reviewed-section-title">Task details</div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="reviewed-label">Assigned to</div>
                                            <div class="reviewed-value">{{ $task?->employee?->name ?? 'Unknown' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reviewed-label">Assigned by</div>
                                            <div class="reviewed-value">{{ $task?->assigner?->name ?? 'Unknown' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reviewed-label">Status</div>
                                            <div class="reviewed-value">{{ $task?->status ?? 'N/A' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="reviewed-label">Deadline</div>
                                            <div class="reviewed-value">{{ $task?->deadline?->format('Y-m-d') ?? 'No deadline' }}</div>
                                        </div>
                                        <div class="col-12">
                                            <div class="reviewed-label">Description</div>
                                            <div class="reviewed-description">{{ $task?->description ?? 'No task description available.' }}</div>
                                        </div>
                                    </div>
                                    @if($task)
                                        <div class="mt-3">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-primary btn-sm">View Task Details</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-5">
                                <div class="reviewed-info-box h-100">
                                    <div class="reviewed-section-title">Grading breakdown</div>
                                    <div class="d-grid gap-3">
                                        @foreach($criteriaLabels as $key => $label)
                                            <div class="grading-row">
                                                <div>
                                                    <div class="reviewed-label mb-1">{{ $label }}</div>
                                                    <div class="reviewed-grade-score">{{ $scores[$key] ?? 'N/A' }}/5</div>
                                                </div>
                                                <div class="progress grading-progress">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ is_numeric($scores[$key] ?? null) ? ((int) $scores[$key] * 20) : 0 }}%" aria-valuenow="{{ $scores[$key] ?? 0 }}" aria-valuemin="0" aria-valuemax="5"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($evaluation->comments)
                                        <div class="mt-4">
                                            <div class="reviewed-section-title mb-2">Comments</div>
                                            <div class="reviewed-comment">{{ $evaluation->comments }}</div>
                                        </div>
                                    @endif

                                    @if($evaluation->attachments)
                                        <div class="mt-4">
                                            <a href="{{ route('evaluations.download', $evaluation) }}" class="btn btn-outline-secondary btn-sm">Download Attachment</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-5 text-center text-muted">No evaluations have been submitted yet.</div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $evaluations->links() }}
    </div>
 </div>

<style>
.reviewed-card {
    border-radius: 18px;
}

.reviewed-item {
    display: block;
    border-radius: 18px;
    background: #fff;
    border: 1px solid rgba(15, 32, 68, 0.08);
    overflow: hidden;
}

.reviewed-item[open] .reviewed-summary {
    border-bottom-color: rgba(15, 32, 68, 0.08);
}

.reviewed-summary {
    list-style: none;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: flex-start;
    padding: 20px 24px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid transparent;
}

.reviewed-summary::-webkit-details-marker {
    display: none;
}

.reviewed-summary-left {
    min-width: 0;
}

.reviewed-summary-right {
    text-align: right;
    flex-shrink: 0;
}

.reviewed-score {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    color: #0f2044;
}

.reviewed-body {
    padding: 24px;
}

.reviewed-info-box {
    border: 1px solid rgba(15, 32, 68, 0.08);
    border-radius: 16px;
    background: #fff;
    padding: 20px;
}

.reviewed-section-title {
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 14px;
}

.reviewed-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #64748b;
}

.reviewed-value {
    font-weight: 600;
    color: #0f172a;
}

.reviewed-description,
.reviewed-comment {
    color: #334155;
    line-height: 1.7;
}

.reviewed-grade-score {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f2044;
}

.grading-row {
    display: grid;
    gap: 8px;
}

.grading-progress {
    height: 10px;
    border-radius: 999px;
    background: #e2e8f0;
}

.grading-progress .progress-bar {
    border-radius: 999px;
    background: linear-gradient(90deg, #0f2044 0%, #1d9e75 100%);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle_reviews');
    const reviewItems = Array.from(document.querySelectorAll('.reviewed-item'));

    if (!toggleButton || !reviewItems.length) {
        return;
    }

    const updateButtonLabel = () => {
        const allOpen = reviewItems.every((item) => item.open);
        toggleButton.textContent = allOpen ? 'Collapse all' : 'Expand all';
    };

    toggleButton.addEventListener('click', function () {
        const shouldOpen = reviewItems.some((item) => !item.open);

        reviewItems.forEach((item) => {
            item.open = shouldOpen;
        });

        updateButtonLabel();
    });

    reviewItems.forEach((item) => {
        item.addEventListener('toggle', updateButtonLabel);
    });

    updateButtonLabel();
});
</script>

@endsection
