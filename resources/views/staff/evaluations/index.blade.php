@extends('layouts.app')

@section('title', 'My Evaluations')
@section('page_title', 'My Evaluations')

@push('styles')
<style>
    .evaluation-card {
        border: 1px solid rgba(15, 32, 68, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 24px rgba(15, 32, 68, 0.06);
        overflow: hidden;
    }

    .evaluation-score {
        min-width: 74px;
        text-align: center;
    }

    .evaluation-score .rating {
        font-size: 1.65rem;
        line-height: 1;
        font-weight: 800;
        color: #0f2044;
    }

    .criteria-pill {
        border: 1px solid rgba(15, 32, 68, 0.08);
        border-radius: 12px;
        padding: 10px 12px;
        background: #f8fafc;
    }

    .criteria-pill .label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .criteria-pill .value {
        font-weight: 800;
        color: #0f2044;
    }

    @media (max-width: 768px) {
        .evaluation-card .card-body {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="app-page-hero d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <div class="app-page-hero-kicker mb-2">Task Reviews</div>
        <h3 class="app-page-hero-title mb-2">My Evaluations</h3>
        <p class="app-page-hero-subtitle">Your reviewed task submissions and grading feedback.</p>
    </div>
    <a href="{{ route('staff.dashboard') }}" class="btn btn-light app-page-hero-action">Back to Dashboard</a>
</div>

<div class="row g-4">
    @forelse($evaluations as $evaluation)
        @php
            $task = $evaluation->task;
            $criteria = (array) ($evaluation->criteria ?? []);
        @endphp

        <div class="col-12">
            <div class="card evaluation-card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <span class="badge bg-primary">{{ $evaluation->grade ?? 'N/A' }}</span>
                                <span class="badge bg-info text-dark">{{ $evaluation->rating ?? 'N/A' }}/5</span>
                                @if($evaluation->evaluated_at)
                                    <span class="text-muted small">{{ $evaluation->evaluated_at->format('Y-m-d H:i') }}</span>
                                @endif
                            </div>

                            <h5 class="fw-bold mb-1">{{ $task->title ?? 'Untitled task' }}</h5>
                            <div class="text-muted mb-3">
                                Evaluated by {{ $evaluation->evaluator->name ?? 'Supervisor' }}
                                @if($task?->assigner)
                                    <span class="mx-1">|</span> Assigned by {{ $task->assigner->name }}
                                @endif
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <div class="criteria-pill h-100">
                                        <div class="label">Quality</div>
                                        <div class="value">{{ $criteria['quality'] ?? 'N/A' }}/5</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="criteria-pill h-100">
                                        <div class="label">Timeliness</div>
                                        <div class="value">{{ $criteria['timeliness'] ?? 'N/A' }}/5</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="criteria-pill h-100">
                                        <div class="label">Evidence</div>
                                        <div class="value">{{ $criteria['evidence'] ?? 'N/A' }}/5</div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-muted">{{ $evaluation->remarks ?: 'No remarks provided.' }}</div>
                        </div>

                        <div class="evaluation-score d-flex flex-lg-column align-items-center justify-content-between gap-3">
                            <div>
                                <div class="text-muted small text-uppercase fw-semibold">Rating</div>
                                <div class="rating">{{ $evaluation->rating ?? '-' }}</div>
                            </div>
                            <a href="{{ route('staff.evaluations.show', $evaluation) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card evaluation-card">
                <div class="card-body py-5 text-center text-muted">No evaluations available yet.</div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $evaluations->links() }}
</div>
@endsection
