<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')->latest('attendance_date')->paginate(15);
        return view('supervisor.attendance.index', compact('attendances'));
    }

    public function create()
    {
        $employees = \App\Models\Employee::all();
        return view('supervisor.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Leave',
            'remarks' => 'nullable|string',
        ]);

        $validated['marked_by'] = auth()->id();
        Attendance::create($validated);

        return redirect()->route('supervisor.attendance.index')->with('success', 'Attendance marked successfully.');
    }
}
