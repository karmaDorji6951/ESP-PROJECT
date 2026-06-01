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
        $tasks = Task::with('employee', 'assigner', 'latestSubmission', 'evaluation')
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
        $departments = \App\Models\Department::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $employeesQuery = Employee::with('departmentRelation.parent')->orderBy('name');

        if (Employee::query()->whereNotNull('department_id')->exists()) {
            $employeesQuery->whereNotNull('department_id');
        }

        $employees = $employeesQuery->get();

        return view('supervisor.tasks.create', compact('employees', 'departments'));
    }

    public function store(Request $request)
    {
        $hasDepartments = Employee::query()
            ->whereNotNull('department_id')
            ->exists();

        $department = $request->input('department');
        $employeeExistsRule = Rule::exists('employees', 'id');

        if (! empty($department)) {
            $employeeExistsRule = Rule::exists('employees', 'id')
                ->where(fn ($query) => $query->where('department_id', $department));
        }

        $validated = $request->validate([
            'department' => $hasDepartments ? 'required|integer|exists:departments,id' : 'nullable',
            'employee_id' => ['required', $employeeExistsRule],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'deadline' => 'nullable|date',
        ]);

        unset($validated['department']);

        $scheduleDate = ($validated['deadline'] ?? null)
            ? \Carbon\Carbon::parse($validated['deadline'])->toDateString()
            : now()->toDateString();

        $validated['assigned_by'] = auth()->id();
        $validated['assignment_type'] = 'date';
        $validated['schedule_start_date'] = $scheduleDate;
        $validated['schedule_end_date'] = $scheduleDate;
        $validated['deadline'] = $validated['deadline'] ?? $scheduleDate;

        $task = Task::create($validated);

        $timetableStatus = match ($validated['status']) {
            'Completed' => 'completed',
            'In Progress' => 'in_progress',
            default => 'scheduled',
        };

        // Create corresponding timetable entry
        Timetable::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $scheduleDate,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'status' => $timetableStatus,
            'priority' => 'medium',
            'employee_id' => $validated['employee_id'],
            'assigned_by' => auth()->id(),
            'task_id' => $task->id,
        ]);

        return redirect()->route('supervisor.tasks.index')->with('success', 'Task assigned successfully.');
    }

    public function edit(Task $task)
    {
        $departments = \App\Models\Department::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $employeesQuery = Employee::with('departmentRelation.parent')->orderBy('name');

        if (Employee::query()->whereNotNull('department_id')->exists()) {
            $employeesQuery->whereNotNull('department_id');
        }

        $employees = $employeesQuery->get();

        $task->loadMissing('employee.departmentRelation.parent');

        return view('supervisor.tasks.edit', compact('task', 'employees', 'departments'));
    }

    public function update(Request $request, Task $task)
    {
        $hasDepartments = Employee::query()
            ->whereNotNull('department_id')
            ->exists();

        $department = $request->input('department');
        $employeeExistsRule = Rule::exists('employees', 'id');

        if (! empty($department)) {
            $employeeExistsRule = Rule::exists('employees', 'id')
                ->where(fn ($query) => $query->where('department_id', $department));
        }

        $validated = $request->validate([
            'department' => $hasDepartments ? 'required|integer|exists:departments,id' : 'nullable',
            'employee_id' => ['required', $employeeExistsRule],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed',
            'deadline' => 'nullable|date',
        ]);

        unset($validated['department']);

        $scheduleDate = ($validated['deadline'] ?? null)
            ? \Carbon\Carbon::parse($validated['deadline'])->toDateString()
            : ($task->schedule_start_date ? $task->schedule_start_date->toDateString() : now()->toDateString());

        $validated['assignment_type'] = $task->assignment_type ?: 'date';
        $validated['schedule_start_date'] = $scheduleDate;
        $validated['schedule_end_date'] = $scheduleDate;
        $validated['deadline'] = $validated['deadline'] ?? $scheduleDate;

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
                'date' => $scheduleDate,
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
