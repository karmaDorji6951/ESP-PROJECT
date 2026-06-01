<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = LeaveRequest::with('employee', 'user')
            ->latest()
            ->paginate(15);
        
        return view('admin.leaves.index', compact('leaves'));
    }

    public function show(LeaveRequest $leave)
    {
        $leave->load('employee', 'user');
        return view('admin.leaves.show', compact('leave'));
    }

    public function update(Request $request, LeaveRequest $leave)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'remarks' => 'nullable|string',
        ]);

        $wasApproved = $leave->status === 'Approved';

        $leave->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($validated['status'] === 'Approved') {
            $this->syncAttendanceForApprovedLeave($leave);
        } elseif ($wasApproved) {
            $this->removeAttendanceForLeave($leave);
        }

        return redirect()->route('admin.leaves.index')
            ->with('success', "Leave request {$validated['status']} successfully.");
    }

    private function syncAttendanceForApprovedLeave(LeaveRequest $leave): void
    {
        $employeeId = $leave->employee_id ?? $leave->user?->employee_id;
        if (! $employeeId || ! $leave->start_date || ! $leave->end_date) {
            return;
        }

        $start = Carbon::parse($leave->start_date)->startOfDay();
        $end = Carbon::parse($leave->end_date)->startOfDay();
        if ($start->gt($end)) {
            return;
        }

        $markerId = Auth::id();
        $remarksPrefix = 'Auto: Approved leave';
        $remarks = $leave->leave_type ? "{$remarksPrefix} ({$leave->leave_type})" : $remarksPrefix;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'attendance_date' => $date->toDateString(),
                ],
                [
                    'status' => 'Leave',
                    'remarks' => $remarks,
                    'marked_by' => $markerId,
                ]
            );
        }
    }

    private function removeAttendanceForLeave(LeaveRequest $leave): void
    {
        $employeeId = $leave->employee_id ?? $leave->user?->employee_id;
        if (! $employeeId || ! $leave->start_date || ! $leave->end_date) {
            return;
        }

        $start = Carbon::parse($leave->start_date)->startOfDay();
        $end = Carbon::parse($leave->end_date)->startOfDay();
        if ($start->gt($end)) {
            return;
        }

        $remarksPrefix = 'Auto: Approved leave';

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            Attendance::query()
                ->where('employee_id', $employeeId)
                ->whereDate('attendance_date', $date->toDateString())
                ->where('status', 'Leave')
                ->where('remarks', 'like', $remarksPrefix . '%')
                ->delete();
        }
    }
}
