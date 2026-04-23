<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Supervisor\AttendanceController as SupervisorAttendanceController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\TaskController as SupervisorTaskController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\LeaveController as StaffLeaveController;
use App\Http\Controllers\Staff\TaskController as StaffTaskController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Landing Page - Redirect to login or dashboard
Route::get('/', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    $role = optional($user->role)->slug;
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'supervisor') {
        return redirect()->route('supervisor.dashboard');
    } elseif ($role === 'staff') {
        return redirect()->route('staff.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('employees', AdminEmployeeController::class);
});

// Supervisor Routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('tasks', SupervisorTaskController::class);
    Route::resource('attendance', SupervisorAttendanceController::class)->only(['index', 'create', 'store']);
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::resource('tasks', StaffTaskController::class)->only(['index', 'show']);
    Route::resource('leaves', StaffLeaveController::class);
});

// Timetable Routes - Shared across all roles with role-based permissions
Route::middleware(['auth'])->get('/timetables/day-details', [TimetableController::class, 'dayDetails'])->name('timetables.day-details');
Route::middleware(['auth'])->resource('timetables', TimetableController::class);

// Profile Routes - Available to all authenticated users
Route::middleware(['auth'])->get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::middleware(['auth'])->get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::middleware(['auth'])->put('/profile', [ProfileController::class, 'update'])->name('profile.update');

// Fallback dashboard route
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    $role = optional($user->role)->slug;
    
    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'supervisor') {
        return redirect()->route('supervisor.dashboard');
    } elseif ($role === 'staff') {
        return redirect()->route('staff.dashboard');
    }
    
    return view('welcome');
})->name('dashboard');
