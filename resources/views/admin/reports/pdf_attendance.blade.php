<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ ($data['title'] ?? 'Attendance Report') }}</title>
    <style>
        @page { margin: 28px 36px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { width: 100%; margin-bottom: 18px; }
        .header td { font-size: 11px; color: #444; }
        .header .center { text-align: center; font-weight: 600; color: #111; }

        h1 { font-size: 16px; margin: 0 0 16px 0; font-weight: 700; text-align: left; }

        .kpis { width: 100%; border-collapse: collapse; margin: 0 0 18px 0; }
        .kpis td { padding: 6px 0; text-align: center; }
        .kpi-number { font-size: 18px; font-weight: 700; line-height: 1.1; }
        .kpi-label { font-size: 10px; color: #666; margin-top: 2px; }

        .dual { width: 100%; border-collapse: collapse; margin: 10px 0 18px 0; }
        .dual td { width: 50%; vertical-align: top; }
        .metric-label { font-size: 11px; color: #444; margin-bottom: 4px; }
        .metric-value { font-size: 18px; font-weight: 800; }

        h2 { font-size: 12px; margin: 14px 0 8px 0; font-weight: 700; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border-top: 1px solid #eee; padding: 6px 8px; }
        table.data th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; color: #666; text-align: left; padding-top: 10px; }
        table.data td { font-size: 11px; }

        .right { text-align: right; }
    </style>
</head>
<body>
@php
    $summary = $data['summary'] ?? [];
    $period = $summary['period'] ?? '';

    $total = (int) ($summary['total'] ?? 0);
    $present = (int) ($summary['present'] ?? 0);
    $late = (int) ($summary['late'] ?? 0);
    $absent = (int) ($summary['absent'] ?? 0);
    $leave = (int) ($summary['leave'] ?? 0);

    $attendanceRate = $summary['attendance_rate'] ?? null;
    $avgHours = $summary['avg_hours_worked'] ?? null;

    $byDepartment = $data['by_department'] ?? [];
@endphp

<table class="header">
    <tr>
        <td>{{ now()->format('n/j/y, g:i A') }}</td>
        <td class="center">ElemServ Report</td>
        <td class="right"></td>
    </tr>
</table>

<h1>Attendance Report — {{ $period }}</h1>

<table class="kpis">
    <tr>
        <td>
            <div class="kpi-number">{{ $total }}</div>
            <div class="kpi-label">Total</div>
        </td>
        <td>
            <div class="kpi-number">{{ $present }}</div>
            <div class="kpi-label">Present</div>
        </td>
        <td>
            <div class="kpi-number">{{ $late }}</div>
            <div class="kpi-label">Late</div>
        </td>
        <td>
            <div class="kpi-number">{{ $absent }}</div>
            <div class="kpi-label">Absent</div>
        </td>
        <td>
            <div class="kpi-number">{{ $leave }}</div>
            <div class="kpi-label">Leave</div>
        </td>
    </tr>
</table>

<table class="dual">
    <tr>
        <td>
            <div class="metric-label">Attendance Rate</div>
            <div class="metric-value">{{ $attendanceRate !== null ? rtrim(rtrim(number_format((float) $attendanceRate, 1), '0'), '.') : '0' }}%</div>
        </td>
        <td>
            <div class="metric-label">Avg Hours Worked</div>
            <div class="metric-value">
                @if($avgHours !== null)
                    {{ rtrim(rtrim(number_format((float) $avgHours, 1), '0'), '.') }}h
                @else
                    -
                @endif
            </div>
        </td>
    </tr>
</table>

<h2>By Department</h2>
<table class="data">
    <thead>
        <tr>
            <th>Department</th>
            <th class="right">Present</th>
            <th class="right">Late</th>
            <th class="right">Absent</th>
            <th class="right">Rate</th>
        </tr>
    </thead>
    <tbody>
        @forelse($byDepartment as $row)
            <tr>
                <td>{{ $row['department'] ?? 'Unassigned' }}</td>
                <td class="right">{{ (int) ($row['present'] ?? 0) }}</td>
                <td class="right">{{ (int) ($row['late'] ?? 0) }}</td>
                <td class="right">{{ (int) ($row['absent'] ?? 0) }}</td>
                <td class="right">{{ rtrim(rtrim(number_format((float) ($row['rate'] ?? 0), 1), '0'), '.') }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="color:#666; padding: 10px 8px;">No attendance records found for this period.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_text(520, 810, "{PAGE_NUM}/{PAGE_COUNT}", null, 9, [0.4,0.4,0.4]);
    }
</script>
</body>
</html>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ ($data['title'] ?? 'Attendance Report') }}</title>
    <style>
        @page { margin: 28px 36px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { width: 100%; margin-bottom: 18px; }
        .header td { font-size: 11px; color: #444; }
        .header .center { text-align: center; font-weight: 600; color: #111; }

        h1 { font-size: 16px; margin: 0 0 16px 0; font-weight: 700; text-align: left; }

        .kpis { width: 100%; border-collapse: collapse; margin: 0 0 18px 0; }
        .kpis td { padding: 6px 0; text-align: center; }
        .kpi-number { font-size: 18px; font-weight: 700; line-height: 1.1; }
        .kpi-label { font-size: 10px; color: #666; margin-top: 2px; }

        .dual { width: 100%; border-collapse: collapse; margin: 10px 0 18px 0; }
        .dual td { width: 50%; vertical-align: top; }
        .metric-label { font-size: 11px; color: #444; margin-bottom: 4px; }
        .metric-value { font-size: 18px; font-weight: 800; }

        h2 { font-size: 12px; margin: 14px 0 8px 0; font-weight: 700; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border-top: 1px solid #eee; padding: 6px 8px; }
        table.data th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; color: #666; text-align: left; padding-top: 10px; }
        table.data td { font-size: 11px; }

        .right { text-align: right; }
    </style>
</head>
<body>
@php
    $summary = $data['summary'] ?? [];
    $period = $summary['period'] ?? '';

    $total = (int) ($summary['total'] ?? 0);
    $present = (int) ($summary['present'] ?? 0);
    $late = (int) ($summary['late'] ?? 0);
    $absent = (int) ($summary['absent'] ?? 0);
    $leave = (int) ($summary['leave'] ?? 0);

    $attendanceRate = $summary['attendance_rate'] ?? null;
    $avgHours = $summary['avg_hours_worked'] ?? null;

    $byDepartment = $data['by_department'] ?? [];
@endphp

<table class="header">
    <tr>
        <td>{{ now()->format('n/j/y, g:i A') }}</td>
        <td class="center">ElemServ Report</td>
        <td class="right"></td>
    </tr>
</table>

<h1>Attendance Report — {{ $period }}</h1>

<table class="kpis">
    <tr>
        <td>
            <div class="kpi-number">{{ $total }}</div>
            <div class="kpi-label">Total</div>
        </td>
        <td>
            <div class="kpi-number">{{ $present }}</div>
            <div class="kpi-label">Present</div>
        </td>
        <td>
            <div class="kpi-number">{{ $late }}</div>
            <div class="kpi-label">Late</div>
        </td>
        <td>
            <div class="kpi-number">{{ $absent }}</div>
            <div class="kpi-label">Absent</div>
        </td>
        <td>
            <div class="kpi-number">{{ $leave }}</div>
            <div class="kpi-label">Leave</div>
        </td>
    </tr>
</table>

<table class="dual">
    <tr>
        <td>
            <div class="metric-label">Attendance Rate</div>
            <div class="metric-value">{{ $attendanceRate !== null ? rtrim(rtrim(number_format((float) $attendanceRate, 1), '0'), '.') : '0' }}%</div>
        </td>
        <td>
            <div class="metric-label">Avg Hours Worked</div>
            <div class="metric-value">
                @if($avgHours !== null)
                    {{ rtrim(rtrim(number_format((float) $avgHours, 1), '0'), '.') }}h
                @else
                    -
                @endif
            </div>
        </td>
    </tr>
</table>

<h2>By Department</h2>
<table class="data">
    <thead>
        <tr>
            <th>Department</th>
            <th class="right">Present</th>
            <th class="right">Late</th>
            <th class="right">Absent</th>
            <th class="right">Rate</th>
        </tr>
    </thead>
    <tbody>
        @forelse($byDepartment as $row)
            <tr>
                <td>{{ $row['department'] ?? 'Unassigned' }}</td>
                <td class="right">{{ (int) ($row['present'] ?? 0) }}</td>
                <td class="right">{{ (int) ($row['late'] ?? 0) }}</td>
                <td class="right">{{ (int) ($row['absent'] ?? 0) }}</td>
                <td class="right">{{ rtrim(rtrim(number_format((float) ($row['rate'] ?? 0), 1), '0'), '.') }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="color:#666; padding: 10px 8px;">No attendance records found for this period.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_text(520, 810, "{PAGE_NUM}/{PAGE_COUNT}", null, 9, [0.4,0.4,0.4]);
    }
</script>
</body>
</html>
