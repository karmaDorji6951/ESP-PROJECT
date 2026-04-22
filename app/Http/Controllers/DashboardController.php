<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $summary = [
            'total_staff' => Employee::count(),
            'present_today' => Attendance::whereDate('attendance_date', today())->where('status', 'Present')->count(),
            'pending_tasks' => Task::where('status', 'Pending')->count(),
            'ongoing_tasks' => Task::where('status', 'In Progress')->count(),
            'pending_leaves' => LeaveRequest::where('status', 'Pending')->count(),
        ];

        $recentAttendance = Attendance::with('employee')->latest('attendance_date')->limit(8)->get();
        $recentTasks = Task::with('employee')->latest()->limit(8)->get();
        $recentLeaves = LeaveRequest::with('employee', 'user')->latest()->limit(8)->get();
        $notifications = $user->notifications()->latest()->limit(5)->get();

        return view('dashboard', compact('summary', 'recentAttendance', 'recentTasks', 'recentLeaves', 'notifications'));
    }
}
