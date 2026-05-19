<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Timetable;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimetableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $view = $request->get('view', 'week'); // week, month, day
        $date = $request->get('date', now()->toDateString());

        $query = Timetable::query()->with(['employee', 'assignedBy']);

        // Apply role-based filtering
        $query->forUser($user);

        // Filter by date range based on view
        $startDate = match($view) {
            'day' => Carbon::parse($date),
            'week' => Carbon::parse($date)->startOfWeek(),
            'month' => Carbon::parse($date)->startOfMonth(),
            default => Carbon::parse($date)->startOfWeek(),
        };

        $endDate = match($view) {
            'day' => Carbon::parse($date),
            // Show 2 rows of days in week view (14-day window).
            // We keep navigation stepping by 7 days so weeks overlap naturally.
            'week' => Carbon::parse($date)->startOfWeek()->addDays(13),
            'month' => Carbon::parse($date)->endOfMonth(),
            default => Carbon::parse($date)->endOfWeek(),
        };

        $timetables = $query->forDateRange($startDate, $endDate)
                           ->orderBy('date')
                           ->orderBy('start_time')
                           ->get();

        $canCreate = in_array($user->role->slug, ['admin', 'supervisor']);
        $employees = Employee::all();

        return view('timetables.index', compact('timetables', 'view', 'date', 'canCreate', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeCreate();

        $employees = Employee::all();
        return view('timetables.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeCreate();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'employee_id' => 'nullable|exists:employees,id',
            'assigned_to_role' => 'nullable|in:admin,supervisor,staff',
        ]);

        $validated['assigned_by'] = Auth::id();

        $targetEmployeeId = $this->resolveTargetEmployeeId($validated);
        if ($targetEmployeeId) {
            $validated['employee_id'] = $targetEmployeeId;
        }

        $timetable = Timetable::create($validated);

        if ($targetEmployeeId) {
            $this->syncTaskWithTimetable($timetable, $validated, $targetEmployeeId);
        }

        return redirect()->route('timetables.index', [
            'date' => $validated['date'],
            'view' => 'day'
        ])->with('success', 'Timetable entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Timetable $timetable)
    {
        $user = Auth::user();
        
        // Check if user can view this timetable entry
        if (!$this->canView($timetable, $user)) {
            abort(403);
        }

        $timetable->load(['employee', 'assignedBy']);
        return view('timetables.show', compact('timetable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Timetable $timetable)
    {
        $this->authorizeEdit($timetable);

        $employees = Employee::all();
        return view('timetables.edit', compact('timetable', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Timetable $timetable)
    {
        $this->authorizeEdit($timetable);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'employee_id' => 'nullable|exists:employees,id',
            'assigned_to_role' => 'nullable|in:admin,supervisor,staff',
        ]);

        $targetEmployeeId = $this->resolveTargetEmployeeId($validated);
        if ($targetEmployeeId) {
            $validated['employee_id'] = $targetEmployeeId;
        }

        $timetable->update($validated);

        if ($targetEmployeeId) {
            $this->syncTaskWithTimetable($timetable, $validated, $targetEmployeeId);
        }

        return redirect()->route('timetables.index')
            ->with('success', 'Timetable entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timetable $timetable)
    {
        $this->authorizeEdit($timetable);

        // Delete associated task if it exists
        if ($timetable->task) {
            $timetable->task->delete();
        }

        $timetable->delete();

        return redirect()->route('timetables.index')
            ->with('success', 'Timetable entry deleted successfully.');
    }

    /**
     * Get day details for AJAX request.
     */
    public function dayDetails(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date');

        if (!$date) {
            return response('Invalid date', 400);
        }

        $query = Timetable::query()->with(['employee', 'assignedBy']);
        $query->forUser($user);

        $dayTimetables = $query->whereDate('date', $date)
                             ->orderBy('start_time')
                             ->get();

        $canCreate = in_array($user->role->slug, ['admin', 'supervisor']);

        return view('timetables.partials.day-details', compact('dayTimetables', 'date', 'canCreate'))->render();
    }

    private function authorizeCreate()
    {
        $user = Auth::user();
        if (!in_array($user->role->slug, ['admin', 'supervisor'])) {
            abort(403, 'Only admin and supervisor can create timetable entries.');
        }
    }

    private function authorizeEdit(Timetable $timetable)
    {
        $user = Auth::user();
        if (!in_array($user->role->slug, ['admin', 'supervisor'])) {
            abort(403, 'Only admin and supervisor can edit timetable entries.');
        }
    }

    private function canView(Timetable $timetable, $user)
    {
        if (in_array($user->role->slug, ['admin', 'supervisor'])) {
            return true;
        }

        if ($user->role->slug === 'staff') {
            if ($user->employee && $timetable->employee_id === $user->employee->id) {
                return true;
            }
            if ($timetable->assigned_to_role === $user->role->slug) {
                return true;
            }
        }

        return false;
    }

    private function resolveTargetEmployeeId(array $validated): ?int
    {
        if (!empty($validated['employee_id'])) {
            return (int) $validated['employee_id'];
        }

        if (($validated['assigned_to_role'] ?? null) === 'staff') {
            return $this->sampleStaffEmployeeId();
        }

        return null;
    }

    private function sampleStaffEmployeeId(): ?int
    {
        return User::whereHas('role', function ($query) {
            $query->where('slug', 'staff');
        })
        ->whereNotNull('employee_id')
        ->value('employee_id');
    }

    private function syncTaskWithTimetable(Timetable $timetable, array $validated, int $employeeId): void
    {
        $taskStatus = match ($validated['status'] ?? 'scheduled') {
            'completed' => 'Completed',
            'in_progress' => 'In Progress',
            default => 'Pending',
        };

        $taskData = [
            'employee_id' => $employeeId,
            'assigned_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'schedule_start_date' => $validated['date'],
            'schedule_end_date' => $validated['date'],
            'status' => $taskStatus,
        ];

        if ($timetable->task) {
            $timetable->task->update($taskData);
            return;
        }

        $task = Task::create($taskData);
        $timetable->update(['task_id' => $task->id]);
    }
}
