@extends('layouts.app')

@section('title', 'Evaluation Details')
@section('page_title', 'Evaluation Details')

@section('content')
<div class="container py-4" style="max-width: 1100px;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Evaluation Details</h1>
            <div class="text-muted">Full review for {{ optional($evaluation->task)->title ?? 'this task' }}</div>
        </div>
        <a href="{{ route('staff.evaluations.index') }}" class="btn btn-outline-secondary">Back to Evaluations</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-semibold">Task Overview</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Task</div>
                            <div class="fw-semibold">{{ optional($evaluation->task)->title ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Status</div>
                            <div class="fw-semibold">{{ optional($evaluation->task)->status ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Assigned By</div>
                            <div class="fw-semibold">{{ optional(optional($evaluation->task)->assigner)->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Evaluated By</div>
                            <div class="fw-semibold">{{ optional($evaluation->evaluator)->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Evaluated At</div>
                            <div class="fw-semibold">{{ $evaluation->evaluated_at?->format('M j, Y H:i') ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase">Submission</div>
                            <div class="fw-semibold">{{ $evaluation->submission?->submitted_at?->format('M j, Y H:i') ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">Score Breakdown</div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $criteria = (array) ($evaluation->criteria ?? []);
                        @endphp
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <div class="text-muted small text-uppercase">Quality</div>
                                <div class="fs-3 fw-bold">{{ $criteria['quality'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <div class="text-muted small text-uppercase">Timeliness</div>
                                <div class="fs-3 fw-bold">{{ $criteria['timeliness'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 bg-light border h-100">
                                <div class="text-muted small text-uppercase">Evidence</div>
                                <div class="fs-3 fw-bold">{{ $criteria['evidence'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border" style="background: linear-gradient(135deg, #ecfeff 0%, #f0fdfa 100%);">
                                <div class="text-muted small text-uppercase">Average Rating</div>
                                <div class="fs-2 fw-bold">{{ $evaluation->rating }}/5</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border" style="background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);">
                                <div class="text-muted small text-uppercase">Grade</div>
                                <div class="fs-2 fw-bold">{{ $evaluation->grade }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded-3 border" style="background: linear-gradient(135deg, #fefce8 0%, #fffdf0 100%);">
                                <div class="text-muted small text-uppercase">Evaluator Remarks</div>
                                <div class="fw-semibold">{{ $evaluation->remarks ?? 'No remarks provided.' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-semibold">Task Submission</div>
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Submitted By</div>
                    <div class="fw-semibold mb-3">{{ optional(optional($evaluation->submission)->submitter)->name ?? 'N/A' }}</div>

                    <div class="text-muted small text-uppercase mb-1">Submission Time</div>
                    <div class="fw-semibold mb-3">{{ $evaluation->submission?->submitted_at?->format('M j, Y H:i') ?? 'N/A' }}</div>

                    <div class="text-muted small text-uppercase mb-1">Task Deadline</div>
                    <div class="fw-semibold">{{ optional($evaluation->task)->deadline?->format('M j, Y') ?? 'N/A' }}</div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">Task Notes</div>
                <div class="card-body">
                    <div class="text-muted small text-uppercase mb-1">Description</div>
                    <div class="mb-3">{{ optional($evaluation->task)->description ?? 'No description provided.' }}</div>

                    <div class="text-muted small text-uppercase mb-1">Assignment Type</div>
                    <div class="fw-semibold mb-3">{{ optional($evaluation->task)->assignment_type ?? 'N/A' }}</div>

                    <div class="text-muted small text-uppercase mb-1">Location</div>
                    <div class="fw-semibold">{{ optional($evaluation->task)->location ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
