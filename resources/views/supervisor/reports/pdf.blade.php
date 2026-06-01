<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $data['title'] ?? 'Team Report' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1, h2, h3 { margin: 0 0 8px 0; }
        .meta { margin-bottom: 14px; color: #444; }
        .summary { margin: 10px 0 16px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary td { padding: 6px 8px; border: 1px solid #ddd; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 6px 8px; vertical-align: top; }
        table.data th { background: #f5f5f5; }
        .muted { color: #666; }
    </style>
</head>
<body>
@php
    $title = $data['title'] ?? 'Team Report';
    $summary = $data['summary'] ?? [];
    $rows = $data['data'] ?? [];
    $headings = (is_array($rows) && count($rows) > 0 && is_array($rows[0])) ? array_keys($rows[0]) : [];
    $labelFor = function ($key) {
        return match ((string) $key) {
            'department' => 'Area',
            'departments' => 'Areas',
            default => str_replace('_', ' ', ucfirst((string) $key)),
        };
    };
@endphp

<h2>{{ $title }}</h2>
<div class="meta">
    <div class="muted">Generated: {{ now()->format('Y-m-d H:i') }}</div>
</div>

@if(is_array($summary) && count($summary))
    <div class="summary">
        <h3>Summary</h3>
        <table>
            <tbody>
            @foreach($summary as $key => $value)
                <tr>
                    <td><strong>{{ $labelFor($key) }}</strong></td>
                    <td>{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(is_array($rows) && count($rows))
    <h3>Details</h3>
    <table class="data">
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $labelFor($heading) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($headings as $heading)
                        @php($value = is_array($row) ? ($row[$heading] ?? '') : '')
                        <td>{{ is_scalar($value) ? $value : json_encode($value) }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="muted">No records found for this report.</p>
@endif

</body>
</html>
