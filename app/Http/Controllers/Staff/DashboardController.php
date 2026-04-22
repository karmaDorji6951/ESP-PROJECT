<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $summary = [
            'my_tasks' => Task::where('employee_id', $user->employee_id)->count(),
            'completed_tasks' => Task::where('employee_id', $user->employee_id)->where('status', 'Completed')->count(),
            'pending_tasks' => Task::where('employee_id', $user->employee_id)->where('status', 'Pending')->count(),
            'in_progress_tasks' => Task::where('employee_id', $user->employee_id)->where('status', 'In Progress')->count(),
            'total_leaves' => LeaveRequest::where('employee_id', $user->employee_id)->count(),
            'approved_leaves' => LeaveRequest::where('employee_id', $user->employee_id)->where('status', 'Approved')->count(),
            'pending_leaves' => LeaveRequest::where('employee_id', $user->employee_id)->where('status', 'Pending')->count(),
        ];

        $myAttendance = Attendance::where('employee_id', $user->employee_id)->latest('attendance_date')->limit(10)->get();
        $myTasks = Task::where('employee_id', $user->employee_id)->latest()->limit(8)->get();
        $myLeaves = LeaveRequest::where('employee_id', $user->employee_id)->latest()->limit(8)->get();

        return view('staff.dashboard', compact('summary', 'myAttendance', 'myTasks', 'myLeaves'));
    }
}
