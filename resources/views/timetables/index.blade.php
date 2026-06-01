@extends('layouts.app')

@section('page_title', 'Schedule')
@section('topbar_title', 'Schedule')

@section('content')
<div class="timetable-container">
    <div class="app-page-hero mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
            <div>
                <div class="app-page-hero-kicker mb-2">Workspace</div>
                <h1 class="app-page-hero-title mb-2">Schedule</h1>
                <p class="app-page-hero-subtitle">Manage and view scheduled tasks and activities</p>
            </div>
            @if($canCreate)
                <a href="{{ route('timetables.create') }}" class="btn btn-light app-page-hero-action">
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
    --bg-primary: #ffffff;
    --bg-secondary: #f5f1e8;
    --border-color: #d4c4a8;
    --text-primary: #2c3e50;
    --text-secondary: #4f6472;
    --text-muted: #7a6a5a;
    --supervisor-accent: #2c3e50;
    --supervisor-dark: #1a252f;
    max-width: 1280px;
    margin: 0 auto;
    padding: 12px 8px 24px;
}

.timetable-container {
    color: var(--text-primary);
}

.timetable-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-bottom: 20px;
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
    margin: 0;
}

.header-right {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
    align-items: center;
}

.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    padding: 16px 18px;
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 10px rgba(44, 62, 80, 0.05);
}

.view-tabs {
    display: flex;
    gap: 8px;
}

.view-tab {
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-secondary);
    font-weight: 500;
    transition: all 0.3s;
    border: 1px solid transparent;
    background: transparent;
}

.view-tab:hover {
    background-color: #ede6d9;
    color: var(--text-primary);
}

.view-tab.active {
    background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
    color: #f5f1e8;
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.18);
}

.date-navigation {
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-btn {
    background: #ede6d9;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 8px 12px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s;
    color: var(--text-primary);
}

.nav-btn:hover {
    background-color: #d4c4a8;
}

.date-input {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    color: var(--text-primary);
    background: #ffffff;
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
    box-shadow: 0 4px 12px rgba(44, 62, 80, 0.18);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3d5568 0%, #1a252f 100%);
    transform: translateY(-2px);
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: linear-gradient(135deg, #d9e8e1 0%, #f5f1e8 100%);
    color: #2c3e50;
    border: 1px solid #b7c9bf;
}

.alert-icon {
    font-size: 18px;
}

.text-muted {
    color: var(--text-muted);
}

.timetable-container h1,
.timetable-container h2,
.timetable-container h3,
.timetable-container p {
    color: var(--text-primary);
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
        align-items: stretch;
    }
    
    .btn {
        width: 100%;
    }

    .view-tabs,
    .date-navigation {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .header-right {
        width: 100%;
        justify-content: space-between;
        flex-wrap: wrap;
    }
}
</style>
@endpush
