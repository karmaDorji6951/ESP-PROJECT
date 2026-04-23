<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Timetable;
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
        $task = Task::create($validated);

        // Create corresponding timetable entry
        $timetable = Timetable::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['deadline'] ?? now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'status' => 'scheduled',
            'priority' => 'medium',
            'employee_id' => $validated['employee_id'],
            'assigned_by' => auth()->id(),
            'task_id' => $task->id,
        ]);

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

        // Sync timetable if it exists
        if ($task->timetable) {
            $timetableStatus = match($validated['status']) {
                'Completed' => 'completed',
                'In Progress' => 'in_progress',
                default => 'scheduled',
            };

            $task->timetable->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'date' => $validated['deadline'] ?? now()->toDateString(),
                'status' => $timetableStatus,
                'employee_id' => $validated['employee_id'],
            ]);
        }

        return redirect()->route('supervisor.tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        // Delete associated timetable entry if it exists
        if ($task->timetable) {
            $task->timetable->delete();
        }

        $task->delete();
        return redirect()->route('supervisor.tasks.index')->with('success', 'Task deleted successfully.');
    }
}
