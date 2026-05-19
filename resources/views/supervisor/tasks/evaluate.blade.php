@extends('layouts.app')

@section('page_title', 'Evaluate Task')
@section('title', 'Evaluate: ' . $task->title)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <div>
                <h5 class="mb-1">Evaluate Task</h5>
                <div class="text-muted small">
                    {{ $task->title }} — Staff: {{ $staffUser->name }}
                    @if(isset($employee) && $employee)
                        <span class="ms-2">({{ $employee->role_title }} · {{ $employee->department }})</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('supervisor.tasks.show', $task) }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
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
    <div class="col-lg-7">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Submission Evidence</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Submitted Notes</div>
                    <div>{{ $submission->submission_notes ?: 'No notes provided.' }}</div>
                </div>

                <hr>

                <div class="mb-0">
                    <div class="text-muted small mb-1">Evidence File</div>
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
                                <div class="text-muted small">Preview not available for this file type.</div>
                            @endif
                        </div>
                    @else
                        <div class="text-muted small">No evidence uploaded.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Evaluation Form</div>
            <div class="card-body">
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

                    <div class="mb-3">
                        <label class="form-label">Work Quality (1-5)</label>
                        <select name="quality" class="form-select">
                            <option value="">--</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ (int) old('quality', $criteria['quality'] ?? 0) === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Timeliness (1-5)</label>
                        <select name="timeliness" class="form-select">
                            <option value="">--</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ (int) old('timeliness', $criteria['timeliness'] ?? 0) === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Evidence Quality (1-5)</label>
                        <select name="evidence" class="form-select">
                            <option value="">--</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ (int) old('evidence', $criteria['evidence'] ?? 0) === $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rating (Auto)</label>
                        <select id="auto_rating_display" class="form-select" disabled>
                            <option value="">--</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        <input type="hidden" name="rating" id="auto_rating" value="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grade (Auto)</label>
                        <select id="auto_grade_display" class="form-select" disabled>
                            <option value="">--</option>
                            @foreach(['A','B','C','D','E','F'] as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="grade" id="auto_grade" value="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="4" placeholder="Write remarks (optional)">{{ old('remarks', $evaluation->remarks ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Save Evaluation</button>
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
            ratingDisplay.value = '';
            gradeDisplay.value = '';
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
        ratingDisplay.value = String(rating);
        gradeDisplay.value = grade;
    }

    qualityEl.addEventListener('change', compute);
    timelinessEl.addEventListener('change', compute);
    evidenceEl.addEventListener('change', compute);

    compute();
});
</script>
@endpush
@endsection
