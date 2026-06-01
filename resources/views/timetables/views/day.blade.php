<!-- Day View -->
<div class="day-view">
    <div class="day-header">
        <h2>{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</h2>
    </div>

    @if($timetables->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <div class="empty-title">No schedules for this day</div>
            <div class="empty-description">No schedule entries found for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</div>
            @if($canCreate)
                <a href="{{ route('timetables.create') }}?date={{ $date }}" class="btn btn-primary">
                    Add Schedule for This Day
                </a>
            @endif
        </div>
    @else
        <div class="timeline">
            @foreach($timetables as $timetable)
                <div class="timeline-item {{ $timetable->task && $timetable->task->reviewed_evaluation ? 'evaluated-item' : '' }}">
                    <div class="time-slot">
                        <div class="time">{{ $timetable->start_time->format('H:i') }}</div>
                        <div class="time-end">{{ $timetable->end_time->format('H:i') }}</div>
                    </div>
                    @php $cardUrl = $timetable->task ? route('tasks.show', $timetable->task) : route('timetables.show', $timetable); @endphp
                    <div
                        class="schedule-card schedule-card-clickable"
                        onclick="window.location.href='{{ $cardUrl }}'"
                        role="link"
                        tabindex="0"
                        onkeydown="if(event.key==='Enter'||event.key===' '){ event.preventDefault(); window.location.href='{{ $cardUrl }}'; }"
                    >
                        <div class="card-header">
                            <h3>{{ $timetable->title }}</h3>
                            <div class="card-badges">
                                @if($timetable->task && $timetable->task->reviewed_evaluation)
                                    <span class="reviewed-badge">Reviewed</span>
                                @endif
                                <span class="priority-badge priority-{{ $timetable->priority }}">
                                    {{ $timetable->priority }}
                                </span>
                                <span class="status-badge status-{{ $timetable->status }}">
                                    {{ $timetable->status }}
                                </span>
                            </div>
                        </div>
                        
                        @if($timetable->description)
                            <p class="description">{{ $timetable->description }}</p>
                        @endif

                        @if($timetable->task && $timetable->task->reviewed_evaluation)
                            <div class="evaluation-summary">
                                <span class="evaluation-label">Evaluated Task</span>
                                <div class="evaluation-meta">
                                    Grade <strong>{{ $timetable->task->reviewed_evaluation->grade }}</strong>
                                    · Rating <strong>{{ $timetable->task->reviewed_evaluation->rating }}/5</strong>
                                </div>
                            </div>
                        @endif

                        @if($timetable->task)
                            <div class="mb-2">
                                <a href="{{ route('tasks.show', $timetable->task) }}" class="task-details-link">View Task Details</a>
                            </div>
                        @endif
                        
                        <div class="schedule-details">
                            @if($timetable->location)
                                <div class="detail">
                                    <span class="detail-icon">📍</span>
                                    <span>{{ $timetable->location }}</span>
                                </div>
                            @endif
                            
                            @if($timetable->employee)
                                <div class="detail">
                                    <span class="detail-icon">👤</span>
                                    <span>{{ $timetable->employee->name }}</span>
                                </div>
                            @endif
                            
                            @if($timetable->assigned_to_role)
                                <div class="detail">
                                    <span class="detail-icon">👥</span>
                                    <span>{{ ucfirst($timetable->assigned_to_role) }}</span>
                                </div>
                            @endif
                            
                            <div class="detail">
                                <span class="detail-icon">📝</span>
                                <span>By {{ $timetable->assignedBy->name }}</span>
                            </div>
                        </div>
                        
                        <div class="card-actions">
                            <a href="{{ route('timetables.show', $timetable) }}" class="action-btn btn-view" onclick="event.stopPropagation()">View</a>
                            @if($canCreate)
                                <a href="{{ route('timetables.edit', $timetable) }}" class="action-btn btn-edit" onclick="event.stopPropagation()">Edit</a>
                                <form action="{{ route('timetables.destroy', $timetable) }}" method="POST" style="display: inline;" onsubmit="event.stopPropagation(); return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn-delete" onclick="event.stopPropagation()">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@push('styles')
<style>
.day-view {
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

.day-view {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.day-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--bg-secondary);
}

.day-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.timeline {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.timeline-item {
    display: flex;
    gap: 20px;
    margin-bottom: 24px;
}

.timeline-item.evaluated-item .schedule-card {
    border-color: #86efac;
    box-shadow: 0 6px 18px rgba(22, 163, 74, 0.12);
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.time-slot {
    min-width: 80px;
    text-align: center;
    padding: 12px 8px;
    background-color: var(--bg-secondary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.time {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
}

.time-end {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 2px;
}

.schedule-card {
    flex: 1;
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 16px;
    transition: box-shadow 0.3s;
    box-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
}

.schedule-card-clickable {
    cursor: pointer;
}

.schedule-card-clickable:hover {
    box-shadow: 0 6px 18px rgba(44, 62, 80, 0.12);
    transform: translateY(-1px);
}

.schedule-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.card-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    flex: 1;
}

.card-badges {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.reviewed-badge {
    padding: 4px 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #15803d;
    border: 1px solid #86efac;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.4px;
    text-transform: uppercase;
}

.priority-badge, .status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
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

.description {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 12px;
    line-height: 1.5;
}

.evaluation-summary {
    margin-bottom: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
    border: 1px solid #86efac;
}

.task-details-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    color: #2c3e50;
}

.task-details-link:hover {
    text-decoration: underline;
}

.evaluation-label {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #15803d;
    margin-bottom: 4px;
}

.evaluation-meta {
    font-size: 13px;
    color: var(--text-primary);
}

.schedule-details {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 12px;
}

.detail {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-secondary);
}

.detail-icon {
    font-size: 14px;
}

.card-actions {
    display: flex;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid var(--border-color);
}

.action-btn {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid var(--border-color);
    transition: all 0.3s;
}

.btn-view {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
}

.btn-view:hover {
    background-color: var(--supervisor-light);
}

.btn-edit {
    background-color: #eef4f8;
    color: #2c3e50;
    border-color: #7a9fb5;
}

.btn-edit:hover {
    background-color: #3b82f6;
    color: white;
}

.btn-delete {
    background-color: #f8e8e8;
    color: #7a4a4a;
    border-color: #a85a5a;
}

.btn-delete:hover {
    background-color: #ef4444;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
}

.empty-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.empty-description {
    color: var(--text-muted);
    margin-bottom: 24px;
}

/* Responsive */
@media (max-width: 768px) {
    .timeline-item {
        flex-direction: column;
        gap: 12px;
    }
    
    .time-slot {
        min-width: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .card-header {
        flex-direction: column;
        gap: 8px;
    }
    
    .schedule-details {
        flex-direction: column;
        gap: 8px;
    }
    
    .card-actions {
        flex-wrap: wrap;
    }
}
</style>
@endpush
