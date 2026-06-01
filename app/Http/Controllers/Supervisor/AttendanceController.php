<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('name')->get();
        $todayAttendanceMarked = $this->attendanceAlreadyTakenForDate(today());
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
        return view('supervisor.attendance.index', compact('attendances', 'employees', 'employeeId', 'fromDate', 'toDate', 'status', 'summary', 'todayAttendanceMarked'));
    }

    public function create(Request $request)
    {
        $date = $request->filled('attendance_date') ? $request->date('attendance_date') : today();
        $employees = Employee::orderBy('name')->get();
        $todayRecords = Attendance::whereDate('attendance_date', $date)->get()->keyBy('employee_id');
        $attendanceAlreadyTaken = $this->attendanceAlreadyTakenForDate($date);

        $dateString = $date->toDateString();
        $approvedLeaves = LeaveRequest::query()
            ->where('status', 'Approved')
            ->whereNotNull('employee_id')
            ->whereDate('start_date', '<=', $dateString)
            ->whereDate('end_date', '>=', $dateString)
            ->get()
            ->keyBy('employee_id');

        $counts = [
            'present' => 0,
            'absent' => 0,
            'leave' => 0,
        ];

        foreach ($employees as $employee) {
            $employeeId = $employee->id;
            if ($approvedLeaves->has($employeeId)) {
                $counts['leave']++;
                continue;
            }

            $existing = $todayRecords->get($employeeId);
            if (! $existing) {
                continue;
            }

            if ($existing->status === 'Present') {
                $counts['present']++;
            } elseif ($existing->status === 'Absent') {
                $counts['absent']++;
            } elseif ($existing->status === 'Leave') {
                $counts['leave']++;
            }
        }

        return view('supervisor.attendance.create', compact('employees', 'date', 'todayRecords', 'counts', 'approvedLeaves', 'attendanceAlreadyTaken'));
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
                'statuses.*' => ['required', 'in:Present,Absent,Leave'],
                'remarks' => ['nullable', 'array'],
            ]);

            $attendanceDate = Carbon::parse($data['attendance_date']);
            if ($attendanceDate->isWeekend()) {
                return back()
                    ->withErrors(['attendance_date' => 'Attendance can only be marked Monday to Friday.'])
                    ->withInput();
            }

            if ($this->attendanceAlreadyTakenForDate($attendanceDate)) {
                return back()
                    ->withErrors(['attendance_date' => 'Attendance has already been marked for this date. You cannot mark it twice.'])
                    ->withInput();
            }

            $attendanceDateString = $attendanceDate->toDateString();
            $approvedLeaveEmployeeIds = LeaveRequest::query()
                ->where('status', 'Approved')
                ->whereNotNull('employee_id')
                ->whereDate('start_date', '<=', $attendanceDateString)
                ->whereDate('end_date', '>=', $attendanceDateString)
                ->pluck('employee_id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $approvedLeaveLookup = array_fill_keys($approvedLeaveEmployeeIds, true);

            foreach ($data['employee_ids'] as $employeeId) {
                if (! array_key_exists($employeeId, $data['statuses'])) {
                    return back()
                        ->withErrors(['statuses' => 'Please mark attendance for every employee before saving.'])
                        ->withInput();
                }

                $status = $data['statuses'][$employeeId];
                $status = is_string($status) ? trim($status) : $status;
                if (! in_array($status, ['Present', 'Absent', 'Leave'], true)) {
                    return back()
                        ->withErrors(['statuses' => 'Please mark attendance for every employee before saving.'])
                        ->withInput();
                }

                if (isset($approvedLeaveLookup[(int) $employeeId]) && $status !== 'Leave') {
                    return back()
                        ->withErrors(['statuses' => 'Some employees have an approved leave for this date and must be marked as On Leave.'])
                        ->withInput();
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

        if (Attendance::query()
            ->where('employee_id', $validated['employee_id'])
            ->whereDate('attendance_date', $attendanceDate->toDateString())
            ->exists()) {
            return back()
                ->withErrors(['attendance_date' => 'Attendance has already been marked for this employee on this date.'])
                ->withInput();
        }

        $attendanceDateString = $attendanceDate->toDateString();
        $hasApprovedLeave = LeaveRequest::query()
            ->where('status', 'Approved')
            ->where('employee_id', $validated['employee_id'])
            ->whereDate('start_date', '<=', $attendanceDateString)
            ->whereDate('end_date', '>=', $attendanceDateString)
            ->exists();

        if ($hasApprovedLeave && $validated['status'] !== 'Leave') {
            return back()
                ->withErrors(['status' => 'This employee has an approved leave for this date and must be marked as On Leave.'])
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

    private function attendanceAlreadyTakenForDate(Carbon|string $date): bool
    {
        $dateString = $date instanceof Carbon
            ? $date->toDateString()
            : Carbon::parse($date)->toDateString();

        $employeeCount = Employee::query()->count();
        if ($employeeCount === 0) {
            return false;
        }

        $markedEmployeeCount = Attendance::query()
            ->whereDate('attendance_date', $dateString)
            ->distinct('employee_id')
            ->count('employee_id');

        return $markedEmployeeCount >= $employeeCount;
    }
}
