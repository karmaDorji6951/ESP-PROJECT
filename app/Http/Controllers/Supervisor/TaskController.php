<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('employee', 'assigner')
            ->where('assigned_by', auth()->id())
            ->latest()
            ->paginate(15);
        return view('supervisor.tasks.index', compact('tasks'));
    }

    public function show(Task $task)
    {
        if ($task->assigned_by !== auth()->id()) {
            abort(404);
        }

        $task->load([
            'employee.user',
            'assigner',
            'timetable',
            'latestSubmission.submitter',
            'evaluation',
        ]);

        return view('supervisor.tasks.show', compact('task'));
    }

    public function create()
    {
        $departments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $employeesQuery = Employee::query()->orderBy('name');

        if ($departments->isNotEmpty()) {
            $employeesQuery
                ->whereNotNull('department')
                ->where('department', '!=', '');
        }

        $employees = $employeesQuery->get();

        return view('supervisor.tasks.create', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        $hasDepartments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->exists();

        $department = $request->input('department');
        $employeeExistsRule = Rule::exists('employees', 'id');

        if (! empty($department)) {
            $employeeExistsRule = Rule::exists('employees', 'id')
                ->where(fn ($query) => $query->where('department', $department));
        }

        $validated = $request->validate([
            'department' => $hasDepartments ? 'required|string' : 'nullable|string',
            'employee_id' => ['required', $employeeExistsRule],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'deadline' => 'nullable|date',
        ]);

        unset($validated['department']);

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
        $departments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $employeesQuery = Employee::query()->orderBy('name');

        if ($departments->isNotEmpty()) {
            $employeesQuery
                ->whereNotNull('department')
                ->where('department', '!=', '');
        }

        $employees = $employeesQuery->get();

        $task->loadMissing('employee');

        return view('supervisor.tasks.edit', compact('task', 'employees', 'departments'));
    }

    public function update(Request $request, Task $task)
    {
        $hasDepartments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->exists();

        $department = $request->input('department');
        $employeeExistsRule = Rule::exists('employees', 'id');

        if (! empty($department)) {
            $employeeExistsRule = Rule::exists('employees', 'id')
                ->where(fn ($query) => $query->where('department', $department));
        }

        $validated = $request->validate([
            'department' => $hasDepartments ? 'required|string' : 'nullable|string',
            'employee_id' => ['required', $employeeExistsRule],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'deadline' => 'nullable|date',
        ]);

        unset($validated['department']);

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
