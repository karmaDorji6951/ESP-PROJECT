<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('name')->get();
        $employeeId = $request->integer('employee_id');
        $fromDate = $request->filled('from_date') ? $request->date('from_date') : null;
        $toDate = $request->filled('to_date') ? $request->date('to_date') : null;
        $status = $request->string('status')->toString();
        $status = in_array($status, ['Present', 'Absent', 'Leave'], true) ? $status : null;

        if (! $fromDate && ! $toDate) {
            $fromDate = now()->subMonth()->startOfDay();
            $toDate = now()->endOfDay();
        }

        $baseQuery = Attendance::query();

        if ($employeeId) {
            $baseQuery->where('employee_id', $employeeId);
        }

        if ($fromDate && $toDate) {
            $baseQuery->whereBetween('attendance_date', [$fromDate->toDateString(), $toDate->toDateString()]);
        } elseif ($fromDate) {
            $baseQuery->whereDate('attendance_date', '>=', $fromDate);
        } elseif ($toDate) {
            $baseQuery->whereDate('attendance_date', '<=', $toDate);
        }

        $summary = [
            'present' => (clone $baseQuery)->where('status', 'Present')->count(),
            'absent' => (clone $baseQuery)->where('status', 'Absent')->count(),
            'leave' => (clone $baseQuery)->where('status', 'Leave')->count(),
        ];

        $query = (clone $baseQuery)->with('employee')->latest('attendance_date');

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->paginate(15)->withQueryString();
        return view('supervisor.attendance.index', compact('attendances', 'employees', 'employeeId', 'fromDate', 'toDate', 'status', 'summary'));
    }

    public function create(Request $request)
    {
        $date = $request->filled('attendance_date') ? $request->date('attendance_date') : today();
        $employees = Employee::orderBy('name')->get();
        $todayRecords = Attendance::whereDate('attendance_date', $date)->get()->keyBy('employee_id');

        $counts = [
            'present' => $todayRecords->where('status', 'Present')->count(),
            'absent' => $todayRecords->where('status', 'Absent')->count(),
            'leave' => $todayRecords->where('status', 'Leave')->count(),
        ];

        return view('supervisor.attendance.create', compact('employees', 'date', 'todayRecords', 'counts'));
    }

    public function store(Request $request)
    {
        // Bulk marking from the attendance table UI
        if ($request->has('employee_ids')) {
            $data = $request->validate([
                'attendance_date' => ['required', 'date'],
                'employee_ids' => ['required', 'array'],
                'employee_ids.*' => ['exists:employees,id'],
                'statuses' => ['required', 'array'],
                'remarks' => ['nullable', 'array'],
            ]);

            $attendanceDate = Carbon::parse($data['attendance_date']);
            if ($attendanceDate->isWeekend()) {
                return back()
                    ->withErrors(['attendance_date' => 'Attendance can only be marked Monday to Friday.'])
                    ->withInput();
            }

            foreach ($data['employee_ids'] as $employeeId) {
                $status = $data['statuses'][$employeeId] ?? 'Absent';
                if (! in_array($status, ['Present', 'Absent', 'Leave'], true)) {
                    $status = 'Absent';
                }

                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'attendance_date' => $data['attendance_date'],
                    ],
                    [
                        'status' => $status,
                        'remarks' => $data['remarks'][$employeeId] ?? null,
                        'marked_by' => auth()->id(),
                    ]
                );
            }

            return redirect()->route('supervisor.attendance.index')->with('success', 'Attendance saved successfully.');
        }

        // Fallback: single employee marking
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Leave',
            'remarks' => 'nullable|string',
        ]);

        $attendanceDate = Carbon::parse($validated['attendance_date']);
        if ($attendanceDate->isWeekend()) {
            return back()
                ->withErrors(['attendance_date' => 'Attendance can only be marked Monday to Friday.'])
                ->withInput();
        }

        Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'attendance_date' => $validated['attendance_date'],
            ],
            [
                'status' => $validated['status'],
                'remarks' => $validated['remarks'] ?? null,
                'marked_by' => auth()->id(),
            ]
        );

        return redirect()->route('supervisor.attendance.index')->with('success', 'Attendance marked successfully.');
    }
}
