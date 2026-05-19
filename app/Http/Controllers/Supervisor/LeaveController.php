<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $this->markLeaveRequestNotificationsAsRead();

        $leaves = LeaveRequest::with('employee', 'user')
            ->where('status', 'Pending')
            ->latest()
            ->paginate(15);
        
        return view('supervisor.leaves.index', compact('leaves'));
    }

    public function show(LeaveRequest $leaf)
    {
        $this->markLeaveRequestNotificationsAsRead($leaf->id);

        $leaf->load('employee', 'user');
        $employeeId = $leaf->employee_id ?? $leaf->user?->employee_id;
        $balance = null;
        if ($employeeId) {
            $balance = \App\Models\LeaveRequest::getLeaveUsage($employeeId, $leaf->start_date?->year ?? now()->year);
        }

        return view('supervisor.leaves.show', compact('leaf', 'balance'));
    }

    public function update(Request $request, LeaveRequest $leaf)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'remarks' => 'nullable|string',
        ]);

        $leaf->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify the staff member about the decision
        $this->notifyStaff($leaf, Auth::user(), $validated['status']);

        return redirect()->route('supervisor.leaves.index')
            ->with('success', "Leave request {$validated['status']} successfully.");
    }

    private function notifyStaff(LeaveRequest $leaf, User $supervisor, string $status)
    {
        try {
            if ($leaf->user) {
                $notificationData = [
                    'leave_request_id' => $leaf->id,
                    'leave_type' => $leaf->leave_type,
                    'status' => $status,
                    'message' => "Your leave request has been {$status}",
                    'reviewed_by' => $supervisor->name,
                    'type' => 'leave_decision',
                ];

                $leaf->user->notify(new \App\Notifications\LeaveDecisionNotification($notificationData));
            }
        } catch (\Exception $e) {
            \Log::error('Staff notification error: ' . $e->getMessage());
            // Don't fail the process if notification fails
        }
    }

    private function markLeaveRequestNotificationsAsRead(?int $leaveRequestId = null): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $query = $user->unreadNotifications();

        // Only mark notifications about leave requests.
        $query->where('data->type', 'leave_request');

        if ($leaveRequestId !== null) {
            $query->where('data->leave_request_id', $leaveRequestId);
        }

        $query->get()->each->markAsRead();
    }
}
