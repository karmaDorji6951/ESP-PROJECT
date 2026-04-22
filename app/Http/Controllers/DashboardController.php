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
        $role = $user->role?->slug ?? 'staff';

        // Route to appropriate dashboard based on role
        return match ($role) {
            'admin' => $this->adminDashboard($user),
            'supervisor' => $this->supervisorDashboard($user),
            default => $this->employeeDashboard($user),
        };
    }

    /**
     * Admin Dashboard - Full system overview and control
     */
    private function adminDashboard($user)
    {
        $summary = [
            'total_staff' => Employee::count(),
            'present_today' => Attendance::whereDate('attendance_date', today())
                ->where('status', 'Present')
                ->count(),
            'pending_leaves' => LeaveRequest::where('status', 'Pending')->count(),
            'total_tasks' => Task::count(),
        ];

        $taskStats = [
            'pending' => Task::where('status', 'Pending')->count(),
            'in_progress' => Task::where('status', 'In Progress')->count(),
            'completed' => Task::where('status', 'Completed')->count(),
        ];

        $recentAttendance = Attendance::with('employee')
            ->latest('attendance_date')
            ->limit(10)
            ->get();

        $recentTasks = Task::with('employee', 'assigner')
            ->latest()
            ->limit(10)
            ->get();

        $pendingLeaves = LeaveRequest::with('employee', 'user')
            ->where('status', 'Pending')
            ->latest()
            ->limit(8)
            ->get();

        $notifications = $user->notifications()->latest()->limit(5)->get();

        return view('dashboards.admin-dashboard', compact(
            'user',
            'summary',
            'taskStats',
            'recentAttendance',
            'recentTasks',
            'pendingLeaves',
            'notifications'
        ));
    }

    /**
     * Supervisor Dashboard - Staff and task management
     */
    private function supervisorDashboard($user)
    {
        $summary = [
            'total_staff' => Employee::count(),
            'present_today' => Attendance::whereDate('attendance_date', today())
                ->where('status', 'Present')
                ->count(),
            'my_tasks_assigned' => Task::where('assigned_by', $user->id)->count(),
            'pending_approvals' => LeaveRequest::where('status', 'Pending')->count(),
        ];

        $taskStats = [
            'pending' => Task::where('assigned_by', $user->id)
                ->where('status', 'Pending')
                ->count(),
            'in_progress' => Task::where('assigned_by', $user->id)
                ->where('status', 'In Progress')
                ->count(),
            'completed' => Task::where('assigned_by', $user->id)
                ->where('status', 'Completed')
                ->count(),
        ];

        $myAssignedTasks = Task::with('employee')
            ->where('assigned_by', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $staffAttendance = Attendance::with('employee')
            ->whereDate('attendance_date', today())
            ->latest()
            ->limit(8)
            ->get();

        $pendingLeaves = LeaveRequest::with('employee', 'user')
            ->where('status', 'Pending')
            ->latest()
            ->limit(8)
            ->get();

        $notifications = $user->notifications()->latest()->limit(5)->get();

        return view('dashboards.supervisor-dashboard', compact(
            'user',
            'summary',
            'taskStats',
            'myAssignedTasks',
            'staffAttendance',
            'pendingLeaves',
            'notifications'
        ));
    }

    /**
     * Employee/ESP Dashboard - Personal overview
     */
    private function employeeDashboard($user)
    {
        $employee = $user->employee;

        $summary = [
            'my_tasks_assigned' => Task::where('employee_id', $employee?->id)->count(),
            'pending_tasks' => Task::where('employee_id', $employee?->id)
                ->where('status', '!=', 'Completed')
                ->count(),
            'completed_tasks' => Task::where('employee_id', $employee?->id)
                ->where('status', 'Completed')
                ->count(),
            'pending_leave_requests' => LeaveRequest::where('user_id', $user->id)
                ->where('status', 'Pending')
                ->count(),
        ];

        $myTasks = Task::with('assigner')
            ->where('employee_id', $employee?->id)
            ->latest()
            ->limit(8)
            ->get();

        $myAttendance = Attendance::where('employee_id', $employee?->id)
            ->latest('attendance_date')
            ->limit(10)
            ->get();

        $myLeaveRequests = LeaveRequest::where('user_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        $notifications = $user->notifications()->latest()->limit(5)->get();

        return view('dashboards.employee-dashboard', compact(
            'user',
            'employee',
            'summary',
            'myTasks',
            'myAttendance',
            'myLeaveRequests',
            'notifications'
        ));
    }
}
