@extends('layouts.app')

@section('page_title', 'Evaluate Task')
@section('title', 'Evaluate: ' . $task->title)

@section('content')
<div class="app-page-hero mb-4">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
        <div>
            <div class="app-page-hero-kicker mb-2">Supervisor Workspace</div>
            <h1 class="app-page-hero-title mb-2">Evaluate Task</h1>
            <p class="app-page-hero-subtitle mb-0">
                {{ $task->title }} — Staff: <span class="fw-semibold">{{ $staffUser->name }}</span>
                @if(isset($employee) && $employee)
                    <span class="ms-2">({{ $employee->role_title }} · {{ $employee->area }})</span>
                @endif
                @if($evaluation)
                    <span class="badge text-bg-light text-dark ms-2">Evaluation exists</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('supervisor.tasks.show', $task) }}" class="btn btn-light app-page-hero-action">Back</a>
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4">
    <div class="col-12">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Submission Evidence</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small mb-2">Submitted Notes</div>
                        <div class="p-3 rounded border bg-light" style="white-space: pre-wrap;">
                            {{ $submission->submission_notes ?: 'No notes provided.' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small mb-2">Evidence File</div>
                        @if($submission->photo_url)
                            <div class="d-flex flex-column gap-2">
                                <a href="{{ $submission->photo_url }}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm align-self-start">Open Evidence</a>

                                @php
                                    $path = (string) ($submission->photo_evidence ?? '');
                                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                                @endphp

                                @if($isImage)
                                    <img src="{{ $submission->photo_url }}" alt="Evidence" class="img-fluid rounded border" style="max-height: 380px; object-fit: contain;">
                                @else
                                    <div class="alert alert-secondary py-2 mb-0">
                                        <div class="small text-muted mb-0">Preview not available for this file type.</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-secondary py-2 mb-0">
                                <div class="small text-muted mb-0">No evidence uploaded.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Evaluation Form</div>
            <div class="card-body">
                <div class="text-muted small mb-3">
                    Select at least one criterion. Rating and grade preview update automatically.
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('supervisor.tasks.evaluation.store', $task) }}">
                    @csrf

                    @php
                        $criteria = (array) ($evaluation->criteria ?? []);
                    @endphp

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Work Quality</label>
                            <select name="quality" class="form-select">
                                <option value="">--</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ (int) old('quality', $criteria['quality'] ?? 0) === $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="form-text">1 (low) → 5 (high)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Timeliness</label>
                            <select name="timeliness" class="form-select">
                                <option value="">--</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ (int) old('timeliness', $criteria['timeliness'] ?? 0) === $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="form-text">1 (late) → 5 (on time)</div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Evidence Quality</label>
                            <select name="evidence" class="form-select">
                                <option value="">--</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ (int) old('evidence', $criteria['evidence'] ?? 0) === $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="form-text">1 (weak) → 5 (strong)</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded border bg-light h-100">
                                <div class="text-muted small">Rating (Auto)</div>
                                <div class="fw-bold fs-4" id="auto_rating_display">--</div>
                                <input type="hidden" name="rating" id="auto_rating" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded border bg-light h-100">
                                <div class="text-muted small">Grade (Auto)</div>
                                <div class="fw-bold fs-4" id="auto_grade_display">--</div>
                                <input type="hidden" name="grade" id="auto_grade" value="">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="4" placeholder="Write remarks (optional)">{{ old('remarks', $evaluation->remarks ?? '') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Save Evaluation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const qualityEl = document.querySelector('select[name="quality"]');
    const timelinessEl = document.querySelector('select[name="timeliness"]');
    const evidenceEl = document.querySelector('select[name="evidence"]');

    const ratingHidden = document.getElementById('auto_rating');
    const ratingDisplay = document.getElementById('auto_rating_display');
    const gradeHidden = document.getElementById('auto_grade');
    const gradeDisplay = document.getElementById('auto_grade_display');

    function toNumber(value) {
        const n = Number(value);
        return Number.isFinite(n) && n > 0 ? n : null;
    }

    function compute() {
        const scores = [toNumber(qualityEl.value), toNumber(timelinessEl.value), toNumber(evidenceEl.value)].filter(v => v !== null);

        if (scores.length === 0) {
            ratingHidden.value = '';
            gradeHidden.value = '';
            ratingDisplay.textContent = '--';
            gradeDisplay.textContent = '--';
            return;
        }

        const avg = scores.reduce((a, b) => a + b, 0) / scores.length;
        const rating = Math.min(5, Math.max(1, Math.round(avg)));
        let grade = 'F';
        if (avg >= 4.5) grade = 'A';
        else if (avg >= 3.5) grade = 'B';
        else if (avg >= 2.5) grade = 'C';
        else if (avg >= 1.5) grade = 'D';
        else if (avg >= 1.0) grade = 'E';

        ratingHidden.value = String(rating);
        gradeHidden.value = grade;
        ratingDisplay.textContent = String(rating);
        gradeDisplay.textContent = grade;
    }

    qualityEl.addEventListener('change', compute);
    timelinessEl.addEventListener('change', compute);
    evidenceEl.addEventListener('change', compute);

    compute();
});
</script>
@endpush
@endsection
