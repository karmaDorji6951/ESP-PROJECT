@extends('layouts.app')

@section('title', 'Task: ' . $task->title)
@section('page_title', 'Task Details')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $task->title }}</h5>
            <a href="{{ route('staff.tasks.index') }}" class="btn btn-outline-secondary btn-sm">Back to Tasks</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Task Details -->
    <div class="col-lg-8">
        <div class="card card-soft">
            <div class="card-body">
                <h6 class="mb-3">Description</h6>
                <p class="text-muted">{{ $task->description ?? 'No description provided' }}</p>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <span class="badge {{ 
                            $task->status === 'Completed' ? 'bg-success' : 
                            ($task->status === 'In Progress' ? 'bg-info' : 'bg-danger') 
                        }} fs-6">
                            {{ $task->status }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Priority</h6>
                        <span class="badge {{ 
                            $task->priority === 'High' ? 'bg-danger' : 
                            ($task->priority === 'Medium' ? 'bg-warning' : 'bg-secondary') 
                        }} fs-6">
                            {{ $task->priority ?? 'Not Set' }}
                        </span>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Created Date</h6>
                        <p>{{ $task->created_at?->format('Y-m-d H:i') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Deadline</h6>
                        <p>{{ $task->deadline?->format('Y-m-d') ?? 'No deadline' }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Assigned By</h6>
                        <p>{{ $task->assigner?->name ?? 'Unknown' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Task ID</h6>
                        <p><code>{{ $task->id }}</code></p>
                    </div>
                </div>
            </div>
        </div>

        @if($task->status === 'Completed')
            <div class="card card-soft mt-4">
                <div class="card-header bg-white fw-semibold">Evaluation</div>
                <div class="card-body">
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

                        <hr>

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

                        <hr>

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

    <!-- Task Summary -->
    <div class="col-lg-4">
        <div class="card card-soft">
            <div class="card-header bg-white fw-semibold">Quick Info</div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status</small>
                    <div class="mt-2">
                        <div class="progress" role="progressbar" style="height: 8px;">
                            <div class="progress-bar {{ 
                                $task->status === 'Completed' ? 'bg-success' : 
                                ($task->status === 'In Progress' ? 'bg-info' : 'bg-danger') 
                            }}" style="width: {{ 
                                $task->status === 'Completed' ? '100' : 
                                ($task->status === 'In Progress' ? '50' : '0') 
                            }}%"></div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted">Time Remaining</small>
                    @if($task->deadline)
                        @if($task->deadline->isPast())
                            <div class="alert alert-danger mt-2 mb-0 py-2">
                                <small>⏰ Overdue by {{ $task->deadline->diffInDays(now()) }} days</small>
                            </div>
                        @else
                            <div class="alert alert-warning mt-2 mb-0 py-2">
                                <small>⏳ {{ $task->deadline->diffInDays(now()) }} days remaining</small>
                            </div>
                        @endif
                    @else
                        <small class="text-muted">No deadline set</small>
                    @endif
                </div>

                <hr>

                <div class="d-grid gap-2">
                    @if($task->status !== 'Completed')
                        <button type="button" class="btn btn-success" onclick="startTask({{ $task->id }})">
                            🚀 Perform Work
                        </button>
                    @endif
                    <a href="{{ route('staff.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                        Back to All Tasks
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Task Performance Modal -->
<div class="modal fade" id="performWorkModal" tabindex="-1" aria-labelledby="performWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="performWorkModalLabel">
                    🚀 Perform Work: {{ $task->title }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Task Details:</strong><br>
                    {{ $task->description ?? 'No description provided' }}
                </div>
                
                <form id="performWorkForm">
                    <div class="mb-3">
                        <label for="workStatus" class="form-label fw-semibold">Work Status</label>
                        <select id="workStatus" class="form-select" required>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <small class="text-muted">Select the current status of your work</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="workNotes" class="form-label fw-semibold">Work Details <span class="text-danger">*</span></label>
                        <textarea id="workNotes" class="form-control" rows="4" placeholder="Describe what you've done, any challenges faced, and the current status of the work..." required style="border: 1px solid #ced4da;"></textarea>
                        <small class="text-muted">Please provide detailed information about your work progress</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photoEvidence" class="form-label fw-semibold">Evidence File (Optional)</label>
                        <input type="file" id="photoEvidence" class="form-control" style="border: 1px solid #ced4da;">
                        <small class="text-muted">Upload any file as evidence of your completed work (PDF, images, documents, max 10MB)</small>
                        <div id="filePreview" class="mt-2" style="display: none;">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <span id="fileIcon" style="font-size: 2rem;">📄</span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" id="fileName">Selected file</div>
                                        <small class="text-muted" id="fileSize">File size</small>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeFile()">Remove File</button>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmWork" required>
                            <label class="form-check-label" for="confirmWork">
                                I confirm that I have honestly reported my work progress
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="submitWorkBtn" class="btn btn-success" onclick="submitWork()">
                    Submit Work
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function startTask(taskId) {
    // Show confirmation modal
    const modal = new bootstrap.Modal(document.getElementById('performWorkModal'));
    modal.show();
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

function submitWork() {
    const taskId = '{{ $task->id }}';
    const workNotes = document.getElementById('workNotes').value;
    const workStatus = document.getElementById('workStatus').value;
    const photoFile = document.getElementById('photoEvidence').files[0];
    
    if (!workNotes.trim()) {
        alert('Please provide work details.');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitWorkBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    
    // Create FormData for file upload
    const formData = new FormData();
    formData.append('notes', workNotes);
    formData.append('status', workStatus);
    if (photoFile) {
        formData.append('photo_evidence', photoFile);
    }
    
    // Submit the work
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
            // Close modal and refresh page
            bootstrap.Modal.getInstance(document.getElementById('performWorkModal')).hide();
            location.reload();
        } else {
            // Handle validation errors
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

// File preview functionality
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
                // Check file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    fileInput.value = '';
                    return;
                }
                
                // Show file info
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                
                // Set appropriate icon based on file type
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
    return '📎'; // Default icon for other files
}

function removeFile() {
    document.getElementById('photoEvidence').value = '';
    document.getElementById('filePreview').style.display = 'none';
}
</script>
@endpush
