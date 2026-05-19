<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $leaves = LeaveRequest::where('employee_id', $user->employee_id)->paginate(15);
        return view('staff.leaves.index', compact('leaves'));
    }

    public function create()
    {
        $user = Auth::user();
        $balance = LeaveRequest::getLeaveUsage($user->employee_id, Carbon::now()->year);
        return view('staff.leaves.create', compact('balance'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'leave_type' => 'required|string|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'nullable|string',
        ]);

        // Calculate requested days (inclusive)
        $from = Carbon::parse($validated['from_date'])->startOfDay();
        $to = Carbon::parse($validated['to_date'])->endOfDay();
        $requestedDays = $from->diffInDays($to) + 1;

        $balance = LeaveRequest::getLeaveUsage($user->employee_id, $from->year);

        if ($requestedDays > $balance['remaining']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['to_date' => "Requested leave ({$requestedDays} days) exceeds remaining allowance of {$balance['remaining']} days for the year."]);
        }

        $leaveRequest = LeaveRequest::firstOrCreate(
            [
                'employee_id' => $user->employee_id,
                'leave_type' => $validated['leave_type'],
                'start_date' => $validated['from_date'],
                'end_date' => $validated['to_date'],
                'reason' => $validated['reason'] ?? null,
                'status' => 'Pending',
            ],
            [
                'user_id' => $user->id,
            ]
        );

        if (! $leaveRequest->wasRecentlyCreated) {
            return redirect()->route('staff.leaves.index')
                ->with('warning', 'This leave request was already submitted.');
        }

        // Notify supervisors about the new leave request
        $this->notifySupervisors($leaveRequest, $user);
        
        return redirect()->route('staff.leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    private function notifySupervisors(LeaveRequest $leaveRequest, User $staff)
    {
        try {
            // Get supervisor users
            $supervisors = User::whereHas('role', function($query) {
                $query->where('slug', 'supervisor');
            })->get();

            // Create notification data
            $notificationData = [
                'leave_request_id' => $leaveRequest->id,
                'leave_type' => $leaveRequest->leave_type,
                'start_date' => $leaveRequest->start_date->format('Y-m-d'),
                'end_date' => $leaveRequest->end_date->format('Y-m-d'),
                'requested_by' => $staff->name,
                'message' => "New leave request: {$leaveRequest->leave_type} from {$staff->name}",
                'type' => 'leave_request',
            ];

            // Notify all supervisors
            foreach ($supervisors as $supervisor) {
                $supervisor->notify(new \App\Notifications\LeaveRequestNotification($notificationData));
            }

        } catch (\Exception $e) {
            \Log::error('Supervisor notification error: ' . $e->getMessage());
            // Don't fail the submission if notification fails
        }
    }
}
