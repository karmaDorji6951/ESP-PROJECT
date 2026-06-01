@csrf

<input type="hidden" id="evaluated_type" name="evaluated_type" value="{{ $evaluated_type ?? request('evaluated_type') ?? old('evaluated_type') }}">
<input type="hidden" id="evaluated_id" name="evaluated_id" value="{{ $evaluated_id ?? request('evaluated_id') ?? old('evaluated_id') }}">

@php
    $criteria = $criteria ?? ['communication' => 'Communication', 'quality' => 'Work Quality', 'timeliness' => 'Timeliness', 'initiative' => 'Initiative'];
@endphp

<div class="evaluation-criteria-grid">
    @foreach($criteria as $key => $label)
        <div class="evaluation-criterion card border-0 shadow-sm">
            <div class="card-body p-3 p-lg-4">
                <div class="criterion-title">{{ $label }}</div>
                <select name="scores[{{ $key }}]" class="form-select evaluation-select" required>
                    <option value="">Select rating</option>
                    @for($i=1;$i<=5;$i++)
                        <option value="{{ $i }}" {{ old('scores.'.$key) == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
    @endforeach
</div>

<div class="mb-3 mt-4">
    <label for="comments" class="form-label">Comments (optional)</label>
    <textarea name="comments" id="comments" class="form-control evaluation-textarea" rows="4" placeholder="Add any notes, observations, or context for this evaluation">{{ old('comments') }}</textarea>
</div>

<div class="mb-3">
    <label for="attachments" class="form-label">Attachment (optional)</label>
    <input type="file" name="attachments" id="attachments" class="form-control evaluation-file">
</div>

<div class="d-flex justify-content-end mt-4">
    <button class="btn btn-primary btn-lg px-4">Submit Evaluation</button>
</div>

<style>
.evaluation-criteria-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

.evaluation-criterion {
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid rgba(15, 32, 68, 0.08);
}

.criterion-title {
    font-weight: 700;
    color: #0f2044;
    margin-bottom: 10px;
}

.evaluation-select,
.evaluation-textarea,
.evaluation-file {
    border-radius: 12px;
    border-color: rgba(15, 32, 68, 0.16);
}

.evaluation-select:focus,
.evaluation-textarea:focus,
.evaluation-file:focus {
    border-color: #1d9e75;
    box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.12);
}

@media (max-width: 767.98px) {
    .evaluation-criteria-grid {
        grid-template-columns: 1fr;
    }
}
</style>
