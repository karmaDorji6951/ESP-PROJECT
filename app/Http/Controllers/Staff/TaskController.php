<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tasks = Task::where('employee_id', $user->employee_id)->paginate(15);
        return view('staff.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        if ($task->employee_id != $user->employee_id) {
            abort(403);
        }
        return view('staff.tasks.show', compact('task'));
    }
}
