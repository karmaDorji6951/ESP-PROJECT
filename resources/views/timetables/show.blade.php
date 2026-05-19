@extends('layouts.app')

@section('page_title', $timetable->title)
@section('topbar_title', 'Schedule Details')

@section('content')
<div class="timetable-show-container">
    <div class="show-header">
        <div class="header-left">
            <h1>{{ $timetable->title }}</h1>
            <div class="header-badges">
                <span class="priority-badge priority-{{ $timetable->priority }}">
                    {{ ucfirst($timetable->priority) }} Priority
                </span>
                <span class="status-badge status-{{ $timetable->status }}">
                    {{ ucfirst($timetable->status) }}
                </span>
            </div>
        </div>
        <div class="header-right">
            @if(auth()->user()->role->slug === 'admin' || auth()->user()->role->slug === 'supervisor')
                <a href="{{ route('timetables.edit', $timetable) }}" class="btn btn-primary">
                    Edit Schedule
                </a>
            @endif
        </div>
    </div>

    <div class="show-content">
        <div class="main-details">
            <div class="detail-section">
                <h3>Schedule Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Date</label>
                        <value>{{ $timetable->date->format('l, F j, Y') }}</value>
                    </div>
                    <div class="detail-item">
                        <label>Time</label>
                        <value>{{ $timetable->start_time->format('H:i') }} - {{ $timetable->end_time->format('H:i') }}</value>
                    </div>
                    @if($timetable->location)
                        <div class="detail-item">
                            <label>Location</label>
                            <value>{{ $timetable->location }}</value>
                        </div>
                    @endif
                </div>
            </div>

            @if($timetable->description)
                <div class="detail-section">
                    <h3>Description</h3>
                    <div class="description-content">
                        {{ $timetable->description }}
                    </div>
                </div>
            @endif

            <div class="detail-section">
                <h3>Assignment Details</h3>
                <div class="detail-grid">
                    @if($timetable->employee)
                        <div class="detail-item">
                            <label>Assigned Employee</label>
                            <value>{{ $timetable->employee->name }}</value>
                        </div>
                    @endif
                    @if($timetable->assigned_to_role)
                        <div class="detail-item">
                            <label>Assigned Role</label>
                            <value>{{ ucfirst($timetable->assigned_to_role) }}</value>
                        </div>
                    @endif
                    <div class="detail-item">
                        <label>Created By</label>
                        <value>{{ $timetable->assignedBy->name }}</value>
                    </div>
                    <div class="detail-item">
                        <label>Created At</label>
                        <value>{{ $timetable->created_at->format('M j, Y \a\t H:i') }}</value>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-details">
            <div class="sidebar-card">
                <h3>Quick Actions</h3>
                <div class="action-list">
                    <a href="{{ route('timetables.index') }}" class="action-item">
                        <span class="action-icon">📅</span>
                        <span>Back to Timetable</span>
                    </a>
                    @if(auth()->user()->role->slug === 'admin' || auth()->user()->role->slug === 'supervisor')
                        <a href="{{ route('timetables.edit', $timetable) }}" class="action-item">
                            <span class="action-icon">✏️</span>
                            <span>Edit Schedule</span>
                        </a>
                        <form action="{{ route('timetables.destroy', $timetable) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-item delete">
                                <span class="action-icon">🗑️</span>
                                <span>Delete Schedule</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="sidebar-card">
                <h3>Timeline</h3>
                <div class="timeline-info">
                    <div class="timeline-item">
                        <div class="timeline-dot created"></div>
                        <div class="timeline-content">
                            <div class="timeline-title">Created</div>
                            <div class="timeline-date">{{ $timetable->created_at->format('M j, Y \a\t H:i') }}</div>
                            <div class="timeline-user">by {{ $timetable->assignedBy->name }}</div>
                        </div>
                    </div>
                    @if($timetable->updated_at->gt($timetable->created_at))
                        <div class="timeline-item">
                            <div class="timeline-dot updated"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Updated</div>
                                <div class="timeline-date">{{ $timetable->updated_at->format('M j, Y \a\t H:i') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timetable-show-container {
    --bg-primary: #ffffff;
    --bg-secondary: #f5f1e8;
    --border-color: #d4c4a8;
    --text-primary: #2c3e50;
    --text-secondary: #4f6472;
    --text-muted: #7a6a5a;
    --supervisor-accent: #2c3e50;
    --supervisor-dark: #1a252f;
    --success: #5a8a7a;
    --warning: #d4c4a8;
    --danger: #a85a5a;
}

.timetable-show-container {
    max-width: 1200px;
    margin: 0 auto;
}

.show-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}

.header-left h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.header-badges {
    display: flex;
    gap: 12px;
}

.priority-badge, .status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-high {
    background-color: #fef2f2;
    color: #991b1b;
}

.priority-medium {
    background-color: #fffbeb;
    color: #92400e;
}

.priority-low {
    background-color: #f0fdf4;
    color: #166534;
}

.status-scheduled {
    background-color: #eff6ff;
    color: #1e40af;
}

.status-in_progress {
    background-color: #fffbeb;
    color: #92400e;
}

.status-completed {
    background-color: #f0fdf4;
    color: #166534;
}

.status-cancelled {
    background-color: #fef2f2;
    color: #991b1b;
}

.header-right {
    display: flex;
    gap: 12px;
}

.show-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
}

.main-details {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.detail-section {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 20px;
    box-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
}

.detail-section h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item label {
    font-size: 12px;
    font-weight: 500;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-item value {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
}

.description-content {
    line-height: 1.6;
    color: var(--text-secondary);
    font-size: 14px;
}

.sidebar-details {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.sidebar-card {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 20px;
    box-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
}

.sidebar-card h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 16px;
}

.action-list {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 6px;
    text-decoration: none;
    color: var(--text-primary);
    transition: background-color 0.3s;
    border: none;
    background: none;
    cursor: pointer;
    text-align: left;
    width: 100%;
    font-size: 14px;
}

.action-item:hover {
    background-color: var(--bg-secondary);
}

.action-item.delete {
    color: var(--danger);
}

.action-item.delete:hover {
    background-color: #fef2f2;
}

.action-icon {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.timeline-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.timeline-item {
    display: flex;
    gap: 12px;
}

.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-top: 2px;
    flex-shrink: 0;
}

.timeline-dot.created {
    background-color: var(--supervisor-accent);
}

.timeline-dot.updated {
    background-color: var(--warning);
}

.timeline-content {
    flex: 1;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.timeline-date {
    font-size: 12px;
    color: var(--text-muted);
    margin-bottom: 1px;
}

.timeline-user {
    font-size: 12px;
    color: var(--text-muted);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary {
    background-color: var(--supervisor-accent);
    color: white;
}

.btn-primary:hover {
    background-color: var(--supervisor-dark);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .show-header {
        flex-direction: column;
        gap: 16px;
    }
    
    .show-content {
        grid-template-columns: 1fr;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
@endpush
