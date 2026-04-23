@if($dayTimetables->isEmpty())
    <div class="no-tasks-day">
        <div class="no-tasks-icon">ð</div>
        <div class="no-tasks-title">No tasks scheduled</div>
        <div class="no-tasks-description">No timetable entries found for this date</div>
        @if($canCreate)
            <a href="{{ route('timetables.create') }}?date={{ $date }}" class="btn btn-primary btn-sm">
                + Add Task for This Day
            </a>
        @endif
    </div>
@else
    <div class="day-timeline">
        @foreach($dayTimetables as $timetable)
            <div class="timeline-event">
                <div class="event-time-block">
                    <div class="time-range">
                        {{ $timetable->start_time->format('H:i') }} - {{ $timetable->end_time->format('H:i') }}
                    </div>
                    <div class="duration">
                        {{ $timetable->start_time->diffInMinutes($timetable->end_time) }} minutes
                    </div>
                </div>
                
                <div class="event-content">
                    <div class="event-header">
                        <h4>{{ $timetable->title }}</h4>
                        <div class="event-badges">
                            <span class="priority-badge priority-{{ $timetable->priority }}">
                                {{ ucfirst($timetable->priority) }}
                            </span>
                            <span class="status-badge status-{{ $timetable->status }}">
                                {{ ucfirst($timetable->status) }}
                            </span>
                        </div>
                    </div>
                    
                    @if($timetable->description)
                        <p class="event-description">{{ $timetable->description }}</p>
                    @endif
                    
                    <div class="event-details">
                        @if($timetable->location)
                            <div class="detail-item">
                                <span class="detail-icon">ð</span>
                                <span>{{ $timetable->location }}</span>
                            </div>
                        @endif
                        
                        @if($timetable->employee)
                            <div class="detail-item">
                                <span class="detail-icon">ð</span>
                                <span>{{ $timetable->employee->name }}</span>
                            </div>
                        @endif
                        
                        @if($timetable->assigned_to_role)
                            <div class="detail-item">
                                <span class="detail-icon">ð</span>
                                <span>{{ ucfirst($timetable->assigned_to_role) }}</span>
                            </div>
                        @endif
                        
                        <div class="detail-item">
                            <span class="detail-icon">ð</span>
                            <span>By {{ $timetable->assignedBy->name }}</span>
                        </div>
                    </div>
                    
                    <div class="event-actions">
                        <a href="{{ route('timetables.show', $timetable) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                        @if($canCreate)
                            <a href="{{ route('timetables.edit', $timetable) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <form action="{{ route('timetables.destroy', $timetable) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<style>
.no-tasks-day {
    text-align: center;
    padding: 40px 20px;
}

.no-tasks-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.no-tasks-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.no-tasks-description {
    color: var(--text-muted);
    margin-bottom: 20px;
}

.day-timeline {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.timeline-event {
    display: flex;
    gap: 16px;
    padding: 16px;
    background-color: var(--bg-secondary);
    border-radius: 8px;
    border-left: 4px solid var(--supervisor-accent);
}

.event-time-block {
    min-width: 120px;
    text-align: center;
    padding: 12px;
    background-color: var(--bg-primary);
    border-radius: 6px;
    border: 1px solid var(--border-color);
}

.time-range {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.duration {
    font-size: 12px;
    color: var(--text-muted);
}

.event-content {
    flex: 1;
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.event-header h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    flex: 1;
}

.event-badges {
    display: flex;
    gap: 8px;
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

.event-description {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 12px;
    line-height: 1.5;
}

.event-details {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 12px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-secondary);
}

.detail-icon {
    font-size: 14px;
}

.event-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-outline-primary {
    color: var(--supervisor-accent);
    border-color: var(--supervisor-accent);
    background-color: transparent;
}

.btn-outline-primary:hover {
    background-color: var(--supervisor-accent);
    color: white;
}

.btn-outline-secondary {
    color: var(--text-secondary);
    border-color: var(--border-color);
    background-color: transparent;
}

.btn-outline-secondary:hover {
    background-color: var(--bg-secondary);
}

.btn-outline-danger {
    color: var(--danger);
    border-color: var(--danger);
    background-color: transparent;
}

.btn-outline-danger:hover {
    background-color: var(--danger);
    color: white;
}
</style>
