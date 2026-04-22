<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $performance = Employee::withCount([
            'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'Completed'),
            'tasks as total_tasks_count',
            'attendances as present_count' => fn ($query) => $query->where('status', 'Present'),
        ])->get();

        return view('reports.index', compact('performance'));
    }

    public function attendanceCsv(Request $request): StreamedResponse
    {
        $rows = Attendance::with('employee')->orderByDesc('attendance_date')->get();
        $filename = 'attendance-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee', 'CID', 'Date', 'Status', 'Remarks']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->employee?->name,
                    $row->employee?->cid,
                    $row->attendance_date?->format('Y-m-d'),
                    $row->status,
                    $row->remarks,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function performanceCsv(): StreamedResponse
    {
        $rows = Employee::withCount([
            'tasks as completed_tasks_count' => fn ($query) => $query->where('status', 'Completed'),
            'tasks as total_tasks_count',
            'attendances as present_count' => fn ($query) => $query->where('status', 'Present'),
        ])->get();

        $filename = 'performance-summary-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee', 'CID', 'Total Tasks', 'Completed Tasks', 'Present Days']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->name,
                    $row->cid,
                    $row->total_tasks_count,
                    $row->completed_tasks_count,
                    $row->present_count,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
