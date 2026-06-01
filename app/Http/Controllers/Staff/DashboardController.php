<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\TaskEvaluation;
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
        $myEvaluations = TaskEvaluation::query()
            ->with(['task', 'evaluator'])
            ->where('staff_user_id', $user->id)
            ->latest('evaluated_at')
            ->limit(8)
            ->get();
        $notifications = $user->notifications()->latest()->limit(5)->get();

        return view('staff.dashboard', compact('summary', 'myAttendance', 'myTasks', 'myLeaves', 'myEvaluations', 'notifications'));
    }

    public function evaluationsIndex()
    {
        $user = Auth::user();

        $evaluations = TaskEvaluation::query()
            ->with([
                'task.employee.user',
                'task.assigner',
                'evaluator',
                'submission.submitter',
            ])
            ->where('staff_user_id', $user->id)
            ->latest('evaluated_at')
            ->latest('id')
            ->paginate(20);

        return view('staff.evaluations.index', compact('evaluations'));
    }

    public function showEvaluation(TaskEvaluation $evaluation)
    {
        $user = Auth::user();

        if ((int) $evaluation->staff_user_id !== (int) $user->id) {
            abort(404);
        }

        $evaluation->load(['task.employee.user', 'task.assigner', 'evaluator', 'submission.submitter']);

        return view('staff.evaluations.show', compact('evaluation'));
    }
}
