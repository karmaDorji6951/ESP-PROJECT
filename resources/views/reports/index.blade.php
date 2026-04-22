@extends('layouts.app')

@section('title', 'Reports')
@section('page_title', 'Reports')

@section('content')
<div class="d-flex gap-2 mb-4">
    <a class="btn btn-outline-primary" href="{{ route('reports.attendance.csv') }}">Export Attendance CSV</a>
    <a class="btn btn-outline-primary" href="{{ route('reports.performance.csv') }}">Export Performance CSV</a>
</div>

<div class="card card-soft">
    <div class="card-header bg-white fw-semibold">Employee Performance Summary</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr><th>Employee</th><th>CID</th><th>Total Tasks</th><th>Completed Tasks</th><th>Present Days</th></tr>
            </thead>
            <tbody>
                @forelse($performance as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{{ $row->cid }}</td>
                        <td>{{ $row->total_tasks_count }}</td>
                        <td>{{ $row->completed_tasks_count }}</td>
                        <td>{{ $row->present_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted p-4">No performance data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
