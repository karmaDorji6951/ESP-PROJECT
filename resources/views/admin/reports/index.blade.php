@extends('layouts.app')

@section('page_title', 'Reports')
@section('topbar_title', 'System Reports')

@section('content')
<div class="reports-container">
    <!-- Reports Header -->
    <div class="reports-header">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Generate Reports</h2>
                <p class="text-muted mb-0">Create comprehensive reports for your ESP management system</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary" onclick="showPreview()">
                    <i class="bi bi-eye me-2"></i>Preview
                </button>
                <button class="btn btn-success" onclick="generateReport()">
                    <i class="bi bi-download me-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Report Generation Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4">
            <h5 class="card-title mb-0">Report Configuration</h5>
        </div>
        <div class="card-body">
            <form id="reportForm" method="POST" action="{{ route('admin.reports.generate') }}">
                @csrf
                <div class="row g-3">
                    <!-- Report Type -->
                    <div class="col-md-6">
                        <label for="report_type" class="form-label">Report Type</label>
                        <select class="form-select" id="report_type" name="report_type" required onchange="updateFormFields()">
                            <option value="">Select Report Type</option>
                            <option value="attendance">Attendance Report</option>
                            <option value="tasks">Tasks Report</option>
                            <option value="leaves">Leave Report</option>
                            <option value="performance">Performance Report</option>
                            <option value="employees">Employees Report</option>
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
                            <option value="last_6_months">Last 6 Months</option>
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

                    <!-- Department Filter -->
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department (Optional)</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach(($departments ?? collect()) as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Employee Filter -->
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Employee (Optional)</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach(($employees ?? collect()) as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->user->name ?? 'N/A' }} ({{ $employee->employee_id }})</option>
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
                            <button type="submit" class="btn btn-primary">
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
        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('attendance', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h6 class="fw-bold">Monthly Attendance</h6>
                <p class="text-muted small mb-0">This month's attendance report</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('tasks', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-list-task"></i>
                </div>
                <h6 class="fw-bold">Monthly Tasks</h6>
                <p class="text-muted small mb-0">This month's task report</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('leaves', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-calendar-x"></i>
                </div>
                <h6 class="fw-bold">Monthly Leaves</h6>
                <p class="text-muted small mb-0">This month's leave report</p>
            </div>
        </div>

        <div class="card border-0 shadow-sm h-100 quick-report-card" onclick="quickReport('performance', 'this_month')">
            <div class="card-body text-center">
                <div class="quick-report-icon mb-3">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h6 class="fw-bold">Performance</h6>
                <p class="text-muted small mb-0">Employee performance metrics</p>
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
                            <th>Report Type</th>
                            <th>Date Range</th>
                            <th>Generated By</th>
                            <th>Generated At</th>
                            <th>Format</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-primary">Attendance</span></td>
                            <td>May 2026</td>
                            <td>{{ auth()->user()->name }}</td>
                            <td>2026-05-05 17:30</td>
                            <td><span class="badge bg-success">PDF</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">Tasks</span></td>
                            <td>April 2026</td>
                            <td>{{ auth()->user()->name }}</td>
                            <td>2026-05-04 14:15</td>
                            <td><span class="badge bg-success">Excel</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">Leaves</span></td>
                            <td>Last 3 Months</td>
                            <td>{{ auth()->user()->name }}</td>
                            <td>2026-05-03 10:20</td>
                            <td><span class="badge bg-success">PDF</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 24px;
    color: white;
    margin-bottom: 24px;
}

.reports-header h2 {
    color: white;
}

.reports-header p {
    color: rgba(255, 255, 255, 0.8);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 24px;
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
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Period</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>IT</td>
                            <td>May 2026</td>
                            <td><span class="badge bg-success">Present</span></td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>HR</td>
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
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Generating...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.reports.generate") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.blob())
    .then(blob => {
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'report.' + formData.get('format');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
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
        showNotification('Error generating report. Please try again.', 'error');
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
</script>
@endpush
