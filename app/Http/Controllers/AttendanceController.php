<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('name')->get();
        $date = $request->filled('attendance_date') ? $request->date('attendance_date') : today();
        $employeeId = $request->integer('employee_id');

        $query = Attendance::with('employee', 'marker')->orderByDesc('attendance_date');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        if ($request->filled('attendance_date')) {
            $query->whereDate('attendance_date', $date);
        }

        $records = $query->paginate(15)->withQueryString();
        $todayRecords = Attendance::whereDate('attendance_date', $date)->get()->keyBy('employee_id');

        return view('attendance.index', compact('employees', 'records', 'date', 'todayRecords', 'employeeId'));
    }

    public function store(Request $request)
    {
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
            Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $data['attendance_date'],
                ],
                [
                    'status' => $data['statuses'][$employeeId] ?? 'Absent',
                    'remarks' => $data['remarks'][$employeeId] ?? null,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return redirect()->route('attendance.index', ['attendance_date' => $data['attendance_date']])->with('success', 'Attendance saved successfully.');
    }
}
