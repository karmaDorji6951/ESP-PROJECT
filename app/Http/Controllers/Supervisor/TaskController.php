<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('employee', 'assigner')->paginate(15);
        return view('supervisor.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $employees = \App\Models\Employee::all();
        return view('supervisor.tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'deadline' => 'nullable|date',
        ]);

        $validated['assigned_by'] = auth()->id();
        Task::create($validated);

        return redirect()->route('supervisor.tasks.index')->with('success', 'Task assigned successfully.');
    }

    public function edit(Task $task)
    {
        $employees = \App\Models\Employee::all();
        return view('supervisor.tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'deadline' => 'nullable|date',
        ]);

        $task->update($validated);
        return redirect()->route('supervisor.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('supervisor.tasks.index')->with('success', 'Task deleted successfully.');
    }
}
