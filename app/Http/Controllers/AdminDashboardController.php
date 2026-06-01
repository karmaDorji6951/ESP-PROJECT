<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
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
        $recentLeaves = LeaveRequest::with('employee', 'user')->latest()->limit(8)->get();
        $evaluationTaskGroups = Role::query()
            ->orderBy('name')
            ->get()
            ->map(function (Role $role) {
                $tasks = Task::with(['employee.user.role', 'assigner'])
                    ->where('status', 'Completed')
                    ->whereHas('employee.user.role', function ($query) use ($role) {
                        $query->whereKey($role->id);
                    })
                    ->latest()
                    ->limit(10)
                    ->get();

                return [
                    'role' => $role,
                    'tasks' => $tasks,
                ];
            })
            ->filter(fn (array $group) => $group['tasks']->isNotEmpty())
            ->values();

        $selectedEvaluationTask = $evaluationTaskGroups->first()['tasks']->first() ?? null;

        return view('admin.dashboard', compact('summary', 'recentUsers', 'recentEmployees', 'recentLeaves', 'evaluationTaskGroups', 'selectedEvaluationTask'));
    }
}
