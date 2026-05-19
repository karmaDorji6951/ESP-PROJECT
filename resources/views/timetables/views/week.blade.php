<!-- Week View -->
<div class="week-view">
    <div class="week-header">
        <h2>
            Week of {{ \Carbon\Carbon::parse($date)->startOfWeek()->format('F j') }} - 
            {{ \Carbon\Carbon::parse($date)->startOfWeek()->addDays(13)->format('F j, Y') }}
        </h2>
    </div>

    <div class="week-grid-simple">
        @php
            $weekStart = \Carbon\Carbon::parse($date)->startOfWeek();
            $weekEnd = \Carbon\Carbon::parse($date)->startOfWeek()->addDays(13);
        @endphp

        @for($day = 0; $day < 14; $day++)
            @php
                $currentDate = $weekStart->copy()->addDays($day);
                $dayTimetables = $timetables->filter(function($timetable) use ($currentDate) {
                    return $timetable->date->format('Y-m-d') === $currentDate->format('Y-m-d');
                });
                $taskCount = $dayTimetables->count();
                $highPriorityCount = $dayTimetables->where('priority', 'high')->count();
                $mediumPriorityCount = $dayTimetables->where('priority', 'medium')->count();
                $lowPriorityCount = $dayTimetables->where('priority', 'low')->count();
            @endphp

            <div class="day-cell-simple {{ $currentDate->isToday() ? 'today' : '' }}" 
                 onclick="showDayDetails('{{ $currentDate->format('Y-m-d') }}')"
                 style="cursor: pointer;">
                <div class="day-header-simple">
                    <div class="day-name">{{ $currentDate->format('D') }}</div>
                    <div class="day-date">{{ $currentDate->format('j') }}</div>
                </div>
                
                <div class="day-indicators">
                    @if($taskCount > 0)
                        <div class="task-count">
                            <span class="count-badge">{{ $taskCount }} {{ $taskCount == 1 ? 'task' : 'tasks' }}</span>
                        </div>
                        
                        <div class="priority-indicators">
                            @if($highPriorityCount > 0)
                                <span class="priority-dot high" title="{{ $highPriorityCount }} high priority"></span>
                            @endif
                            @if($mediumPriorityCount > 0)
                                <span class="priority-dot medium" title="{{ $mediumPriorityCount }} medium priority"></span>
                            @endif
                            @if($lowPriorityCount > 0)
                                <span class="priority-dot low" title="{{ $lowPriorityCount }} low priority"></span>
                            @endif
                        </div>
                        
                        <div class="task-preview">
                            @foreach($dayTimetables->take(2) as $timetable)
                                <div class="task-item">
                                    <span class="task-time">{{ $timetable->start_time->format('H:i') }}</span>
                                    <span class="task-title">{{ Str::limit($timetable->title, 15) }}</span>
                                </div>
                            @endforeach
                            @if($taskCount > 2)
                                <div class="more-tasks">+{{ $taskCount - 2 }} more</div>
                            @endif
                        </div>
                    @else
                        <div class="no-tasks">
                            <span class="no-tasks-text">No tasks</span>
                        </div>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    @if($timetables->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-calendar-week"></i></div>
            <div class="empty-title">No schedules in this period</div>
            <div class="empty-description">No timetable entries found for these two weeks</div>
            @if($canCreate)
                <a href="{{ route('timetables.create') }}?date={{ $date }}" class="btn btn-primary">
                    Add Schedule for This Week
                </a>
            @endif
        </div>
    @endif
</div>

<!-- Day Details Modal -->
<div id="dayDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalDate">Day Details</h3>
            <button class="modal-close" onclick="closeDayDetails()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make function global
    window.showDayDetails = function(date) {
        const modal = document.getElementById('dayDetailsModal');
        const modalDate = document.getElementById('modalDate');
        const modalBody = document.getElementById('modalBody');

        if (!modal || !modalDate || !modalBody) {
            console.error('Day details modal elements not found');
            return;
        }
        
        // Format date for display
        const dateObj = new Date(date);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        modalDate.textContent = formattedDate;
        modalBody.innerHTML = '<div class="loading">Loading...</div>';
        modal.style.display = 'block';

        // Use the correct route URL
        const url = '{{ route("timetables.day-details") }}?date=' + encodeURIComponent(date);

        // Fetch day details via AJAX
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                modalBody.innerHTML = '<div class="error">Error loading details: ' + error.message + '<br>URL: ' + url + '</div>';
            });
    };
    
    window.closeDayDetails = function() {
        const modal = document.getElementById('dayDetailsModal');
        if (modal) {
            modal.style.display = 'none';
        }
    };
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('dayDetailsModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

@push('styles')
<style>
.week-view {
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
    background-color: var(--bg-primary);
    border-radius: 10px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 16px rgba(44, 62, 80, 0.06);
}

.week-view {
    color: var(--text-primary);
}

.week-header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, #f5f1e8 0%, #ede6d9 100%);
}

.week-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.week-grid-simple {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: var(--border-color);
    border-radius: 0 0 10px 10px;
    overflow: hidden;
}

.day-cell-simple {
    background-color: var(--bg-primary);
    min-height: 180px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
}

.day-cell-simple:hover {
    background-color: #ede6d9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.day-cell-simple.today {
    background: linear-gradient(135deg, #eef4f8 0%, #f5f1e8 100%);
    box-shadow: inset 0 0 0 2px rgba(44, 62, 80, 0.9);
}

.day-header-simple {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-color);
}

.day-name {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--text-primary);
}

.day-date {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}

.day-indicators {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.task-count {
    display: flex;
    justify-content: center;
}

.count-badge {
    background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
    color: #f5f1e8;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}

.priority-indicators {
    display: flex;
    justify-content: center;
    gap: 4px;
}

.priority-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    cursor: help;
}

.priority-dot.high {
    background-color: var(--danger);
}

.priority-dot.medium {
    background-color: var(--warning);
}

.priority-dot.low {
    background-color: var(--success);
}

.task-preview {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10px;
    color: var(--text-secondary);
    background: rgba(245, 241, 232, 0.7);
    padding: 2px 4px;
    border-radius: 4px;
}

.task-time {
    font-weight: 600;
    color: var(--text-primary);
    min-width: 35px;
}

.task-title {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.more-tasks {
    font-size: 9px;
    color: var(--text-muted);
    text-align: center;
    font-style: italic;
}

.no-tasks {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.no-tasks-text {
    color: var(--text-muted);
    font-size: 12px;
    font-style: italic;
}

/* Modal Styles */
.modal {
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: none;
    backdrop-filter: blur(2px);
}

.modal-content {
    position: relative;
    background-color: #ffffff;
    margin: 50px auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 800px;
    max-height: calc(100vh - 100px);
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
    opacity: 1;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    background: linear-gradient(135deg, #f5f1e8 0%, #ede6d9 100%);
    opacity: 1;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
}

.modal-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: var(--text-muted);
    padding: 0;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.3s;
    z-index: 10000;
    position: relative;
}

.modal-close:hover {
    background-color: var(--danger);
    color: white;
    transform: rotate(90deg);
}

.modal-body {
    padding: 24px;
    max-height: calc(80vh - 140px);
    overflow-y: auto;
    position: relative;
    background-color: #ffffff;
    opacity: 1;
}

.modal-body::-webkit-scrollbar {
    width: 8px;
}

.modal-body::-webkit-scrollbar-track {
    background: var(--bg-secondary);
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

.loading {
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
    font-size: 14px;
}

.error {
    text-align: center;
    padding: 40px;
    color: var(--danger);
    font-size: 14px;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 48px 20px;
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
    background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
    color: #f5f1e8;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3d5568 0%, #1a252f 100%);
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 1200px) {
    .week-grid {
        grid-template-columns: 60px repeat(7, 1fr);
    }
    
    .time-slot {
        font-size: 10px;
    }
    
    .timetable-event {
        font-size: 10px;
    }
}

@media (max-width: 768px) {
    .week-grid {
        grid-template-columns: 50px repeat(7, 1fr);
    }
    
    .time-slot {
        font-size: 9px;
    }
    
    .timetable-event {
        font-size: 9px;
        padding: 2px 4px;
    }

    .day-cell-simple {
        min-height: 140px;
    }
    
    .event-title {
        font-size: 9px;
    }
    
    .event-employee {
        font-size: 8px;
    }
}

@media (max-width: 640px) {
    .week-grid {
        grid-template-columns: 40px repeat(7, 1fr);
        font-size: 8px;
    }
    
    .time-slot {
        font-size: 8px;
    }
    
    .day-header-cell {
        padding: 4px;
    }
    
    .day-date {
        font-size: 12px;
    }
    
    .timetable-event {
        font-size: 8px;
        padding: 1px 2px;
    }
    
    .event-actions {
        flex-direction: column;
        gap: 2px;
    }
}
</style>
@endpush
