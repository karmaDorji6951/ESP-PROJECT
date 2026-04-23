@extends('layouts.app')

@section('page_title', 'Edit Profile')
@section('topbar_title', 'Edit Profile')

@section('content')
<div class="profile-edit-container">
    <div class="edit-header">
        <h1>Edit Profile</h1>
        <p>Update your personal information and account settings</p>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
        @csrf
        @method('PUT')

        <div class="form-sections">
            <!-- Personal Information -->
            <div class="form-section">
                <h3>Personal Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($user->employee)
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->employee->phone) }}" 
                                   class="form-control @error('phone') is-invalid @enderror" placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="123 Main St, City, State 12345">{{ old('address', $user->employee->address) }}</textarea>
                            @error('address')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Information -->
            <div class="form-section">
                <h3>Account Information</h3>
                <div class="account-info">
                    <div class="info-row">
                        <label>Account Type</label>
                        <value>{{ ucfirst($user->role->name) }}</value>
                    </div>
                    @if($user->employee)
                        <div class="info-row">
                            <label>Employee ID</label>
                            <value>{{ $user->employee->employee_id ?? 'N/A' }}</value>
                        </div>
                    @endif
                    <div class="info-row">
                        <label>Account Created</label>
                        <value>{{ $user->created_at->format('M j, Y') }}</value>
                    </div>
                    <div class="info-row">
                        <label>Last Updated</label>
                        <value>{{ $user->updated_at->format('M j, Y \a\t H:i') }}</value>
                    </div>
                </div>
            </div>

            <!-- Password Change -->
            <div class="form-section">
                <h3>Password Change</h3>
                <div class="password-note">
                    <p>Leave these fields blank if you don't want to change your password</p>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-control" placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="form-control" placeholder="Enter new password" minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="form-control" placeholder="Confirm new password">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('profile.show') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <span class="btn-icon">â</span>
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.profile-edit-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.edit-header {
    margin-bottom: 32px;
}

.edit-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.edit-header p {
    color: var(--text-secondary);
    font-size: 16px;
}

.profile-form {
    background-color: var(--bg-primary);
    border-radius: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.form-sections {
    padding: 32px;
}

.form-section {
    margin-bottom: 40px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    padding-bottom: 8px;
    border-bottom: 2px solid var(--supervisor-accent);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
}

.form-control {
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
    background-color: var(--bg-primary);
    color: var(--text-primary);
}

.form-control:focus {
    outline: none;
    border-color: var(--supervisor-accent);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-control.is-invalid {
    border-color: var(--danger);
}

.form-control.is-invalid:focus {
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-control::placeholder {
    color: var(--text-muted);
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

.error-message {
    color: var(--danger);
    font-size: 12px;
    margin-top: 4px;
}

.account-info {
    background-color: var(--bg-secondary);
    border-radius: 8px;
    padding: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.info-row:last-child {
    border-bottom: none;
}

.info-row label {
    font-weight: 500;
    color: var(--text-primary);
}

.info-row value {
    color: var(--text-secondary);
    font-weight: 400;
}

.password-note {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
    padding: 12px 16px;
    margin-bottom: 20px;
}

.password-note p {
    color: #856404;
    font-size: 14px;
    margin: 0;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 24px 32px;
    background-color: var(--bg-secondary);
    border-top: 1px solid var(--border-color);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
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

.btn-secondary {
    background-color: var(--bg-primary);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
}

.btn-icon {
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-sections {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column;
        padding: 20px;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush
