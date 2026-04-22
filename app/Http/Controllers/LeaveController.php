<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\PmsNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee', 'user', 'reviewer')->latest();

        if (Auth::user()->role?->slug === 'staff') {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->paginate(10)->withQueryString();

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $employees = Employee::orderBy('name')->get();

        return view('leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id'],
            'leave_type' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string'],
        ]);

        LeaveRequest::create($data + [
            'user_id' => Auth::id(),
            'status' => 'Pending',
        ]);

        $admins = User::whereHas('role', function ($query) {
            $query->whereIn('slug', ['admin', 'supervisor']);
        })->get();

        Notification::send($admins, new PmsNotification(
            'New leave request',
            Auth::user()->name . ' submitted a leave request.',
            route('leaves.index')
        ));

        return redirect()->route('leaves.index')->with('success', 'Leave request submitted successfully.');
    }

    public function approve(LeaveRequest $leave)
    {
        return $this->reviewLeave($leave, 'Approved');
    }

    public function reject(LeaveRequest $leave)
    {
        return $this->reviewLeave($leave, 'Rejected');
    }

    private function reviewLeave(LeaveRequest $leave, string $status)
    {
        $leave->update([
            'status' => $status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($leave->user) {
            $leave->user->notify(new PmsNotification(
                'Leave request ' . strtolower($status),
                'Your leave request has been ' . strtolower($status) . '.',
                route('leaves.index')
            ));
        }

        return redirect()->route('leaves.index')->with('success', 'Leave request ' . strtolower($status) . '.');
    }
}
