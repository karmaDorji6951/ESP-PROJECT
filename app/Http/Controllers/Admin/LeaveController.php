<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
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

        $leave->update([
            'status' => $validated['status'],
            'remarks' => $validated['remarks'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.leaves.index')
            ->with('success', "Leave request {$validated['status']} successfully.");
    }
}
