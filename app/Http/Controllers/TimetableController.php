<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Timetable;
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
            'week' => Carbon::parse($date)->endOfWeek(),
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

        Timetable::create($validated);

        return redirect()->route('timetables.index')
            ->with('success', 'Timetable entry created successfully.');
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

        $timetable->update($validated);

        return redirect()->route('timetables.index')
            ->with('success', 'Timetable entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Timetable $timetable)
    {
        $this->authorizeEdit($timetable);

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
}
