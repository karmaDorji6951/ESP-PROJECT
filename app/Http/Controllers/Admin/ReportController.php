<?php

namespace App\Http\Controllers\Admin;

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
        $departments = Employee::query()
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        $employees = Employee::query()
            ->with('user')
            ->orderBy('employee_id')
            ->get();

        return view('admin.reports.index', compact('user', 'departments', 'employees'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string|in:attendance,tasks,leaves,performance,employees',
            'date_range' => 'required|string|in:this_month,last_month,last_3_months,last_6_months,custom',
            'start_date' => 'required_if:date_range,custom|date',
            'end_date' => 'required_if:date_range,custom|date|after_or_equal:start_date',
            'format' => 'required|string|in:pdf,excel',
            'department' => 'nullable|string',
            'employee_id' => 'nullable|integer|exists:employees,id'
        ]);

        $reportType = $request->input('report_type');
        $dateRange = $request->input('date_range');
        $format = $request->input('format');
        $department = $request->input('department');
        $employeeId = $request->input('employee_id');

        // Get date range
        $dates = $this->getDateRange($dateRange, $request);
        
        // Generate report data based on type
        $data = $this->generateReportData($reportType, $dates, $department, $employeeId);
        
        // Generate and return download response
        return $this->generateReportFile($data, $reportType, $format, $dates);
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
            case 'last_6_months':
                return [
                    'start' => Carbon::now()->subMonths(5)->startOfMonth(),
                    'end' => Carbon::now()->endOfMonth(),
                    'label' => 'Last 6 Months'
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

    private function generateReportData($type, $dates, $department = null, $employeeId = null)
    {
        switch ($type) {
            case 'attendance':
                return $this->getAttendanceReport($dates, $department, $employeeId);
            case 'tasks':
                return $this->getTasksReport($dates, $department, $employeeId);
            case 'leaves':
                return $this->getLeavesReport($dates, $department, $employeeId);
            case 'performance':
                return $this->getPerformanceReport($dates, $department, $employeeId);
            case 'employees':
                return $this->getEmployeesReport($department);
            default:
                return [];
        }
    }

    private function getAttendanceReport($dates, $department = null, $employeeId = null)
    {
        $query = Attendance::with(['employee.user'])
            ->whereBetween('attendance_date', [$dates['start'], $dates['end']]);

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get();

        $presentCount = $attendances->where('status', 'Present')->count();
        $lateCount = $attendances->where('status', 'Late')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $leaveCount = $attendances->where('status', 'Leave')->count();
        $totalCount = $attendances->count();

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
            ->map(function ($deptAttendances, $departmentName) {
                $present = $deptAttendances->where('status', 'Present')->count();
                $late = $deptAttendances->where('status', 'Late')->count();
                $absent = $deptAttendances->where('status', 'Absent')->count();
                $total = $deptAttendances->count();
                $rate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

                return [
                    'department' => $departmentName,
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

        $summary = [
            'total' => $totalCount,
            'present' => $presentCount,
            'late' => $lateCount,
            'absent' => $absentCount,
            'leave' => $leaveCount,
            'attendance_rate' => 0,
            'avg_hours_worked' => $avgHoursWorked,
            'period' => $dates['label']
        ];

        if ($totalCount > 0) {
            $summary['attendance_rate'] = round((($presentCount + $lateCount) / $totalCount) * 100, 1);
        }

        return [
            'report_key' => 'attendance',
            'title' => 'Attendance Report',
            'summary' => $summary,
            'by_department' => $byDepartment,
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

    private function getTasksReport($dates, $department = null, $employeeId = null)
    {
        $query = Task::with(['employee.user'])
            ->whereBetween('created_at', [$dates['start'], $dates['end']]);

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $tasks = $query->get();

        $summary = [
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
            'title' => 'Tasks Report',
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

    private function getLeavesReport($dates, $department = null, $employeeId = null)
    {
        $query = LeaveRequest::with(['employee.user', 'reviewer'])
            ->whereBetween('start_date', [$dates['start'], $dates['end']]);

        if ($department) {
            $query->whereHas('employee', function ($q) use ($department) {
                $q->where('department', $department);
            });
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $leaves = $query->get();

        $summary = [
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
            'title' => 'Leave Report',
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

    private function getPerformanceReport($dates, $department = null, $employeeId = null)
    {
        $query = Employee::with(['user', 'tasks', 'attendances'])
            ->whereHas('user');

        if ($department) {
            $query->where('department', $department);
        }

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
            'total_employees' => $employees->count(),
            'avg_performance_score' => $performanceData->avg('performance_score'),
            'avg_task_completion' => $performanceData->avg('task_completion_rate'),
            'avg_attendance_rate' => $performanceData->avg('attendance_rate'),
            'period' => $dates['label']
        ];

        return [
            'title' => 'Performance Report',
            'summary' => $summary,
            'data' => $performanceData->toArray()
        ];
    }

    private function getEmployeesReport($department = null)
    {
        $query = Employee::with('user');

        if ($department) {
            $query->where('department', $department);
        }

        $employees = $query->get();

        $summary = [
            'total_employees' => $employees->count(),
            'active_employees' => $employees->where('status', 'Active')->count(),
            'inactive_employees' => $employees->where('status', 'Inactive')->count(),
            'departments' => $employees->pluck('department')->unique()->count()
        ];

        return [
            'title' => 'Employees Report',
            'summary' => $summary,
            'data' => $employees->map(function ($employee) {
                return [
                    'employee_name' => $employee->user->name,
                    'employee_id' => $employee->employee_id,
                    'email' => $employee->user->email,
                    'department' => $employee->department,
                    'position' => $employee->position ?? 'N/A',
                    'status' => $employee->status,
                    'join_date' => $employee->created_at->format('Y-m-d'),
                    'phone' => $employee->phone ?? 'N/A',
                    'address' => $employee->address ?? 'N/A'
                ];
            })->toArray()
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
        $view = $type === 'attendance'
            ? 'admin.reports.pdf_attendance'
            : 'admin.reports.pdf';

        $pdf = Pdf::loadView($view, compact('data'));
        return $pdf->download($filename . '.pdf');
    }

    private function generateExcel($data, $filename)
    {
        return Excel::download(new \App\Exports\ReportExport($data), $filename . '.xlsx');
    }
}
