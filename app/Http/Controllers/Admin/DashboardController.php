<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $summary = [
            'total_users' => User::count(),
            'total_supervisors' => User::whereHas('role', function ($q) {
                $q->where('slug', 'supervisor');
            })->count(),
            'total_staff' => User::whereHas('role', function ($q) {
                $q->where('slug', 'staff');
            })->count(),
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'Active')->count(),
            'present_today' => Attendance::whereDate('attendance_date', today())->where('status', 'Present')->count(),
            'pending_tasks' => Task::where('status', 'Pending')->count(),
            'pending_leaves' => LeaveRequest::where('status', 'Pending')->count(),
        ];

        $recentUsers = User::with('role')->latest()->limit(8)->get();
        $recentEmployees = Employee::latest()->limit(8)->get();
        $recentAttendance = Attendance::with('employee')->latest('attendance_date')->limit(8)->get();
        $recentTasks = Task::with('employee')->latest()->limit(8)->get();
        $recentLeaves = LeaveRequest::with('employee', 'user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('summary', 'recentUsers', 'recentEmployees', 'recentAttendance', 'recentTasks', 'recentLeaves'));
    }
}
