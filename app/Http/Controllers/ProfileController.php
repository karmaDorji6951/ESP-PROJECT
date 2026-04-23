<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['role', 'employee']);
        
        // Get user statistics based on role
        $statistics = $this->getUserStatistics($user);
        
        return view('profile.show', compact('user', 'statistics'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load(['role', 'employee']);
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo_path && \Storage::exists($user->photo_path)) {
                \Storage::delete($user->photo_path);
            }

            // Store new photo
            $photoPath = $request->file('photo')->store('profiles', 'public');
            $validated['photo_path'] = $photoPath;
        }

        $user->update($validated);

        // Update employee information if exists
        if ($user->employee) {
            $employeeData = $request->validate([
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
            ]);
            
            $user->employee->update($employeeData);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Get user statistics based on their role.
     */
    private function getUserStatistics($user)
    {
        $statistics = [];

        switch ($user->role->slug) {
            case 'admin':
                $statistics = [
                    'total_users' => \App\Models\User::count(),
                    'total_employees' => \App\Models\Employee::count(),
                    'total_tasks' => \App\Models\Task::count(),
                    'pending_leaves' => \App\Models\LeaveRequest::where('status', 'pending')->count(),
                ];
                break;

            case 'supervisor':
                $statistics = [
                    'assigned_tasks' => \App\Models\Task::where('assigned_by', $user->id)->count(),
                    'pending_tasks' => \App\Models\Task::where('assigned_by', $user->id)->where('status', 'pending')->count(),
                    'completed_tasks' => \App\Models\Task::where('assigned_by', $user->id)->where('status', 'completed')->count(),
                    'team_size' => \App\Models\Employee::count(),
                    'pending_leaves' => \App\Models\LeaveRequest::where('status', 'pending')->count(),
                ];
                break;

            case 'staff':
                $statistics = [
                    'my_tasks' => $user->employee ? \App\Models\Task::where('employee_id', $user->employee->id)->count() : 0,
                    'completed_tasks' => $user->employee ? \App\Models\Task::where('employee_id', $user->employee->id)->where('status', 'completed')->count() : 0,
                    'pending_tasks' => $user->employee ? \App\Models\Task::where('employee_id', $user->employee->id)->where('status', 'pending')->count() : 0,
                    'my_leaves' => \App\Models\LeaveRequest::where('user_id', $user->id)->count(),
                    'attendance_rate' => $this->calculateAttendanceRate($user),
                ];
                break;
        }

        return $statistics;
    }

    /**
     * Calculate attendance rate for staff users.
     */
    private function calculateAttendanceRate($user)
    {
        if (!$user->employee) {
            return 0;
        }

        $totalDays = \App\Models\Attendance::where('employee_id', $user->employee->id)->count();
        $presentDays = \App\Models\Attendance::where('employee_id', $user->employee->id)
                                       ->where('status', 'Present')
                                       ->count();

        return $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
    }
}
