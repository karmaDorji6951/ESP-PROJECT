<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Get current user and role
        $user = auth()->user();
        
        // Time periods for analytics
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonth()->month;
        $lastMonthYear = Carbon::now()->subMonth()->year;
        
        // Employee Analytics
        $employeeAnalytics = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'Active')->count(),
            'new_employees_this_month' => Employee::whereMonth('created_at', $thisMonth)
                ->whereYear('created_at', $thisYear)->count(),
            'employee_growth_rate' => $this->calculateEmployeeGrowthRate(),
        ];
        
        // Attendance Analytics
        $attendanceAnalytics = $this->getAttendanceAnalytics($thisMonth, $thisYear);
        
        // Task Analytics
        $taskAnalytics = $this->getTaskAnalytics();
        
        // Leave Analytics
        $leaveAnalytics = $this->getLeaveAnalytics($thisMonth, $thisYear);
        
        // Department Analytics
        $departmentAnalytics = $this->getDepartmentAnalytics();
        
        // Performance Metrics
        $performanceMetrics = $this->getPerformanceMetrics();
        
        // Monthly Trends (last 6 months)
        $monthlyTrends = $this->getMonthlyTrends();
        
        return view('admin.analytics.index', compact(
            'user',
            'employeeAnalytics',
            'attendanceAnalytics',
            'taskAnalytics',
            'leaveAnalytics',
            'departmentAnalytics',
            'performanceMetrics',
            'monthlyTrends'
        ));
    }
    
    private function calculateEmployeeGrowthRate()
    {
        $currentMonthEmployees = Employee::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->count();
            
        $lastMonthEmployees = Employee::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)->count();
            
        if ($lastMonthEmployees == 0) {
            return $currentMonthEmployees > 0 ? 100 : 0;
        }
        
        return round((($currentMonthEmployees - $lastMonthEmployees) / $lastMonthEmployees) * 100, 2);
    }
    
    private function getAttendanceAnalytics($month, $year)
    {
        $totalDays = Carbon::now()->daysInMonth;
        $workingDays = $this->getWorkingDays($month, $year);
        
        $presentDays = Attendance::whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'Present')
            ->count();
            
        $absentDays = Attendance::whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'Absent')
            ->count();
            
        $lateDays = Attendance::whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'Late')
            ->count();

        $leaveDays = Attendance::whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'Leave')
            ->count();
            
        $totalAttendanceRecords = $presentDays + $lateDays + $absentDays + $leaveDays;
        
        return [
            'attendance_rate' => $totalAttendanceRecords > 0 ? round((($presentDays + $lateDays) / $totalAttendanceRecords) * 100, 2) : 0,
            'punctuality_rate' => ($presentDays + $lateDays) > 0 ? round(($presentDays / ($presentDays + $lateDays)) * 100, 2) : 0,
            'total_present' => $presentDays,
            'total_absent' => $absentDays,
            'total_late' => $lateDays,
            'total_leave' => $leaveDays,
            'working_days' => $workingDays,
        ];
    }
    
    private function getWorkingDays($month, $year)
    {
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $workingDays = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            if ($date->isWeekday()) {
                $workingDays++;
            }
        }
        
        return $workingDays;
    }
    
    private function getTaskAnalytics()
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'Completed')->count();
        $inProgressTasks = Task::where('status', 'In Progress')->count();
        $pendingTasks = Task::where('status', 'Pending')->count();
        $overdueTasks = Task::where('status', '!=', 'Completed')
            ->where('deadline', '<', Carbon::today())
            ->count();
            
        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'pending_tasks' => $pendingTasks,
            'overdue_tasks' => $overdueTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0,
        ];
    }
    
    private function getLeaveAnalytics($month, $year)
    {
        $totalLeaves = LeaveRequest::whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->count();
            
        $approvedLeaves = LeaveRequest::whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->where('status', 'Approved')
            ->count();
            
        $pendingLeaves = LeaveRequest::whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->where('status', 'Pending')
            ->count();
            
        $rejectedLeaves = LeaveRequest::whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->where('status', 'Rejected')
            ->count();
            
        return [
            'total_leaves' => $totalLeaves,
            'approved_leaves' => $approvedLeaves,
            'pending_leaves' => $pendingLeaves,
            'rejected_leaves' => $rejectedLeaves,
            'approval_rate' => $totalLeaves > 0 ? round(($approvedLeaves / $totalLeaves) * 100, 2) : 0,
        ];
    }
    
    private function getDepartmentAnalytics()
    {
        return \App\Models\Department::withCount('employees')
            ->orderByDesc('employees_count')
            ->get()
            ->map(function ($dept) {
                return [
                    'department' => $dept->name,
                    'employee_count' => $dept->employees_count,
                ];
            });
    }
    
    private function getPerformanceMetrics()
    {
        // Average task completion time
        $avgCompletionTime = Task::where('status', 'Completed')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(DATEDIFF(completed_at, created_at)) as avg_days')
            ->value('avg_days');
            
        // Employee productivity score (based on task completion and attendance)
        $productivityScore = $this->calculateProductivityScore();
        
        // Top performers
        $topPerformers = $this->getTopPerformers();
        
        return [
            'avg_task_completion_time' => round($avgCompletionTime, 1),
            'productivity_score' => $productivityScore,
            'top_performers' => $topPerformers,
        ];
    }
    
    private function calculateProductivityScore()
    {
        $totalEmployees = Employee::count();
        if ($totalEmployees == 0) return 0;
        
        $totalScore = 0;
        
        foreach (Employee::with('tasks')->get() as $employee) {
            $employeeScore = 0;
            
            // Task completion score (40% weight)
            $completedTasks = $employee->tasks()->where('status', 'Completed')->count();
            $totalTasks = $employee->tasks()->count();
            if ($totalTasks > 0) {
                $employeeScore += ($completedTasks / $totalTasks) * 40;
            }
            
            // Attendance score (60% weight)
            $presentDays = Attendance::where('employee_id', $employee->id)
                ->where('status', 'Present')
                ->count();
            $totalAttendanceRecords = Attendance::where('employee_id', $employee->id)->count();
            if ($totalAttendanceRecords > 0) {
                $employeeScore += ($presentDays / $totalAttendanceRecords) * 60;
            }
            
            $totalScore += $employeeScore;
        }
        
        return round($totalScore / $totalEmployees, 2);
    }
    
    private function getTopPerformers()
    {
        return Employee::with(['tasks', 'user'])
            ->get()
            ->map(function ($employee) {
                $completedTasks = $employee->tasks()->where('status', 'Completed')->count();
                $totalTasks = $employee->tasks()->count();
                $taskCompletionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                
                $presentDays = Attendance::where('employee_id', $employee->id)
                    ->where('status', 'Present')
                    ->count();
                $totalAttendanceRecords = Attendance::where('employee_id', $employee->id)->count();
                $attendanceRate = $totalAttendanceRecords > 0 ? ($presentDays / $totalAttendanceRecords) * 100 : 0;
                
                $performanceScore = ($taskCompletionRate * 0.4) + ($attendanceRate * 0.6);
                
                return [
                    'name' => $employee->user?->name ?? $employee->name,
                    'department' => $employee->department,
                    'performance_score' => round($performanceScore, 2),
                    'task_completion_rate' => round($taskCompletionRate, 2),
                    'attendance_rate' => round($attendanceRate, 2),
                ];
            })
            ->sortByDesc('performance_score')
            ->take(5)
            ->values();
    }
    
    private function getMonthlyTrends()
    {
        $trends = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            $monthName = $date->format('M Y');
            
            $trends[] = [
                'month' => $monthName,
                'new_employees' => Employee::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)->count(),
                'attendance_rate' => $this->getMonthlyAttendanceRate($month, $year),
                'task_completion_rate' => $this->getMonthlyTaskCompletionRate($month, $year),
                'leaves_taken' => LeaveRequest::whereMonth('start_date', $month)
                    ->whereYear('start_date', $year)
                    ->where('status', 'Approved')->count(),
            ];
        }
        
        return collect($trends);
    }
    
    private function getMonthlyAttendanceRate($month, $year)
    {
        $present = Attendance::whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->where('status', 'Present')->count();
        $total = Attendance::whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)->count();
            
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }
    
    private function getMonthlyTaskCompletionRate($month, $year)
    {
        $completed = Task::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('status', 'Completed')->count();
        $total = Task::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)->count();
            
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
    
    public function exportReport(Request $request)
    {
        $type = $request->input('type', 'summary');
        $format = $request->input('format', 'pdf');
        
        // Implementation for exporting reports
        // This would generate PDF/Excel reports based on the analytics data
        
        return response()->json([
            'success' => true,
            'message' => 'Report exported successfully',
            'download_url' => '#'
        ]);
    }
}
