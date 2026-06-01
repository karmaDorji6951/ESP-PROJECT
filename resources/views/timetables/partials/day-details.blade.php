@if($dayTimetables->isEmpty())
    <div class="no-tasks-day">
        <div class="no-tasks-icon">📅</div>
        <div class="no-tasks-title">No tasks scheduled</div>
        <div class="no-tasks-description">No schedule entries found for this date</div>
        
        <!-- Add Task Button -->
        <div style="margin-top: 30px;">
            @if($canCreate)
                <a href="{{ route('timetables.create') }}?date={{ $date }}" 
                   onmouseover="this.style.backgroundColor='#0e919e'; this.style.boxShadow='0 8px 20px rgba(6, 182, 212, 0.4)';"
                   onmouseout="this.style.backgroundColor='#06b6d4'; this.style.boxShadow='0 4px 12px rgba(6, 182, 212, 0.3)';"
                   style="display: inline-block; background: linear-gradient(135deg, #06b6d4 0%, #0e919e 100%); color: white; padding: 14px 32px; font-size: 16px; font-weight: 700; border-radius: 8px; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);">
                    ✨ Add Task
                </a>
            @else
                <div style="display: inline-block; background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%); color: white; padding: 14px 32px; font-size: 16px; font-weight: 700; border-radius: 8px; opacity: 0.7; cursor: not-allowed;">
                    ✨ Add Task (Supervisor Only)
                </div>
                <div style="display: block; margin-top: 15px; color: #0369a1; background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); padding: 14px 16px; border-radius: 8px; font-size: 14px; border-left: 4px solid #06b6d4; font-weight: 500;">
                    ℹ️ You can only view tasks. Contact your supervisor to create new tasks.
                </div>
            @endif
        </div>
    </div>
@else
    <div class="day-details-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 12px 16px; background-color: var(--bg-secondary); border-radius: 8px;">
        <div>
            <h5 style="margin: 0; font-size: 16px; font-weight: 600;">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</h5>
            <p style="margin: 4px 0 0 0; color: var(--text-muted); font-size: 14px;">{{ $dayTimetables->count() }} task{{ $dayTimetables->count() !== 1 ? 's' : '' }} scheduled</p>
        </div>
        @if($canCreate)
            <a href="{{ route('timetables.create') }}?date={{ $date }}" class="btn btn-primary btn-sm">
                + Add Task
            </a>
        @endif
    </div>

    <div class="day-timeline">
        @foreach($dayTimetables as $timetable)
            @php $eventUrl = $timetable->task ? route('tasks.show', $timetable->task) : route('timetables.show', $timetable); @endphp
            <div
                class="timeline-event timeline-event-clickable"
                onclick="window.location.href='{{ $eventUrl }}'"
                role="link"
                tabindex="0"
                onkeydown="if(event.key==='Enter'||event.key===' '){ event.preventDefault(); window.location.href='{{ $eventUrl }}'; }"
            >
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

                    @if($timetable->task && $timetable->task->reviewed_evaluation)
                        <div class="evaluation-summary">
                            <div class="evaluation-label"><span class="reviewed-icon">✓</span> Reviewed Task</div>
                            <div class="evaluation-meta">
                                Grade <strong>{{ $timetable->task->reviewed_evaluation->grade }}</strong>
                                · Rating <strong>{{ $timetable->task->reviewed_evaluation->rating }}/5</strong>
                            </div>
                        </div>
                    @endif

                    @if($timetable->task)
                        <div class="event-task-link-wrap">
                            <a href="{{ route('tasks.show', $timetable->task) }}" class="event-task-link">View Task Details</a>
                        </div>
                    @endif
                    
                    <div class="event-details">
                        @if($timetable->location)
                            <div class="detail-item">
                                <span class="detail-icon"><i class="bi bi-geo-alt"></i></span>
                                <span>{{ $timetable->location }}</span>
                            </div>
                        @endif
                        
                        @if($timetable->employee)
                            <div class="detail-item">
                                <span class="detail-icon"><i class="bi bi-person"></i></span>
                                <span>{{ $timetable->employee->name }}</span>
                            </div>
                        @endif
                        
                        @if($timetable->assigned_to_role)
                            <div class="detail-item">
                                <span class="detail-icon"><i class="bi bi-people"></i></span>
                                <span>{{ ucfirst($timetable->assigned_to_role) }}</span>
                            </div>
                        @endif
                        
                        <div class="detail-item">
                            <span class="detail-icon"><i class="bi bi-person-check"></i></span>
                            <span>By {{ $timetable->assignedBy->name }}</span>
                        </div>
                    </div>
                    
                    <div class="event-actions">
                        <a href="{{ route('timetables.show', $timetable) }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation()">View Details</a>
                        @if($canCreate)
                            <a href="{{ route('timetables.edit', $timetable) }}" class="btn btn-sm btn-outline-secondary" onclick="event.stopPropagation()">Edit</a>
                            <form action="{{ route('timetables.destroy', $timetable) }}" method="POST" style="display: inline;" onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation()">Delete</button>
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

.no-tasks-day .no-tasks-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.no-tasks-day .no-tasks-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.no-tasks-day .no-tasks-description {
    color: var(--text-muted);
    margin-bottom: 20px;
}

.no-tasks-day .btn {
    margin-top: 8px;
    font-weight: 500;
    padding: 10px 24px;
    font-size: 15px;
    display: inline-block;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.no-tasks-day .btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.no-tasks-day .btn:disabled {
    cursor: not-allowed;
    opacity: 0.6;
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
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 12px;
    border-left: 5px solid var(--supervisor-accent);
    border: 1px solid #e0f2fe;
    box-shadow: 0 2px 8px rgba(6, 182, 212, 0.1);
    transition: all 0.3s ease;
}

.timeline-event-clickable {
    cursor: pointer;
}

.timeline-event-clickable:hover {
    box-shadow: 0 10px 20px rgba(6, 182, 212, 0.16);
    transform: translateX(4px);
}

.timeline-event:hover {
    box-shadow: 0 8px 16px rgba(6, 182, 212, 0.15);
    transform: translateX(4px);
}

.event-time-block {
    min-width: 120px;
    text-align: center;
    padding: 12px;
    background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
    border-radius: 8px;
    border: 1px solid #7dd3fc;
    font-weight: 600;
    color: #0369a1;
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
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.priority-high {
    background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
    color: #b91c1c;
}

.priority-medium {
    background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
    color: #b45309;
}

.priority-low {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #15803d;
}

.status-scheduled {
    background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
    color: #0369a1;
}

.status-in_progress {
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: #4338ca;
}

.status-completed {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #15803d;
}

.status-cancelled {
    background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
    color: #b91c1c;
}

.event-description {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 12px;
    line-height: 1.5;
}

.evaluation-summary {
    margin-bottom: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    background: #ecfdf5;
    border: 1px solid #86efac;
}

.evaluation-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #15803d;
    margin-bottom: 4px;
}

.reviewed-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #16a34a;
    color: #ffffff;
    font-size: 10px;
    line-height: 1;
}

.evaluation-meta {
    font-size: 13px;
    color: var(--text-primary);
}

.event-task-link-wrap {
    margin-bottom: 12px;
}

.event-task-link {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    color: var(--supervisor-accent);
}

.event-task-link:hover {
    text-decoration: underline;
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
