<!-- Month View -->
<div class="month-view">
    <div class="month-header">
        <h2>{{ \Carbon\Carbon::parse($date)->format('F Y') }}</h2>
    </div>

    @php
        $monthStart = \Carbon\Carbon::parse($date)->startOfMonth();
        $monthEnd = \Carbon\Carbon::parse($date)->endOfMonth();
        $calendarStart = $monthStart->copy()->startOfWeek();
        $calendarEnd = $monthEnd->copy()->endOfWeek();
    @endphp

    <div class="calendar-scroll">
    <div class="calendar-grid desktop-calendar">
        <!-- Day headers -->
        <div class="calendar-day-header">Sun</div>
        <div class="calendar-day-header">Mon</div>
        <div class="calendar-day-header">Tue</div>
        <div class="calendar-day-header">Wed</div>
        <div class="calendar-day-header">Thu</div>
        <div class="calendar-day-header">Fri</div>
        <div class="calendar-day-header">Sat</div>

        <!-- Calendar days -->
        @for($date = $calendarStart->copy(); $date <= $calendarEnd; $date->addDay())
            @php
                $isCurrentMonth = $date->month === $monthStart->month;
                $isToday = $date->isToday();
                $dayTimetables = $timetables->filter(function($timetable) use ($date) {
                    return $timetable->date->format('Y-m-d') === $date->format('Y-m-d');
                });
            @endphp

            <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                <div class="calendar-date">
                    <span class="date-number">{{ $date->day }}</span>
                    @if($isToday)
                        <span class="today-indicator">Today</span>
                    @endif
                </div>

                <div class="calendar-events">
                    @if($dayTimetables->isNotEmpty())
                        @foreach($dayTimetables->take(3) as $timetable)
                            @php $eventUrl = $timetable->task ? route('tasks.show', $timetable->task) : route('timetables.show', $timetable); @endphp
                            <div
                                class="calendar-event calendar-event-clickable"
                                style="background-color: {{ $timetable->priority_color }}20; border-left: 2px solid {{ $timetable->priority_color }};"
                                onclick="window.location.href='{{ $eventUrl }}'"
                                role="link"
                                tabindex="0"
                                onkeydown="if(event.key==='Enter'||event.key===' '){ event.preventDefault(); window.location.href='{{ $eventUrl }}'; }"
                            >
                                <div class="event-time">{{ $timetable->start_time->format('H:i') }}</div>
                                <div class="event-title">{{ Str::limit($timetable->title, 20) }}</div>
                                @if($timetable->employee)
                                    <div class="event-employee">{{ Str::limit($timetable->employee->name, 15) }}</div>
                                @endif
                                @if($timetable->task && $timetable->task->reviewed_evaluation)
                                    <div class="event-evaluation">
                                        <span class="reviewed-icon">✓</span>
                                        Reviewed · Grade {{ $timetable->task->reviewed_evaluation->grade }} · Rating {{ $timetable->task->reviewed_evaluation->rating }}/5
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if($dayTimetables->count() > 3)
                            <div class="more-events">
                                +{{ $dayTimetables->count() - 3 }} more
                            </div>
                        @endif
                    @endif
                </div>

                @if($isCurrentMonth && $canCreate)
                    <a href="{{ route('timetables.create') }}?date={{ $date->format('Y-m-d') }}" 
                       class="add-event-btn">+</a>
                @endif
            </div>
        @endfor
    </div>
    </div>

    <div class="month-mobile-list">
        @for($date = $calendarStart->copy(); $date <= $calendarEnd; $date->addDay())
            @php
                $isCurrentMonth = $date->month === $monthStart->month;
                $isToday = $date->isToday();
                $dayTimetables = $timetables->filter(function($timetable) use ($date) {
                    return $timetable->date->format('Y-m-d') === $date->format('Y-m-d');
                });
            @endphp

            <div class="month-day-card {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                <div class="month-day-card-header">
                    <div>
                        <div class="month-day-title">{{ $date->format('D, M j') }}</div>
                        @if($isToday)
                            <span class="today-indicator">Today</span>
                        @endif
                    </div>
                    @if($isCurrentMonth && $canCreate)
                        <a href="{{ route('timetables.create') }}?date={{ $date->format('Y-m-d') }}" class="add-event-btn mobile-add-btn">+</a>
                    @endif
                </div>

                <div class="month-day-card-body">
                    @if($dayTimetables->isNotEmpty())
                        @foreach($dayTimetables as $timetable)
                            @php $eventUrl = $timetable->task ? route('tasks.show', $timetable->task) : route('timetables.show', $timetable); @endphp
                            <div class="month-mobile-event" onclick="window.location.href='{{ $eventUrl }}'" role="link" tabindex="0">
                                <div class="event-time">{{ $timetable->start_time->format('H:i') }}</div>
                                <div class="event-title">{{ Str::limit($timetable->title, 32) }}</div>
                                @if($timetable->employee)
                                    <div class="event-employee">{{ $timetable->employee->name }}</div>
                                @endif
                                @if($timetable->task && $timetable->task->reviewed_evaluation)
                                    <div class="event-evaluation">
                                        <span class="reviewed-icon">✓</span>
                                        Reviewed · Grade {{ $timetable->task->reviewed_evaluation->grade }} · Rating {{ $timetable->task->reviewed_evaluation->rating }}/5
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if($dayTimetables->count() > 3)
                            <div class="more-events">+{{ $dayTimetables->count() - 3 }} more</div>
                        @endif
                    @else
                        <div class="no-events">No tasks scheduled</div>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    @if($timetables->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <div class="empty-title">No schedules this month</div>
            <div class="empty-description">No schedule entries found for {{ \Carbon\Carbon::parse($date)->format('F Y') }}</div>
            @if($canCreate)
                <a href="{{ route('timetables.create') }}?date={{ $date }}" class="btn btn-primary">
                    Add Schedule for This Month
                </a>
            @endif
        </div>
    @endif
</div>

@push('styles')
<style>
.month-view {
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

.month-view {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.month-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background-color: var(--bg-secondary);
}

.month-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    min-height: 500px;
    background: var(--border-color);
}

.calendar-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.calendar-scroll .calendar-grid {
    min-width: 720px;
}

.month-mobile-list {
    display: none;
}

.calendar-day-header {
    padding: 12px 8px;
    background-color: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    border-right: 1px solid var(--border-color);
    font-weight: 600;
    font-size: 12px;
    text-align: center;
    color: var(--text-primary);
}

.calendar-day-header:last-child {
    border-right: none;
}

.calendar-day {
    min-height: 100px;
    border-right: 1px solid var(--border-color);
    border-bottom: 1px solid var(--border-color);
    position: relative;
    background-color: var(--bg-primary);
    padding-bottom: 26px;
}

.calendar-day:nth-child(7n) {
    border-right: none;
}

.calendar-day.other-month {
    background-color: var(--bg-secondary);
    opacity: 0.5;
}

.calendar-day.today {
    background-color: #f0f9ff;
}

.calendar-date {
    padding: 8px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid var(--border-color);
}

.date-number {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
}

.today-indicator {
    font-size: 9px;
    background-color: var(--supervisor-accent);
    color: white;
    padding: 2px 4px;
    border-radius: 8px;
    font-weight: 500;
}

.calendar-events {
    padding: 6px;
}

.calendar-event {
    padding: 6px 8px;
    border-radius: 6px;
    margin-bottom: 6px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.3s;
}

.calendar-event:has(.event-evaluation) {
    border: 1px solid #86efac;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.12);
}

.calendar-event:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.event-time {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1px;
}

.event-title {
    color: var(--text-primary);
    font-weight: 500;
    margin-bottom: 1px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.calendar-event-clickable {
    cursor: pointer;
}

.calendar-event-clickable:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
}

.event-employee {
    color: var(--text-muted);
    font-size: 10px;
}

.event-evaluation {
    margin-top: 3px;
    color: #166534;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
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

.more-events {
    font-size: 9px;
    color: var(--text-muted);
    text-align: center;
    padding: 2px;
    font-weight: 500;
}

.add-event-btn {
    position: absolute;
    bottom: 4px;
    right: 4px;
    width: 22px;
    height: 22px;
    background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
    color: #f5f1e8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    opacity: 0;
    transition: all 0.3s;
}

.calendar-day:hover .add-event-btn {
    opacity: 1;
}

.add-event-btn:hover {
    background-color: var(--supervisor-dark);
    transform: scale(1.1);
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: var(--text-primary);
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
    .month-header {
        padding: 16px 18px;
    }

    .month-header h2 {
        font-size: 18px;
    }

    .calendar-scroll .calendar-grid {
        min-width: 680px;
    }

    .desktop-calendar {
        display: none;
    }

    .month-mobile-list {
        display: block;
        padding: 12px;
        background: var(--bg-primary);
        border-top: 1px solid var(--border-color);
    }

    .month-day-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        background: #fff;
    }

    .month-day-card.other-month {
        opacity: 0.75;
        background: var(--bg-secondary);
    }

    .month-day-card.today {
        box-shadow: 0 0 0 2px rgba(44, 62, 80, 0.12);
    }

    .month-day-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        background: var(--bg-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .month-day-title {
        font-weight: 700;
        color: var(--text-primary);
    }

    .month-day-card-body {
        padding: 12px 14px;
    }

    .month-mobile-event {
        border-left: 3px solid #d4c4a8;
        background: rgba(245, 241, 232, 0.75);
        border-radius: 10px;
        padding: 10px 12px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .month-mobile-event:last-child { margin-bottom: 0; }

    .month-mobile-event .event-title {
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
        font-size: 13px;
    }

    .no-events {
        color: var(--text-muted);
        font-size: 13px;
        padding: 8px 2px;
    }

    .calendar-day {
        min-height: 80px;
    }
    
    .calendar-event {
        font-size: 9px;
        padding: 2px 4px;
    }
    
    .date-number {
        font-size: 12px;
    }
    
    .today-indicator {
        font-size: 8px;
        padding: 1px 3px;
    }
}

@media (max-width: 480px) {
    .month-header {
        padding: 14px 16px;
    }

    .month-header h2 {
        font-size: 17px;
    }

    .calendar-scroll .calendar-grid {
        min-width: 640px;
    }

    .month-mobile-list {
        padding: 10px;
    }

    .month-day-card-header,
    .month-day-card-body {
        padding-left: 12px;
        padding-right: 12px;
    }

    .calendar-day {
        min-height: 60px;
    }
    
    .calendar-event {
        font-size: 8px;
        padding: 1px 2px;
    }
    
    .date-number {
        font-size: 11px;
    }
    
    .calendar-day-header {
        font-size: 10px;
        padding: 8px 4px;
    }
    
    .calendar-date {
        padding: 4px;
    }
    
    .calendar-events {
        padding: 2px;
    }
}
</style>
@endpush
