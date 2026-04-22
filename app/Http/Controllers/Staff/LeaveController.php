<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
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
        return view('staff.leaves.create');
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

        $validated['employee_id'] = $user->employee_id;
        $validated['status'] = 'Pending';

        LeaveRequest::create($validated);
        return redirect()->route('staff.leaves.index')->with('success', 'Leave request submitted successfully.');
    }
}
