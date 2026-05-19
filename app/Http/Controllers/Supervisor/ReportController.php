<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\Timetable;
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $teamEmployees = $this->getSupervisorTeam();
        $recentReports = \App\Models\Report::with('user')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('supervisor.reports.index', compact('user', 'teamEmployees', 'recentReports'));
    }

    public function generate(Request $request)
    {
        try {
            $request->validate([
                'report_type' => 'required|string|in:team_attendance,team_tasks,team_leaves,team_performance',
                'date_range' => 'required|string|in:this_month,last_month,last_3_months,custom',
                'start_date' => 'required_if:date_range,custom|date',
                'end_date' => 'required_if:date_range,custom|date|after_or_equal:start_date',
                'format' => 'required|string|in:pdf,excel',
                'employee_id' => 'nullable|integer|exists:employees,id'
            ]);

            $reportType = $request->input('report_type');
            $dateRange = $request->input('date_range');
            $format = $request->input('format');
            $employeeId = $request->input('employee_id');

            // Get supervisor's team
            $teamEmployees = $this->getSupervisorTeam();

            // Get date range
            $dates = $this->getDateRange($dateRange, $request);

            // Generate report data based on type
            $data = $this->generateTeamReportData($reportType, $dates, $teamEmployees, $employeeId);

            // Save report record to database
            $reportId = 'RPT-' . strtoupper(uniqid());
            $report = \App\Models\Report::create([
                'user_id' => auth()->id(),
                'report_type' => $reportType,
                'date_range' => $dateRange,
                'start_date' => $dateRange === 'custom' ? $dates['start'] : null,
                'end_date' => $dateRange === 'custom' ? $dates['end'] : null,
                'period_label' => $dates['label'],
                'format' => $format,
                'report_id' => $reportId,
                'summary_data' => $data['summary'] ?? [],
            ]);

            // Add report ID to data for display
            $data['summary']['report_id'] = $reportId;

            // Generate and return download response
            return $this->generateReportFile($data, $reportType, $format, $dates);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Report generation error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 500);
        }
    }

    private function getSupervisorTeam()
    {
        // Requirement: supervisors can generate reports for any staff member.
        // So we list all active employees that are linked to a staff user.
        return Employee::query()
            ->with(['user.role'])
            ->where('status', 'Active')
            ->whereHas('user.role', function ($query) {
                $query->where('slug', 'staff');
            })
            ->orderBy('name')
            ->get();
    }

    private function getDateRange($range, $request)
    {
        switch ($range) {
            case 'this_month':
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'label' => Carbon::now()->format('F Y')
                ];
            case 'last_month':
                return [
                    'start' => Carbon::now()->subMonth()->startOfMonth(),
                    'end' => Carbon::now()->subMonth()->endOfMonth(),
                    'label' => Carbon::now()->subMonth()->format('F Y')
                ];
            case 'last_3_months':
                return [
                    'start' => Carbon::now()->subMonths(2)->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'label' => 'Last 3 Months'
                ];
            case 'custom':
                $start = Carbon::parse($request->input('start_date'));
                $end = Carbon::parse($request->input('end_date'));
                $label = $start->isSameDay($end)
                    ? $start->format('F j, Y')
                    : $start->format('F j, Y') . ' - ' . $end->format('F j, Y');

                return [
                    'start' => $start,
                    'end' => $end,
                    'label' => $label,
                ];
            default:
                return [
                    'start' => Carbon::now()->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'label' => Carbon::now()->format('F Y')
                ];
        }
    }

    private function generateTeamReportData($type, $dates, $teamEmployees, $employeeId = null)
    {
        switch ($type) {
            case 'team_attendance':
                return $this->getTeamAttendanceReport($dates, $teamEmployees, $employeeId);
            case 'team_tasks':
                return $this->getTeamTasksReport($dates, $teamEmployees, $employeeId);
            case 'team_leaves':
                return $this->getTeamLeavesReport($dates, $teamEmployees, $employeeId);
            case 'team_performance':
                return $this->getTeamPerformanceReport($dates, $teamEmployees, $employeeId);
            default:
                return [];
        }
    }

    private function getTeamAttendanceReport($dates, $teamEmployees, $employeeId = null)
    {
        $employeeIds = $teamEmployees->pluck('id');
        
        $query = Attendance::with(['employee.user'])
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('attendance_date', [$dates['start'], $dates['end']]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get();

        $presentCount = $attendances->where('status', 'Present')->count();
        $lateCount = $attendances->where('status', 'Late')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $leaveCount = $attendances->where('status', 'Leave')->count();
        $totalCount = $attendances->count();

        // Calculate working days in the period
        $workingDays = 0;
        $period = \Carbon\CarbonPeriod::create($dates['start'], $dates['end']);
        foreach ($period as $day) {
            if (!$day->isWeekend()) {
                $workingDays++;
            }
        }

        $avgHoursWorked = null;
        $attendedRecords = $attendances->whereIn('status', ['Present', 'Late']);
        if ($attendedRecords->isNotEmpty()) {
            $attendedEmployeeIds = $attendedRecords->pluck('employee_id')->unique()->values();
            $timetables = Timetable::query()
                ->whereIn('employee_id', $attendedEmployeeIds)
                ->whereBetween('date', [$dates['start']->toDateString(), $dates['end']->toDateString()])
                ->get(['employee_id', 'date', 'start_time', 'end_time']);

            $hoursByEmployeeDate = [];
            foreach ($timetables as $timetable) {
                $dateKey = $timetable->date?->toDateString() ?? (string) $timetable->getRawOriginal('date');
                $startRaw = (string) $timetable->getRawOriginal('start_time');
                $endRaw = (string) $timetable->getRawOriginal('end_time');

                try {
                    $start = Carbon::createFromFormat('H:i:s', $startRaw);
                } catch (\Exception $e) {
                    $start = Carbon::parse($startRaw);
                }

                try {
                    $end = Carbon::createFromFormat('H:i:s', $endRaw);
                } catch (\Exception $e) {
                    $end = Carbon::parse($endRaw);
                }

                $minutes = max(0, $end->diffInMinutes($start));
                $hoursByEmployeeDate[$timetable->employee_id][$dateKey] = ($hoursByEmployeeDate[$timetable->employee_id][$dateKey] ?? 0) + ($minutes / 60);
            }

            $hoursTotal = 0;
            $hoursCount = 0;
            foreach ($attendedRecords as $attendance) {
                $dateKey = $attendance->attendance_date?->toDateString();
                $hours = $hoursByEmployeeDate[$attendance->employee_id][$dateKey] ?? null;
                if ($hours !== null) {
                    $hoursTotal += $hours;
                    $hoursCount++;
                }
            }

            if ($hoursCount > 0) {
                $avgHoursWorked = round($hoursTotal / $hoursCount, 1);
            }
        }

        $byDepartment = $attendances
            ->groupBy(function ($attendance) {
                return $attendance->employee?->department ?: 'Unassigned';
            })
            ->map(function ($deptAttendances, $department) {
                $present = $deptAttendances->where('status', 'Present')->count();
                $late = $deptAttendances->where('status', 'Late')->count();
                $absent = $deptAttendances->where('status', 'Absent')->count();
                $total = $deptAttendances->count();
                $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

                return [
                    'department' => $department,
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'rate' => $rate,
                ];
            })
            ->values()
            ->sortBy('department')
            ->values()
            ->toArray();

        // Employee-level details with hours worked
        $employeeDetails = [];
        foreach ($attendances->groupBy('employee_id') as $employeeAttendances) {
            $attendance = $employeeAttendances->first();
            $employee = $attendance->employee;
            
            // Calculate hours worked for this employee
            $employeeHours = 0;
            $employeeAttended = $employeeAttendances->whereIn('status', ['Present', 'Late']);
            foreach ($employeeAttended as $att) {
                $dateKey = $att->attendance_date?->toDateString();
                $hours = $hoursByEmployeeDate[$employee->id][$dateKey] ?? 0;
                $employeeHours += $hours;
            }
            
            $employeeDetails[] = [
                'employee_name' => $employee->user->name ?? $employee->name ?? 'N/A',
                'employee_id' => $employee->employee_id,
                'designation' => $employee->designation ?? 'Staff',
                'department' => $employee->department,
                'status' => $attendance->status,
                'time_in' => $attendance->check_in ? $attendance->check_in->format('H:i') : 'N/A',
                'time_out' => $attendance->check_out ? $attendance->check_out->format('H:i') : 'N/A',
                'hours_worked' => $employeeHours > 0 ? round($employeeHours, 1) : 'N/A',
                'date' => $attendance->attendance_date->format('Y-m-d'),
            ];
        }

        $summary = [
            'total' => $totalCount,
            'present' => $presentCount,
            'late' => $lateCount,
            'absent' => $absentCount,
            'leave' => $leaveCount,
            'attendance_rate' => 0,
            'avg_hours_worked' => $avgHoursWorked,
            'period' => $dates['label'],
            'exact_period' => $dates['start']->format('F j, Y') . ' - ' . $dates['end']->format('F j, Y'),
            'working_days' => $workingDays,
            'generated_by' => auth()->user()->name,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'report_id' => 'ATT-' . strtoupper(uniqid()),
        ];

        if ($totalCount > 0) {
            $summary['attendance_rate'] = round((($presentCount + $lateCount) / $totalCount) * 100, 1);
        }

        return [
            'report_key' => 'attendance',
            'title' => 'Attendance Report',
            'summary' => $summary,
            'by_department' => $byDepartment,
            'employee_details' => $employeeDetails,
            'data' => $attendances->map(function ($attendance) {
                return [
                    'employee_name' => $attendance->employee->user->name ?? $attendance->employee->name ?? 'N/A',
                    'employee_id' => $attendance->employee->employee_id,
                    'department' => $attendance->employee->department,
                    'date' => $attendance->attendance_date->format('Y-m-d'),
                    'status' => $attendance->status,
                    'remarks' => $attendance->remarks ?? 'N/A'
                ];
            })->toArray()
        ];
    }

    private function getTeamTasksReport($dates, $teamEmployees, $employeeId = null)
    {
        $employeeIds = $teamEmployees->pluck('id');
        
        $query = Task::with(['employee.user'])
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('created_at', [$dates['start'], $dates['end']]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $tasks = $query->get();

        $summary = [
            'total_team_members' => $teamEmployees->count(),
            'total_tasks' => $tasks->count(),
            'completed_tasks' => $tasks->where('status', 'Completed')->count(),
            'in_progress_tasks' => $tasks->where('status', 'In Progress')->count(),
            'pending_tasks' => $tasks->where('status', 'Pending')->count(),
            'overdue_tasks' => $tasks->where('status', '!=', 'Completed')
                ->where('deadline', '<', Carbon::today())->count(),
            'completion_rate' => 0,
            'period' => $dates['label']
        ];

        if ($summary['total_tasks'] > 0) {
            $summary['completion_rate'] = round(($summary['completed_tasks'] / $summary['total_tasks']) * 100, 2);
        }

        return [
            'title' => 'Team Tasks Report',
            'summary' => $summary,
            'data' => $tasks->map(function ($task) {
                return [
                    'employee_name' => $task->employee->user->name,
                    'employee_id' => $task->employee->employee_id,
                    'department' => $task->employee->department,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'priority' => $task->priority ?? 'N/A',
                    'created_date' => $task->created_at->format('Y-m-d'),
                    'deadline' => $task->deadline ? $task->deadline->format('Y-m-d') : 'N/A',
                    'completed_date' => $task->completed_at ? $task->completed_at->format('Y-m-d') : 'N/A'
                ];
            })->toArray()
        ];
    }

    private function getTeamLeavesReport($dates, $teamEmployees, $employeeId = null)
    {
        $employeeIds = $teamEmployees->pluck('id');
        
        $query = LeaveRequest::with(['employee.user', 'reviewer'])
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('start_date', [$dates['start'], $dates['end']]);

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $leaves = $query->get();

        $summary = [
            'total_team_members' => $teamEmployees->count(),
            'total_leaves' => $leaves->count(),
            'approved_leaves' => $leaves->where('status', 'Approved')->count(),
            'pending_leaves' => $leaves->where('status', 'Pending')->count(),
            'rejected_leaves' => $leaves->where('status', 'Rejected')->count(),
            'approval_rate' => 0,
            'period' => $dates['label']
        ];

        if ($summary['total_leaves'] > 0) {
            $summary['approval_rate'] = round(($summary['approved_leaves'] / $summary['total_leaves']) * 100, 2);
        }

        return [
            'title' => 'Team Leave Report',
            'summary' => $summary,
            'data' => $leaves->map(function ($leave) {
                return [
                    'employee_name' => $leave->employee->user->name,
                    'employee_id' => $leave->employee->employee_id,
                    'department' => $leave->employee->department,
                    'leave_type' => $leave->leave_type ?? 'N/A',
                    'start_date' => $leave->start_date->format('Y-m-d'),
                    'end_date' => $leave->end_date->format('Y-m-d'),
                    'days' => $leave->start_date->diffInDays($leave->end_date) + 1,
                    'reason' => $leave->reason,
                    'status' => $leave->status,
                    'reviewed_by' => optional($leave->reviewer)->name ?? 'N/A'
                ];
            })->toArray()
        ];
    }

    private function getTeamPerformanceReport($dates, $teamEmployees, $employeeId = null)
    {
        $employeeIds = $teamEmployees->pluck('id');
        
        $query = Employee::with(['user', 'tasks', 'attendances'])
            ->whereIn('id', $employeeIds);

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();

        $performanceData = $employees->map(function ($employee) use ($dates) {
            $tasks = $employee->tasks()
                ->whereBetween('created_at', [$dates['start'], $dates['end']])
                ->get();
            
            $attendances = $employee->attendances()
                ->whereBetween('attendance_date', [$dates['start'], $dates['end']])
                ->get();

            $totalTasks = $tasks->count();
            $completedTasks = $tasks->where('status', 'Completed')->count();
            $totalAttendance = $attendances->count();
            $presentDays = $attendances->where('status', 'Present')->count();

            $taskCompletionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
            $attendanceRate = $totalAttendance > 0 ? ($presentDays / $totalAttendance) * 100 : 0;
            $performanceScore = ($taskCompletionRate * 0.4) + ($attendanceRate * 0.6);

            return [
                'employee_name' => $employee->user->name,
                'employee_id' => $employee->employee_id,
                'department' => $employee->department,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'task_completion_rate' => round($taskCompletionRate, 2),
                'total_attendance_days' => $totalAttendance,
                'present_days' => $presentDays,
                'attendance_rate' => round($attendanceRate, 2),
                'performance_score' => round($performanceScore, 2)
            ];
        });

        $summary = [
            'total_team_members' => $employees->count(),
            'avg_performance_score' => $performanceData->avg('performance_score'),
            'avg_task_completion' => $performanceData->avg('task_completion_rate'),
            'avg_attendance_rate' => $performanceData->avg('attendance_rate'),
            'top_performer' => $performanceData->sortByDesc('performance_score')->first(),
            'period' => $dates['label']
        ];

        return [
            'title' => 'Team Performance Report',
            'summary' => $summary,
            'data' => $performanceData->toArray()
        ];
    }

    private function generateReportFile($data, $type, $format, $dates)
    {
        $filename = $type . '_report_' . $dates['label'] . '_' . date('Y-m-d_H-i-s');

        if ($format === 'pdf') {
            return $this->generatePDF($data, $filename, $type);
        } else {
            return $this->generateExcel($data, $filename);
        }
    }

    private function generatePDF($data, $filename, $type)
    {
        try {
            $view = $type === 'team_attendance'
                ? 'supervisor.reports.pdf_attendance'
                : 'supervisor.reports.pdf';

            $pdf = Pdf::loadView($view, compact('data'));
            return $pdf->download($filename . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF generation error: ' . $e->getMessage());
            \Log::error('PDF data: ' . json_encode($data));
            throw new \Exception('Failed to generate PDF: ' . $e->getMessage());
        }
    }

    private function generateExcel($data, $filename)
    {
        return Excel::download(new \App\Exports\TeamReportExport($data), $filename . '.xlsx');
    }

    public function destroy(\App\Models\Report $report)
    {
        try {
            // Check if user owns this report
            if ($report->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $report->delete();
            return response()->json(['message' => 'Report deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Report deletion error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete report'], 500);
        }
    }
}
