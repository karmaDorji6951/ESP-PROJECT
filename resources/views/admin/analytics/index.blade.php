@extends('layouts.app')

@section('page_title', 'Analytics Dashboard')
@section('topbar_title', 'Analytics & Reports')

@section('content')
<div class="analytics-container">
    <!-- Analytics Header -->
    <div class="analytics-header app-page-hero">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="app-page-hero-kicker mb-2">Admin Workspace</div>
                <h2 class="app-page-hero-title mb-2">Analytics Dashboard</h2>
                <p class="app-page-hero-subtitle">Comprehensive insights into your ESP management system.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-light app-page-hero-action" onclick="exportReport('summary', 'pdf')">
                    <i class="bi bi-file-pdf me-2"></i>Export PDF
                </button>
                <button class="btn btn-light app-page-hero-action" onclick="exportReport('summary', 'excel')">
                    <i class="bi bi-file-excel me-2"></i>Export Excel
                </button>
                <button class="btn btn-light app-page-hero-action" onclick="refreshAnalytics()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Employees</h6>
                            <h3 class="fw-bold mb-0">{{ $employeeAnalytics['total_employees'] }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> +{{ $employeeAnalytics['new_employees_this_month'] }} this month
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Attendance Rate</h6>
                            <h3 class="fw-bold mb-0">{{ $attendanceAnalytics['attendance_rate'] }}%</h3>
                            <small class="text-muted">{{ $attendanceAnalytics['total_present'] }} present today</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-list-task text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Task Completion</h6>
                            <h3 class="fw-bold mb-0">{{ $taskAnalytics['completion_rate'] }}%</h3>
                            <small class="text-muted">{{ $taskAnalytics['completed_tasks'] }}/{{ $taskAnalytics['total_tasks'] }} tasks</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-graph-up text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Productivity Score</h6>
                            <h3 class="fw-bold mb-0">{{ $performanceMetrics['productivity_score'] }}%</h3>
                            <small class="text-muted">Overall performance</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Monthly Trends Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Monthly Trends</h5>
                    <p class="text-muted small mb-0">6-month performance overview</p>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Area Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Area Distribution</h5>
                    <p class="text-muted small mb-0">Employees by building area</p>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="row g-4 mb-4">
        <!-- Task Analytics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Task Analytics</h5>
                    <p class="text-muted small mb-0">Task status distribution</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $taskAnalytics['completed_tasks'] }}</h6>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-hourglass-split text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $taskAnalytics['in_progress_tasks'] }}</h6>
                                    <small class="text-muted">In Progress</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-clock text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $taskAnalytics['pending_tasks'] }}</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-exclamation-triangle text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $taskAnalytics['overdue_tasks'] }}</h6>
                                    <small class="text-muted">Overdue</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Completion Rate</span>
                            <span class="fw-bold">{{ $taskAnalytics['completion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $taskAnalytics['completion_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Attendance Analytics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Attendance Analytics</h5>
                    <p class="text-muted small mb-0">This month's attendance summary</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-clock-history text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $attendanceAnalytics['total_late'] }}</h6>
                                    <small class="text-muted">Late</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-calendar text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $attendanceAnalytics['working_days'] }}</h6>
                                    <small class="text-muted">Working Days</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Attendance Rate</span>
                            <span class="fw-bold">{{ $attendanceAnalytics['attendance_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ $attendanceAnalytics['attendance_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Top Performers</h5>
                    <p class="text-muted small mb-0">Best performing employees this month</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee Name</th>
                                    <th>Area</th>
                                    <th>Performance Score</th>
                                    <th>Task Completion Rate</th>
                                    <th>Attendance Rate</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($performanceMetrics['top_performers'] as $performer)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $performer['name'] }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $performer['department'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold me-2">{{ $performer['performance_score'] }}%</span>
                                                <div class="progress" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $performer['performance_score'] }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $performer['task_completion_rate'] }}%</td>
                                        <td>{{ $performer['attendance_rate'] }}%</td>
                                        <td>
                                            @if($performer['performance_score'] >= 80)
                                                <span class="badge bg-success">Excellent</span>
                                            @elseif($performer['performance_score'] >= 60)
                                                <span class="badge bg-primary">Good</span>
                                            @else
                                                <span class="badge bg-warning">Needs Improvement</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-graph-up d-block fs-2 mb-2"></i>
                                            No performance data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Analytics -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Leave Analytics</h5>
                    <p class="text-muted small mb-0">Leave status distribution</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $leaveAnalytics['approved_leaves'] }}</h6>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-clock text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $leaveAnalytics['pending_leaves'] }}</h6>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-x-circle text-danger"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $leaveAnalytics['rejected_leaves'] }}</h6>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-calendar-check text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $leaveAnalytics['total_leaves'] }}</h6>
                                    <small class="text-muted">Total Requests</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Approval Rate</span>
                            <span class="fw-bold">{{ $leaveAnalytics['approval_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ $leaveAnalytics['approval_rate'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Employee Growth -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4">
                    <h5 class="card-title mb-0">Employee Growth</h5>
                    <p class="text-muted small mb-0">Workforce expansion metrics</p>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-people text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $employeeAnalytics['total_employees'] }}</h6>
                                    <small class="text-muted">Total Employees</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-person-check text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $employeeAnalytics['active_employees'] }}</h6>
                                    <small class="text-muted">Active Employees</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-person-plus text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $employeeAnalytics['new_employees_this_month'] }}</h6>
                                    <small class="text-muted">New This Month</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="bi bi-graph-up-arrow text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $employeeAnalytics['employee_growth_rate'] }}%</h6>
                                    <small class="text-muted">Growth Rate</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Active Rate</span>
                            <span class="fw-bold">{{ round(($employeeAnalytics['active_employees'] / $employeeAnalytics['total_employees']) * 100, 2) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ round(($employeeAnalytics['active_employees'] / $employeeAnalytics['total_employees']) * 100, 2) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css" rel="stylesheet">
<style>
.analytics-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.analytics-header {
    margin-bottom: 24px;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    border-radius: 4px;
    transition: width 0.6s ease;
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

@media (max-width: 768px) {
    .analytics-container {
        padding: 10px;
    }
    
    .analytics-header {
        padding: 16px;
    }
    
    .analytics-header h2 {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    const monthlyTrendsData = @json($monthlyTrends);
    
    new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
            labels: monthlyTrendsData.map(item => item.month),
            datasets: [
                {
                    label: 'Attendance Rate',
                    data: monthlyTrendsData.map(item => item.attendance_rate),
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Task Completion Rate',
                    data: monthlyTrendsData.map(item => item.task_completion_rate),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'New Employees',
                    data: monthlyTrendsData.map(item => item.new_employees),
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
    
    // Area Distribution Chart
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    const departmentData = @json($departmentAnalytics);
    
    new Chart(departmentCtx, {
        type: 'doughnut',
        data: {
            labels: departmentData.map(item => item.department),
            datasets: [{
                data: departmentData.map(item => item.employee_count),
                backgroundColor: [
                    '#06b6d4',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#ec4899',
                    '#14b8a6',
                    '#f97316'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
});

function exportReport(type, format) {
    fetch('/admin/analytics/export', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: type,
            format: format
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Create download link
            const link = document.createElement('a');
            link.href = data.download_url;
            link.download = `analytics-report.${format}`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('Error exporting report: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error exporting report. Please try again.');
    });
}

function refreshAnalytics() {
    // Show loading state
    const refreshBtn = event.target;
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Refreshing...';
    refreshBtn.disabled = true;
    
    // Reload the page after a short delay
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}
</script>
@endpush
