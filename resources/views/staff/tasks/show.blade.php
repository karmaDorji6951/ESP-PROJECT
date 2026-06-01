@extends('layouts.app')

@section('title', 'Task: ' . $task->title)
@section('page_title', 'Task Details')

@push('styles')
<style>
    .task-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .task-hero .title-wrap {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .task-avatar {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, #1D9E75 0%, #0F2044 100%);
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 700;
        font-size: 20px;
        box-shadow: 0 12px 24px rgba(15, 32, 68, 0.14);
    }

    .task-meta {
        color: #6b7280;
    }

    .card-soft {
        border-radius: 18px;
        border: 1px solid rgba(15, 32, 68, 0.06);
        box-shadow: 0 12px 30px rgba(15, 32, 68, 0.06);
        overflow: hidden;
    }

    .card-soft .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(15, 32, 68, 0.06);
    }

    .section-divider {
        height: 1px;
        background: rgba(15, 32, 68, 0.06);
        margin: 18px 0;
    }

    .badge.custom-priority {
        padding: 0.45rem 0.7rem;
        border-radius: 12px;
        font-weight: 600;
    }

    .quick-info .time-remaining {
        display: block;
        padding: 12px 14px;
        border-radius: 10px;
        background: linear-gradient(90deg, #fef3c7, #fff7ed);
        color: #7c2d12;
        font-weight: 600;
        border-left: 4px solid #d97706;
    }

    .quick-info .time-remaining.overdue {
        background: linear-gradient(90deg, #fee2e2, #fff1f2);
        color: #991b1b;
        border-left-color: #dc2626;
    }

    .quick-info .btn-perform {
        background: linear-gradient(135deg, #1D9E75 0%, #16638a 100%);
        color: #fff;
        border: none;
        box-shadow: 0 8px 18px rgba(29, 158, 117, 0.18);
    }

    .quick-info .btn-perform:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .work-modal .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 28px 60px rgba(15, 32, 68, 0.28);
    }

    .work-modal .modal-header {
        background: linear-gradient(135deg, #0F2044 0%, #1D9E75 150%);
        color: #fff;
        border-bottom: 0;
        padding: 18px 22px;
    }

    .work-modal .modal-body {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        padding: 22px;
    }

    .work-modal .modal-footer {
        background: #fff;
        border-top: 1px solid rgba(15, 32, 68, 0.06);
        padding: 16px 22px;
    }

    .task-summary-strip {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .summary-pill {
        background: #fff;
        border: 1px solid rgba(15, 32, 68, 0.08);
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 10px 20px rgba(15, 32, 68, 0.04);
    }

    .summary-pill .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .summary-pill .value {
        font-weight: 700;
        color: #111827;
    }

    .form-section {
        background: #fff;
        border: 1px solid rgba(15, 32, 68, 0.08);
        border-radius: 16px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .form-section .section-title {
        font-weight: 700;
        color: #0F2044;
        margin-bottom: 8px;
    }

    .form-help {
        color: #6b7280;
        font-size: 0.85rem;
        margin-top: 8px;
    }

    .evidence-preview {
        margin-top: 12px;
        border-radius: 14px;
        overflow: hidden;
        border: 1px dashed rgba(15, 32, 68, 0.18);
        background: #f8fafc;
    }

    .evidence-preview .preview-body {
        padding: 12px 14px;
    }

    .check-area {
        background: linear-gradient(90deg, #ecfdf5 0%, #f0fdf4 100%);
        border: 1px solid rgba(29, 158, 117, 0.18);
        border-radius: 14px;
        padding: 14px 16px;
    }

    .info-box {
        background: #f8fafc;
        border: 1px solid rgba(15, 32, 68, 0.06);
        border-radius: 14px;
        padding: 14px;
        height: 100%;
    }

    .info-label {
        color: #6b7280;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }

    .info-value {
        font-weight: 600;
        color: #1f2937;
    }

    @media (max-width: 992px) {
        .task-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .task-avatar {
            width: 48px;
            height: 48px;
            font-size: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="task-hero">
    <div class="title-wrap">
        <div class="task-avatar">{{ strtoupper(substr($task->title, 0, 1)) }}</div>
        <div>
            <div class="text-uppercase fw-semibold task-meta" style="letter-spacing: .12em; font-size: 11px;">Task Details</div>
            <h3 class="mb-1 fw-bold">{{ $task->title }}</h3>
            <div class="task-meta">Assigned by {{ $task->assigner?->name ?? 'Unknown' }} · Task #{{ $task->id }}</div>
        </div>
    </div>
    <a href="{{ route('staff.tasks.index') }}" class="btn btn-outline-secondary btn-sm px-3">Back to Tasks</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-body p-4 p-lg-4">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                    <div>
                        <div class="text-uppercase fw-semibold task-meta" style="letter-spacing: .12em; font-size: 11px;">Description</div>
                        <p class="mb-0 text-muted">{{ $task->description ?? 'No description provided' }}</p>
                    </div>
                    <span class="badge custom-priority {{ $task->priority === 'High' ? 'bg-danger' : ($task->priority === 'Medium' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                        {{ $task->priority ?? 'Not Set' }}
                    </span>
                </div>

                <div class="section-divider"></div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">Status</div>
                            <span class="badge fs-6 {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'In Progress' ? 'bg-info' : 'bg-danger') }}">
                                {{ $task->status }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">Priority</div>
                            <span class="badge fs-6 custom-priority {{ $task->priority === 'High' ? 'bg-danger' : ($task->priority === 'Medium' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                {{ $task->priority ?? 'Not Set' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">Created Date</div>
                            <div class="info-value">{{ $task->created_at?->format('Y-m-d H:i') ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">Deadline</div>
                            <div class="info-value">{{ $task->deadline?->format('Y-m-d') ?? 'No deadline' }}</div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">Assigned By</div>
                            <div class="info-value">{{ $task->assigner?->name ?? 'Unknown' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="info-label">Task ID</div>
                            <div class="info-value"><code>{{ $task->id }}</code></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($task->status === 'Completed')
            <div class="card card-soft mt-4">
                <div class="card-header bg-white fw-semibold">Evaluation</div>
                <div class="card-body p-4">
                    @php
                        $evaluation = $task->evaluation;
                        $isMyEvaluation = $evaluation && (int) ($evaluation->staff_user_id ?? 0) === (int) auth()->id();
                        $criteria = (array) ($evaluation->criteria ?? []);
                    @endphp

                    @if($isMyEvaluation)
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Grade</div>
                                <div class="fs-5 fw-semibold">{{ $evaluation->grade }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small mb-1">Rating</div>
                                <div class="fs-5 fw-semibold">{{ $evaluation->rating }}/5</div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Work Quality</div>
                                <div class="fw-semibold">{{ $criteria['quality'] ?? '—' }}/5</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Timeliness</div>
                                <div class="fw-semibold">{{ $criteria['timeliness'] ?? '—' }}/5</div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Evidence Quality</div>
                                <div class="fw-semibold">{{ $criteria['evidence'] ?? '—' }}/5</div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <div class="mb-2">
                            <div class="text-muted small mb-1">Remarks</div>
                            <div>{{ $evaluation->remarks ?: 'No remarks provided.' }}</div>
                        </div>

                        <div class="text-muted small">
                            Evaluated by {{ $evaluation->evaluator?->name ?? 'Supervisor' }}
                            @if($evaluation->evaluated_at)
                                · {{ $evaluation->evaluated_at->format('Y-m-d H:i') }}
                            @endif
                        </div>
                    @else
                        <div class="text-muted">Not evaluated yet.</div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card card-soft quick-info">
            <div class="card-header bg-white fw-semibold">Quick Info</div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-semibold text-uppercase" style="letter-spacing: .08em;">Status</small>
                        <small class="text-muted">Progress</small>
                    </div>
                    <div class="progress" role="progressbar" style="height: 10px; border-radius: 999px; background: #eef2f7;">
                        <div class="progress-bar {{ $task->status === 'Completed' ? 'bg-success' : ($task->status === 'In Progress' ? 'bg-info' : 'bg-danger') }}" style="width: {{ $task->status === 'Completed' ? '100' : ($task->status === 'In Progress' ? '55' : '15') }}%; border-radius: 999px;"></div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <div class="mb-3">
                    <small class="text-muted fw-semibold text-uppercase" style="letter-spacing: .08em;">Time Remaining</small>
                    @if($task->deadline)
                        @if($task->deadline->isPast())
                            <div class="time-remaining overdue mt-2">
                                ⏰ Overdue by {{ $task->deadline->diffInDays(now()) }} days
                            </div>
                        @else
                            <div class="time-remaining mt-2">
                                ⏳ {{ $task->deadline->diffInDays(now()) }} days remaining
                            </div>
                        @endif
                    @else
                        <div class="time-remaining mt-2" style="background: #f8fafc; color: #475569; border-left-color: #94a3b8;">No deadline set</div>
                    @endif
                </div>

                <div class="section-divider"></div>

                <div class="d-grid gap-2">
                    @if($task->status !== 'Completed')
                        <button type="button" class="btn btn-perform btn-lg" onclick="startTask({{ $task->id }})">
                            🚀 Perform Work
                        </button>
                    @endif
                    <a href="{{ route('staff.tasks.index') }}" class="btn btn-outline-secondary">
                        Back to All Tasks
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="modal fade work-modal" id="performWorkModal" tabindex="-1" aria-labelledby="performWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="text-uppercase fw-semibold" style="font-size: 11px; letter-spacing: 0.12em; opacity: 0.85;">Work Submission</div>
                    <h5 class="modal-title mb-0" id="performWorkModalLabel">🚀 Perform Work: {{ $task->title }}</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="task-summary-strip">
                    <div class="summary-pill">
                        <div class="label">Status</div>
                        <div class="value">{{ $task->status }}</div>
                    </div>
                    <div class="summary-pill">
                        <div class="label">Deadline</div>
                        <div class="value">{{ $task->deadline?->format('Y-m-d') ?? 'No deadline' }}</div>
                    </div>
                    <div class="summary-pill">
                        <div class="label">Priority</div>
                        <div class="value">{{ $task->priority ?? 'Not Set' }}</div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-title">Task Details</div>
                    <div class="text-muted">{{ $task->description ?? 'No description provided' }}</div>
                </div>

                <form id="performWorkForm">
                    <div class="form-section">
                        <div class="section-title">Update Work Status</div>
                        <label for="workStatus" class="form-label fw-semibold">Current Status</label>
                        <select id="workStatus" class="form-select" required>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <div class="form-help">Choose the status that best reflects your current progress.</div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">Work Summary</div>
                        <label for="workNotes" class="form-label fw-semibold">Work Details <span class="text-danger">*</span></label>
                        <textarea id="workNotes" class="form-control" rows="5" placeholder="Write a short update: what was done, what remains, issues encountered, and any next steps..." required></textarea>
                        <div class="form-help">Give enough detail for the reviewer to understand the progress without asking follow-up questions.</div>
                    </div>

                    <div class="form-section">
                        <div class="section-title">Evidence</div>
                        <label for="photoEvidence" class="form-label fw-semibold">Evidence File (Optional)</label>
                        <input type="file" id="photoEvidence" class="form-control">
                        <div class="form-help">PDFs, images, and documents are supported. Maximum file size is 10MB.</div>
                        <div id="filePreview" class="evidence-preview" style="display: none;">
                            <div class="preview-body d-flex align-items-center gap-3">
                                <div class="fs-2" id="fileIcon">📄</div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold" id="fileName">Selected file</div>
                                    <small class="text-muted" id="fileSize">File size</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="check-area mb-2">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="confirmWork" required>
                            <label class="form-check-label fw-semibold" for="confirmWork">
                                I confirm that I have honestly reported my work progress
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitWorkBtn" class="btn btn-success px-4" onclick="submitWork()">Submit Work</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function startTask(taskId) {
    const modal = new bootstrap.Modal(document.getElementById('performWorkModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('perform') === 'true') {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('performWorkModal'));
            if (modal) {
                modal.show();
            }
        }, 500);
    }
});

function submitWork() {
    const taskId = '{{ $task->id }}';
    const workNotes = document.getElementById('workNotes').value;
    const workStatus = document.getElementById('workStatus').value;
    const photoFile = document.getElementById('photoEvidence').files[0];

    if (!workNotes.trim()) {
        alert('Please provide work details.');
        return;
    }

    const submitBtn = document.getElementById('submitWorkBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    const formData = new FormData();
    formData.append('notes', workNotes);
    formData.append('status', workStatus);
    if (photoFile) {
        formData.append('photo_evidence', photoFile);
    }

    fetch(`/staff/tasks/${taskId}/perform`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('performWorkModal')).hide();
            location.reload();
        } else {
            if (data.errors) {
                let errorMessage = 'Please fix the following errors:\n';
                for (const [field, errors] of Object.entries(data.errors)) {
                    errorMessage += `\n• ${errors.join(', ')}`;
                }
                alert(errorMessage);
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting your work.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit Work';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('photoEvidence');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileIcon = document.getElementById('fileIcon');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    fileInput.value = '';
                    return;
                }

                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileIcon.textContent = getFileIcon(file.type);
                filePreview.style.display = 'block';
            }
        });
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getFileIcon(fileType) {
    if (fileType.startsWith('image/')) return '🖼️';
    if (fileType.startsWith('video/')) return '🎥';
    if (fileType.startsWith('audio/')) return '🎵';
    if (fileType.includes('pdf')) return '📄';
    if (fileType.includes('word') || fileType.includes('document')) return '📝';
    if (fileType.includes('excel') || fileType.includes('spreadsheet')) return '📊';
    if (fileType.includes('powerpoint') || fileType.includes('presentation')) return '📽️';
    if (fileType.includes('zip') || fileType.includes('rar') || fileType.includes('7z')) return '🗜️';
    if (fileType.includes('text')) return '📃';
    return '📎';
}

function removeFile() {
    document.getElementById('photoEvidence').value = '';
    document.getElementById('filePreview').style.display = 'none';
}
</script>
@endpush
