@extends('layouts.app')

@section('page_title', 'Timetable')
@section('topbar_title', 'Timetable Schedule')

@section('content')
<div class="timetable-container">
    <!-- Header with View Controls -->
    <div class="timetable-header">
        <div class="header-left">
            <h1>Timetable Schedule</h1>
            <p class="text-muted">Manage and view scheduled tasks and activities</p>
        </div>
        <div class="header-right">
            @if($canCreate)
                <a href="{{ route('timetables.create') }}" class="btn btn-primary">
                    + Add Schedule
                </a>
            @endif
        </div>
    </div>

    <!-- View Controls -->
    <div class="view-controls">
        <div class="view-tabs">
            <a href="{{ route('timetables.index', ['view' => 'day', 'date' => $date]) }}" 
               class="view-tab {{ $view === 'day' ? 'active' : '' }}">
                Day
            </a>
            <a href="{{ route('timetables.index', ['view' => 'week', 'date' => $date]) }}" 
               class="view-tab {{ $view === 'week' ? 'active' : '' }}">
                Week
            </a>
            <a href="{{ route('timetables.index', ['view' => 'month', 'date' => $date]) }}" 
               class="view-tab {{ $view === 'month' ? 'active' : '' }}">
                Month
            </a>
        </div>
        <div class="date-navigation">
            <button onclick="navigateDate(-1)" class="nav-btn">‹</button>
            <input type="date" id="datePicker" value="{{ $date }}" class="date-input" onchange="changeDate()">
            <button onclick="navigateDate(1)" class="nav-btn">›</button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        {{ session('success') }}
    </div>
    @endif

    <!-- Timetable Display -->
    @if($view === 'day')
        @include('timetables.views.day')
    @elseif($view === 'week')
        @include('timetables.views.week')
    @else
        @include('timetables.views.month')
    @endif
</div>

<script>
function navigateDate(direction) {
    const currentView = '{{ $view }}';
    const currentDate = new Date('{{ $date }}');
    
    if (currentView === 'day') {
        currentDate.setDate(currentDate.getDate() + direction);
    } else if (currentView === 'week') {
        currentDate.setDate(currentDate.getDate() + (7 * direction));
    } else if (currentView === 'month') {
        currentDate.setMonth(currentDate.getMonth() + direction);
    }
    
    const newDate = currentDate.toISOString().split('T')[0];
    window.location.href = `{{ route('timetables.index') }}?view=${currentView}&date=${newDate}`;
}

function changeDate() {
    const newDate = document.getElementById('datePicker').value;
    const currentView = '{{ $view }}';
    window.location.href = `{{ route('timetables.index') }}?view=${currentView}&date=${newDate}`;
}
</script>
@endsection

@push('styles')
<style>
.timetable-container {
    max-width: 1400px;
    margin: 0 auto;
}

.timetable-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.header-left h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.header-left p {
    color: var(--text-muted);
    font-size: 14px;
}

.header-right {
    display: flex;
    gap: 12px;
}

.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding: 16px;
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.view-tabs {
    display: flex;
    gap: 8px;
}

.view-tab {
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
    color: var(--text-secondary);
    font-weight: 500;
    transition: all 0.3s;
    border: 1px solid transparent;
}

.view-tab:hover {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
}

.view-tab.active {
    background-color: var(--supervisor-accent);
    color: white;
}

.date-navigation {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-btn {
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s;
}

.nav-btn:hover {
    background-color: var(--supervisor-light);
}

.date-input {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
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

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background-color: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-icon {
    font-size: 18px;
}

.text-muted {
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .timetable-header {
        flex-direction: column;
        gap: 16px;
    }
    
    .view-controls {
        flex-direction: column;
        gap: 16px;
    }
    
    .btn {
        width: 100%;
    }
}
</style>
@endpush
