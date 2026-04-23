<?php

namespace App\Http\Controllers;

use App\Events\TaskAssigned;
use App\Events\TaskSubmitted;
use App\Models\Employee;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Timetable;
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

        $task = Task::create($data);

        // Create timetable entry for the task
        Timetable::create([
            'title' => $task->title,
            'description' => $task->description,
            'date' => $task->schedule_start_date,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'status' => 'scheduled',
            'priority' => 'medium',
            'employee_id' => $task->employee_id,
            'assigned_by' => auth()->id(),
            'task_id' => $task->id,
        ]);

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

        // Update timetable entry if it exists
        if ($task->timetable) {
            $task->timetable->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'date' => $data['schedule_start_date'],
                'status' => strtolower($data['status']) === 'in progress' ? 'in_progress' : (strtolower($data['status']) === 'completed' ? 'completed' : 'scheduled'),
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Timetable assignment updated successfully.');
    }

    public function destroy(Task $task)
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to delete assignments.');

        // Delete associated timetable entry
        if ($task->timetable) {
            $task->timetable->delete();
        }

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

    /**
     * Submit a task for review
     */
    public function submitTask(Request $request, Task $task)
    {
        // Verify the authenticated user is the one assigned the task
        if ($task->employee_id !== auth()->user()?->employee_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'submission_notes' => ['required', 'string', 'min:10'],
        ]);

        // Create task submission
        $submission = TaskSubmission::create([
            'task_id' => $task->id,
            'submitted_by' => auth()->id(),
            'submission_notes' => $validated['submission_notes'],
            'submission_status' => 'Submitted',
            'submitted_at' => now(),
        ]);

        // Load relationships for event broadcasting
        $submission->load('task.assigner', 'submitter');

        // Dispatch event to notify supervisor
        TaskSubmitted::dispatch($submission);

        return response()->json([
            'success' => true,
            'message' => 'Work submitted for evaluation',
            'submission' => $submission,
        ]);
    }

    /**
     * Check submission status
     */
    public function checkSubmissionStatus(Task $task)
    {
        $submission = $task->latestSubmission()->first();

        return response()->json([
            'submitted' => $submission ? true : false,
            'submission' => $submission,
        ]);
    }

    /**
     * Assign task and trigger real-time notification
     */
    public function assignTaskWithNotification(Request $request, Task $task)
    {
        abort_unless($this->canManageAssignments(), 403, 'You are not authorized to assign work.');

        // Update task if needed
        if ($request->filled('status')) {
            $task->update(['status' => $request->input('status')]);
        }

        // Load relationships for event broadcasting
        $task->load('employee:id,name', 'assigner:id,name');

        // Dispatch event to notify employee
        TaskAssigned::dispatch($task);

        return response()->json([
            'success' => true,
            'message' => 'Task assigned and notification sent',
            'task' => $task,
        ]);
    }
}
