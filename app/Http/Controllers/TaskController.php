<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->string('month')->toString() ?: now()->format('Y-m');
        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $user = auth()->user();
        $canManage = $this->canManageAssignments();

        $employeesQuery = Employee::query()->orderBy('name');

        if (! $canManage) {
            $employeesQuery->whereKey($user->employee_id);
        } elseif ($request->filled('employee_id')) {
            $employeesQuery->whereKey((int) $request->input('employee_id'));
        }

        $employees = $employeesQuery->get();

        $tasksQuery = Task::with('employee', 'assigner')
            ->whereDate('schedule_start_date', '<=', $monthEnd->toDateString())
            ->whereDate('schedule_end_date', '>=', $monthStart->toDateString())
            ->orderBy('schedule_start_date')
            ->orderBy('employee_id');

        if (! $canManage) {
            $tasksQuery->where('employee_id', $user->employee_id ?: 0);
        } elseif ($request->filled('employee_id')) {
            $tasksQuery->where('employee_id', (int) $request->input('employee_id'));
        }

        $tasks = $tasksQuery->get();
        $days = collect(range(1, $monthStart->daysInMonth))
            ->map(fn (int $day) => $monthStart->copy()->day($day));

        return view('tasks.index', [
            'tasks' => $tasks,
            'days' => $days,
            'employees' => $employees,
            'month' => $month,
            'monthLabel' => $monthStart->format('F Y'),
            'canManage' => $canManage,
        ]);
    }

    public function create()
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to assign work.');

        $employees = Employee::orderBy('name')->get();

        return view('tasks.create', compact('employees'));
    }

    public function store(Request $request)
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to assign work.');

        $data = $this->validateTask($request) + [
            'assigned_by' => auth()->id(),
        ];

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Timetable work assigned successfully.');
    }

    public function edit(Task $task)
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to edit assignments.');

        $employees = Employee::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'employees'));
    }

    public function update(Request $request, Task $task)
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to edit assignments.');

        $data = $this->validateTask($request);

        if ($data['status'] === 'Completed' && $task->completed_at === null) {
            $data['completed_at'] = now();
        }

        if ($data['status'] !== 'Completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Timetable assignment updated successfully.');
    }

    public function destroy(Task $task)
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to delete assignments.');

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Timetable assignment deleted successfully.');
    }

    private function validateTask(Request $request): array
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assignment_type' => ['required', Rule::in(['date', 'week', 'month'])],
            'start_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'deadline' => ['nullable', 'date'],
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = match ($validated['assignment_type']) {
            'week' => $start->copy()->addDays(6),
            'month' => $start->copy()->endOfMonth(),
            default => $start->copy(),
        };

        return [
            'employee_id' => $validated['employee_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assignment_type' => $validated['assignment_type'],
            'schedule_start_date' => $start->toDateString(),
            'schedule_end_date' => $end->toDateString(),
            'status' => $validated['status'],
            'deadline' => $validated['deadline'] ?? $end->toDateString(),
        ];
    }

    private function canManageAssignments(): bool
    {
        $role = auth()->user()?->role;
        $slug = $role?->slug ?? str($role?->name)->lower()->toString();

        return in_array($slug, ['admin', 'supervisor'], true);
    }
}
