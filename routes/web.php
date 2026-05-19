<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Supervisor\AttendanceController as SupervisorAttendanceController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\LeaveController as SupervisorLeaveController;
use App\Http\Controllers\Supervisor\TaskEvaluationController as SupervisorTaskEvaluationController;
use App\Http\Controllers\Supervisor\TaskController as SupervisorTaskController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LeaveController as StaffLeaveController;
use App\Http\Controllers\Staff\TaskController as StaffTaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Default Route - Redirect to login or dashboard
Route::get('/', function () {
    $user = auth()->user();
    if (!$user) {
        return view('landing');
    }
    $role = trim((string) optional($user->role)->slug);
    $role = $role !== '' ? strtolower($role) : strtolower(trim((string) optional($user->role)->name));
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'supervisor') {
        return redirect()->route('supervisor.dashboard');
    } elseif ($role === 'staff') {
        return redirect()->route('staff.dashboard');
    }
    // If the user is authenticated but their role isn't one of the supported
    // dashboard roles, keep them in an authenticated area instead of bouncing
    // them back to the login page (which can create redirect loops).
    return redirect()->route('dashboard');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
});

Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Convenience route used by some shared views/dashboards.
// Redirects to the correct role-scoped tasks index.
Route::middleware('auth')->get('/tasks', function () {
    $user = auth()->user();
    $role = trim((string) optional($user->role)->slug);
    $role = $role !== '' ? strtolower($role) : strtolower(trim((string) optional($user->role)->name));

    if ($role === 'supervisor') {
        return redirect()->route('supervisor.tasks.index');
    }

    if ($role === 'staff') {
        return redirect()->route('staff.tasks.index');
    }

    // Admin doesn't have a dedicated tasks module in this codebase.
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    abort(403);
})->name('tasks.index');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('/analytics/export', [AnalyticsController::class, 'exportReport'])->name('analytics.export');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::resource('users', AdminUserController::class);
    Route::resource('employees', AdminEmployeeController::class);
    Route::resource('leaves', AdminLeaveController::class);
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [\App\Http\Controllers\Supervisor\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [\App\Http\Controllers\Supervisor\ReportController::class, 'generate'])->name('reports.generate');
    Route::delete('/reports/{report}', [\App\Http\Controllers\Supervisor\ReportController::class, 'destroy'])->name('reports.destroy');
    Route::resource('tasks', SupervisorTaskController::class);
    Route::get('tasks/{task}/evaluation', [SupervisorTaskEvaluationController::class, 'create'])->name('tasks.evaluation.create');
    Route::post('tasks/{task}/evaluation', [SupervisorTaskEvaluationController::class, 'store'])->name('tasks.evaluation.store');
    Route::resource('attendance', SupervisorAttendanceController::class)->only(['index', 'create', 'store']);
    Route::resource('leaves', SupervisorLeaveController::class);
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::resource('tasks', StaffTaskController::class)->only(['index', 'show']);
    Route::post('/tasks/{task}/perform', [StaffTaskController::class, 'perform'])->name('tasks.perform');
    Route::resource('leaves', StaffLeaveController::class);
});

// Timetable Routes - Shared across all roles with role-based permissions
Route::middleware(['auth'])->get('/timetables/day-details', [TimetableController::class, 'dayDetails'])->name('timetables.day-details');
Route::middleware(['auth'])->resource('timetables', TimetableController::class);

// Notification Routes - Available to all authenticated users
Route::middleware(['auth'])->get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::middleware(['auth'])->post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::middleware(['auth'])->any('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

// Profile Routes - Available to all authenticated users
Route::middleware(['auth'])->get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::middleware(['auth'])->get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::middleware(['auth'])->put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::middleware(['auth'])->put('/profile/photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload-photo');

// Fallback dashboard route
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    $role = trim((string) optional($user->role)->slug);
    $role = $role !== '' ? strtolower($role) : strtolower(trim((string) optional($user->role)->name));
    
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'supervisor') {
        return redirect()->route('supervisor.dashboard');
    } elseif ($role === 'staff') {
        return redirect()->route('staff.dashboard');
    }
    
    return view('welcome');
})->name('dashboard');
