@extends('layouts.app')

@section('page_title', 'My Profile')
@section('topbar_title', 'User Profile')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <div class="avatar-upload-container">
                <div class="avatar-circle" style="width: 160px; height: 160px; border-radius: 12px; overflow: hidden; background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); display: flex; align-items: center; justify-content: center; border: 3px solid #06b6d4; box-shadow: 0 4px 16px rgba(6, 182, 212, 0.2); position: relative;">
                    @if($user->photo_url)
                        <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="text-align: center; color: #0369a1;">
                            <div style="font-size: 64px; font-weight: bold; line-height: 1;">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                        </div>
                    @endif
                    <div class="avatar-overlay">
                        <div class="upload-icon">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                        <div class="upload-text">Change Photo</div>
                    </div>
                </div>
                <form action="{{ route('profile.upload-photo') }}" method="POST" enctype="multipart/form-data" class="avatar-upload-form" style="display: none;">
                    @csrf
                    <input type="file" name="photo" id="photo-input" accept="image/*" class="photo-input">
                    <input type="hidden" name="_method" value="PUT">
                </form>
            </div>
        </div>
        <div class="profile-info">
            <h1>{{ $user->name }}</h1>
            <p class="role-badge">{{ ucfirst($user->role->name) }}</p>
            <p class="user-email">{{ $user->email }}</p>
            <div class="profile-actions">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <span class="action-icon">✏️</span>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-main">
            <!-- Personal Information -->
            <div class="profile-section">
                <h3>Personal Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Full Name</label>
                        <value>{{ $user->name }}</value>
                    </div>
                    <div class="info-item">
                        <label>Email Address</label>
                        <value>{{ $user->email }}</value>
                    </div>
                    <div class="info-item">
                        <label>Phone Number</label>
                        <value>{{ $user->employee->phone ?? $user->phone ?? 'Not provided' }}</value>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <value>{{ $user->employee->address ?? $user->address ?? 'Not provided' }}</value>
                    </div>
                    @if($user->employee)
                        <div class="info-item">
                            <label>Employee ID</label>
                            <value>{{ $user->employee->employee_id ?? 'N/A' }}</value>
                        </div>
                        <div class="info-item">
                            <label>CID</label>
                            <value>{{ $user->employee->cid ?? 'N/A' }}</value>
                        </div>
                        <div class="info-item">
                            <label>Job Type</label>
                            <value>{{ $user->employee->role_title ?? 'Not provided' }}</value>
                        </div>
                        <div class="info-item">
                            <label>Building / Area</label>
                            <value>
                                @if($user->employee?->building && $user->employee?->area)
                                    {{ $user->employee->building }} / {{ $user->employee->area }}
                                @else
                                    {{ $user->employee->area ?? 'Not provided' }}
                                @endif
                            </value>
                        </div>
                        <div class="info-item">
                            <label>Dzongkhag</label>
                            <value>{{ $user->employee->address ?? 'Not provided' }}</value>
                        </div>
                        <div class="info-item">
                            <label>Joining Date</label>
                            <value>{{ $user->employee->joining_date ? \Illuminate\Support\Carbon::parse($user->employee->joining_date)->format('M j, Y') : 'Not provided' }}</value>
                        </div>
                    @endif
                    <div class="info-item">
                        <label>Account Created</label>
                        <value>{{ $user->created_at->format('M j, Y') }}</value>
                    </div>
                    <div class="info-item">
                        <label>Last Updated</label>
                        <value>{{ $user->updated_at->format('M j, Y \a\t H:i') }}</value>
                    </div>
                </div>
            </div>

            <!-- Role-specific Statistics -->
            <div class="profile-section">
                <h3>{{ ucfirst($user->role->name) }} Statistics</h3>
                <div class="stats-grid">
                    @if($user->role->slug === 'admin')
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['total_users'] }}</div>
                                <div class="stat-label">Total Users</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['total_employees'] }}</div>
                                <div class="stat-label">Total Employees</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-list-task"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['total_tasks'] }}</div>
                                <div class="stat-label">Total Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-calendar2-event"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['pending_leaves'] }}</div>
                                <div class="stat-label">Pending Leaves</div>
                            </div>
                        </div>
                    @elseif($user->role->slug === 'supervisor')
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-list-check"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['assigned_tasks'] }}</div>
                                <div class="stat-label">Assigned Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['pending_tasks'] }}</div>
                                <div class="stat-label">Pending Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['completed_tasks'] }}</div>
                                <div class="stat-label">Completed Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['team_size'] }}</div>
                                <div class="stat-label">Team Size</div>
                            </div>
                        </div>
                    @elseif($user->role->slug === 'staff')
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-list-task"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['my_tasks'] }}</div>
                                <div class="stat-label">My Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-check2"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['completed_tasks'] }}</div>
                                <div class="stat-label">Completed Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['pending_tasks'] }}</div>
                                <div class="stat-label">Pending Tasks</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-calendar-plus"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['my_leaves'] }}</div>
                                <div class="stat-label">My Leaves</div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="bi bi-clipboard-check"></i></div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $statistics['attendance_rate'] }}%</div>
                                <div class="stat-label">Attendance Rate</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="profile-section">
                <h3>Recent Activity</h3>
                <div class="activity-list">
                    @if($user->role->slug === 'staff' && $user->employee)
                        @php
                            $recentTasks = \App\Models\Task::where('employee_id', $user->employee->id)
                                                    ->orderBy('updated_at', 'desc')
                                                    ->take(5)
                                                    ->get();
                        @endphp
                        @if($recentTasks->isNotEmpty())
                            @foreach($recentTasks as $task)
                                <div class="activity-item">
                                    <div class="activity-icon"><i class="bi bi-list-task"></i></div>
                                    <div class="activity-content">
                                        <div class="activity-title">{{ $task->title }}</div>
                                        <div class="activity-description">{{ $task->description }}</div>
                                        <div class="activity-time">{{ $task->updated_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-activity">
                                <p>No recent activity found</p>
                            </div>
                        @endif
                    @else
                        <div class="no-activity">
                            <p>Activity tracking available for staff users</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="profile-sidebar">
            <!-- Quick Actions -->
            <div class="sidebar-card">
                <h3>Quick Actions</h3>
                <div class="action-list">
                    <a href="{{ route('profile.edit') }}" class="action-item">
                        <span class="action-icon"><i class="bi bi-pencil-square"></i></span>
                        <span>Edit Profile</span>
                    </a>
                    @if($user->role->slug === 'staff')
                        <a href="{{ route('staff.leaves.index') }}" class="action-item">
                            <span class="action-icon"><i class="bi bi-calendar-plus"></i></span>
                            <span>My Leaves</span>
                        </a>
                        <a href="{{ route('staff.tasks.index') }}" class="action-item">
                            <span class="action-icon"><i class="bi bi-list-task"></i></span>
                            <span>My Tasks</span>
                        </a>
                    @elseif($user->role->slug === 'supervisor')
                        <a href="{{ route('supervisor.tasks.index') }}" class="action-item">
                            <span class="action-icon"><i class="bi bi-list-check"></i></span>
                            <span>Manage Tasks</span>
                        </a>
                        <a href="{{ route('supervisor.attendance.index') }}" class="action-item">
                            <span class="action-icon"><i class="bi bi-clipboard-check"></i></span>
                            <span>Attendance</span>
                        </a>
                    @elseif($user->role->slug === 'admin')
                        <a href="{{ route('admin.users.index') }}" class="action-item">
                            <span class="action-icon"><i class="bi bi-people"></i></span>
                            <span>Manage Users</span>
                        </a>
                        <a href="{{ route('admin.employees.index') }}" class="action-item">
                            <span class="action-icon"><i class="bi bi-person-badge"></i></span>
                            <span>Manage Employees</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Account Settings -->
            <div class="sidebar-card">
                <h3>Account Settings</h3>
                <div class="settings-list">
                    <div class="setting-item">
                        <span class="setting-icon"><i class="bi bi-envelope"></i></span>
                        <div class="setting-info">
                            <div class="setting-title">Email Notifications</div>
                            <div class="setting-description">Receive email updates</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <span class="setting-icon"><i class="bi bi-shield-lock"></i></span>
                        <div class="setting-info">
                            <div class="setting-title">Two-Factor Auth</div>
                            <div class="setting-description">Extra security layer</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-container {
    --bg-primary: #ffffff;
    --bg-secondary: #f5f1e8;
    --border-color: #d4c4a8;
    --text-primary: #2c3e50;
    --text-secondary: #4f6472;
    --text-muted: #7a6a5a;
    --supervisor-accent: #2c3e50;
    --supervisor-dark: #1a252f;
}

.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 32px;
    padding: 24px;
    background: linear-gradient(135deg, #2c3e50 0%, #3d5568 100%);
    border-radius: 12px;
    color: white;
}

.profile-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.avatar-upload-container {
    position: relative;
    display: inline-block;
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
    border-radius: 12px;
}

.avatar-circle:hover .avatar-overlay {
    opacity: 1;
}

.upload-icon {
    font-size: 32px;
    color: white;
    margin-bottom: 8px;
}

.upload-text {
    color: white;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
}

.photo-input {
    display: none;
}

.avatar-text {
    font-size: 28px;
    font-weight: 700;
    color: white;
}

.profile-info {
    flex: 1;
}

.profile-info h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #f5f1e8;
}

.role-badge {
    display: inline-block;
    padding: 4px 12px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
}

.user-email {
    color: rgba(245, 241, 232, 0.88);
    margin-bottom: 16px;
}

.profile-actions {
    display: flex;
    gap: 12px;
}

.profile-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
}

.profile-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.profile-section {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 24px;
    box-shadow: 0 4px 16px rgba(44, 62, 80, 0.06);
}

.profile-section h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.info-item label {
    font-size: 12px;
    font-weight: 500;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item value {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-card {
    background: linear-gradient(135deg, #f5f1e8 0%, #ede6d9 100%);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    font-size: 32px;
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #2c3e50 0%, #7a9fb5 100%);
    color: #f5f1e8;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    background-color: var(--bg-secondary);
    border-radius: 6px;
}

.activity-icon {
    font-size: 20px;
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #2c3e50 0%, #7a9fb5 100%);
    color: #f5f1e8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.activity-description {
    color: var(--text-secondary);
    font-size: 14px;
    margin-bottom: 4px;
}

.activity-time {
    color: var(--text-muted);
    font-size: 12px;
}

.no-activity {
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
}

.profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.sidebar-card {
    background-color: var(--bg-primary);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 20px;
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
}

.action-item:hover {
    background-color: #ede6d9;
}

.action-icon {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.settings-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.setting-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.setting-icon {
    font-size: 16px;
    width: 32px;
    height: 32px;
    background: var(--bg-secondary);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.setting-info {
    flex: 1;
}

.setting-title {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.setting-description {
    font-size: 12px;
    color: var(--text-muted);
}

.switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: var(--supervisor-accent);
}

input:checked + .slider:before {
    transform: translateX(20px);
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
    background: linear-gradient(135deg, #d4c4a8 0%, #c9b8a0 100%);
    color: #2c3e50;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #c9b8a0 0%, #b8a890 100%);
    transform: translateY(-2px);
}

.profile-container,
.profile-container p,
.profile-container span,
.profile-container div,
.profile-container label,
.profile-container a {
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-content {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarOverlay = document.querySelector('.avatar-overlay');
    const photoInput = document.getElementById('photo-input');
    const uploadForm = document.querySelector('.avatar-upload-form');
    
    if (avatarOverlay && photoInput && uploadForm) {
        // Click on overlay to trigger file input
        avatarOverlay.addEventListener('click', function(e) {
            e.preventDefault();
            photoInput.click();
        });
        
        // Handle file selection
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Image size should be less than 5MB.');
                    return;
                }
                
                // Show loading state
                avatarOverlay.innerHTML = `
                    <div class="upload-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="upload-text">Uploading...</div>
                `;
                
                // Create FormData and submit via AJAX
                const formData = new FormData(uploadForm);
                formData.append('photo', file);
                
                fetch('{{ route("profile.upload-photo") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload page to show new photo
                        window.location.reload();
                    } else {
                        const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                        alert(firstError || data.message || 'Error uploading photo. Please try again.');
                        // Reset overlay
                        avatarOverlay.innerHTML = `
                            <div class="upload-icon">
                                <i class="bi bi-camera-fill"></i>
                            </div>
                            <div class="upload-text">Change Photo</div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error uploading photo. Please try again.');
                    // Reset overlay
                    avatarOverlay.innerHTML = `
                        <div class="upload-icon">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                        <div class="upload-text">Change Photo</div>
                    `;
                });
            }
        });
    }
});
</script>
@endpush
