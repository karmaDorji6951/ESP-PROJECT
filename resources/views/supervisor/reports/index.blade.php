@extends('layouts.app')

@section('page_title', 'Report')
@section('topbar_title', 'Report')

@section('content')
<div class="reports-container">
    @php
        $teamEmployees = $teamEmployees ?? collect();
    @endphp
    <!-- Reports Header -->
    <div class="reports-header app-page-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="app-page-hero-kicker mb-2">Supervisor Workspace</div>
                <h2 class="app-page-hero-title mb-2">Report</h2>
                <p class="app-page-hero-subtitle">Generate comprehensive reports for your team.</p>
            </div>
        </div>
    </div>

    <!-- Report Generation Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4">
            <h5 class="card-title mb-0">Team Report Configuration</h5>
        </div>
        <div class="card-body">
            <form id="reportForm" method="POST" action="{{ route('supervisor.reports.generate') }}">
                @csrf
                <div class="row g-3">
                    <!-- Report Type -->
                    <div class="col-md-6">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type" required onchange="updateFormFields()">
                            <option value="">Select Report Type</option>
                            <option value="team_attendance">Attendance</option>
                            <option value="team_tasks">Tasks</option>
                            <option value="team_leaves">Leaves</option>
                            <option value="team_performance">Performance</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-6">
                        <label for="date_range" class="form-label">Date Range</label>
                        <select class="form-select" id="date_range" name="date_range" required onchange="toggleCustomDates()">
                            <option value="">Select Date Range</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="last_3_months">Last 3 Months</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <!-- Custom Date Range -->
                    <div class="col-md-6" id="custom_dates" style="display: none;">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>

                    <div class="col-md-6" id="custom_dates_end" style="display: none;">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>

                    <!-- Member Filter -->
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Member (Optional)</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">All Members</option>
                            @foreach($teamEmployees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->user->name ?? $employee->name ?? 'N/A' }}@if(!empty($employee->employee_id)) ({{ $employee->employee_id }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Export Format -->
                    <div class="col-md-6">
                        <label for="format" class="form-label">Export Format</label>
                        <select class="form-select" id="format" name="format" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                            </button>
                            <button type="button" id="generateBtn" class="btn btn-primary" onclick="generateReport()">
                                <i class="bi bi-download me-2"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="quick-reports-grid mb-4">
        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('team_attendance', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h6 class="fw-bold">Attendance</h6>
                <p class="text-muted small mb-0">This month's attendance</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('team_tasks', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-list-task"></i>
                </div>
                <h6 class="fw-bold">Tasks</h6>
                <p class="text-muted small mb-0">This month's tasks</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('team_leaves', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h6 class="fw-bold">Leaves</h6>
                <p class="text-muted small mb-0">This month's leaves</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('team_performance', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h6 class="fw-bold">Performance</h6>
                <p class="text-muted small mb-0">Performance metrics</p>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4">
            <h5 class="card-title mb-0">Recent Reports</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Report Type</th>
                            <th>Date Range</th>
                            <th>Generated At</th>
                            <th>Format</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentReports as $report)
                            <tr>
                                <td><code>{{ $report->report_id }}</code></td>
                                <td>
                                    @switch($report->report_type)
                                        @case('team_attendance')
                                            <span class="badge bg-primary">Attendance</span>
                                        @break
                                        @case('team_tasks')
                                            <span class="badge bg-info">Tasks</span>
                                        @break
                                        @case('team_leaves')
                                            <span class="badge bg-warning">Leaves</span>
                                        @break
                                        @case('team_performance')
                                            <span class="badge bg-success">Performance</span>
                                        @break
                                        @default
                                            <span class="badge bg-secondary">{{ $report->report_type }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $report->period_label }}</td>
                                <td>{{ $report->created_at->format('Y-m-d H:i') }}</td>
                                <td><span class="badge bg-{{ $report->format === 'pdf' ? 'success' : 'primary' }}">{{ strtoupper($report->format) }}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteReport({{ $report->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="color:#666; padding: 20px; text-align: center;">No reports generated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Report Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="previewContent">
                    <!-- Preview content will be loaded here -->
                    <div class="text-center py-4">
                        <i class="bi bi-hourglass-split fs-1 text-muted"></i>
                        <p class="text-muted">Loading preview...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="generateReport()">Generate Report</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.reports-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.reports-header {
    margin-bottom: 24px;
}

.quick-reports-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1.5rem;
}

.quick-report-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
    position: relative;
    min-width: 0;
}

.quick-report-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    z-index: 2;
}

.quick-report-icon {
    width: 60px;
    height: 60px;
    background: var(--gradient-dashboard);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 24px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    background-color: #f8f9fa;
    border-radius: 6px;
}

.activity-icon {
    font-size: 20px;
    width: 32px;
    height: 32px;
    background: var(--gradient-dashboard);
    color: #f5f1e8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
}

.activity-description {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 4px;
}

.activity-time {
    color: #6c757d;
    font-size: 12px;
}

.no-activity {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

@media (max-width: 768px) {
    .reports-container {
        padding: 10px;
    }
    
    .reports-header {
        padding: 16px;
    }
    
    .reports-header h2 {
        font-size: 1.5rem;
    }

    .quick-reports-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

@media (max-width: 1200px) {
    .quick-reports-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleCustomDates() {
    const dateRange = document.getElementById('date_range').value;
    const customDates = document.getElementById('custom_dates');
    const customDatesEnd = document.getElementById('custom_dates_end');
    
    if (dateRange === 'custom') {
        customDates.style.display = 'block';
        customDatesEnd.style.display = 'block';
        document.getElementById('start_date').required = true;
        document.getElementById('end_date').required = true;
    } else {
        customDates.style.display = 'none';
        customDatesEnd.style.display = 'none';
        document.getElementById('start_date').required = false;
        document.getElementById('end_date').required = false;
    }
}

function updateFormFields() {
    const reportType = document.getElementById('report_type').value;
    // You can add dynamic form field updates based on report type here
}

function resetForm() {
    document.getElementById('reportForm').reset();
    toggleCustomDates();
}

function quickReport(type, dateRange) {
    document.getElementById('report_type').value = type;
    document.getElementById('date_range').value = dateRange;
    document.getElementById('format').value = 'pdf';
    toggleCustomDates();
    generateReport();
}

function showPreview() {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    // Simulate loading preview data
    setTimeout(() => {
        document.getElementById('previewContent').innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Report preview will show a sample of the data that will be included in your report.
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Area</th>
                            <th>Period</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>Block 1 - Corridor</td>
                            <td>May 2026</td>
                            <td><span class="badge bg-success">Present</span></td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>Block 2 - Parking</td>
                            <td>May 2026</td>
                            <td><span class="badge bg-warning">Leave</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
    }, 1000);
}

function generateReport() {
    const form = document.getElementById('reportForm');
    const formData = new FormData(form);
    
    // Validate form before submission
    const reportType = formData.get('report_type');
    const dateRange = formData.get('date_range');
    const format = formData.get('format');
    
    if (!reportType || !dateRange || !format) {
        showNotification('Please fill in all required fields.', 'error');
        return;
    }
    
    // Only include custom date fields if custom range is selected
    if (dateRange !== 'custom') {
        formData.delete('start_date');
        formData.delete('end_date');
    } else {
        const startDate = formData.get('start_date');
        const endDate = formData.get('end_date');
        if (!startDate || !endDate) {
            showNotification('Please select both start and end dates for custom range.', 'error');
            return;
        }
    }
    
    // Show loading state
    const submitBtn = document.getElementById('generateBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating...';
    submitBtn.disabled = true;
    
    fetch('{{ route("supervisor.reports.generate") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        }
    })
    .then(async response => {
        if (!response.ok) {
            // Try to get error message from response
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error('Server returned ' + response.status + ': ' + errorText);
        }
        
        const contentType = response.headers.get('content-type');
        console.log('Response content type:', contentType);
        
        if (format === 'pdf' && !contentType.includes('pdf')) {
            const errorText = await response.text();
            console.error('Expected PDF but got:', contentType, errorText);
            throw new Error('Server did not return a PDF file');
        }
        
        return response.blob();
    })
    .then(blob => {
        console.log('Blob size:', blob.size);
        
        if (blob.size === 0) {
            throw new Error('Received empty file');
        }
        
        // Create a URL for the blob and open in a new window/tab
        const url = window.URL.createObjectURL(blob);
        const newWindow = window.open(url, '_blank');
        
        if (newWindow) {
            newWindow.onload = () => {
                // Revoke the object URL after the new window has loaded it
                window.URL.revokeObjectURL(url);
            };
        } else {
            // Fallback for browsers that block pop-ups
            const a = document.createElement('a');
            a.href = url;
            a.download = 'report.' + formData.get('format');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showNotification('Pop-up blocked. Report downloaded instead.', 'warning');
        }
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        showNotification('Report generated successfully!', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        showNotification('Error generating report: ' + error.message, 'error');
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function deleteReport(reportId) {
    if (!confirm('Are you sure you want to delete this report?')) {
        return;
    }
    
    fetch(`/supervisor/reports/${reportId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {
        if (response.ok) {
            showNotification('Report deleted successfully!', 'success');
            location.reload();
        } else {
            throw new Error('Failed to delete report');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting report. Please try again.', 'error');
    });
}
</script>
@endpush
