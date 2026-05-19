<?php

namespace App\Http\Controllers\Supervisor;

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
            'total_staff' => User::whereHas('role', function ($q) {
                $q->where('slug', 'staff');
            })->count(),
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'Active')->count(),
            'present_today' => Attendance::whereDate('attendance_date', today())->where('status', 'Present')->count(),
            'absent_today' => Attendance::whereDate('attendance_date', today())->where('status', 'Absent')->count(),
            'on_leave_today' => Attendance::whereDate('attendance_date', today())->where('status', 'Leave')->count(),
            'pending_tasks' => Task::where('status', 'Pending')->count(),
            'in_progress_tasks' => Task::where('status', 'In Progress')->count(),
            'pending_leaves' => LeaveRequest::where('status', 'Pending')->count(),
        ];

        $recentEmployees = Employee::latest()->limit(8)->get();
        $recentAttendance = Attendance::with('employee')->latest('attendance_date')->limit(8)->get();
        $recentTasks = Task::with('employee')->latest()->limit(8)->get();

        return view('supervisor.dashboard', compact('summary', 'recentEmployees', 'recentAttendance', 'recentTasks'));
    }
}
