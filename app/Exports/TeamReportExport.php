<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /** @var array<string, mixed> */
    protected array $report;

    /**
     * @param array<string, mixed> $report
     */
    public function __construct(array $report)
    {
        $this->report = $report;
    }

    public function headings(): array
    {
        $rows = $this->report['data'] ?? [];
        if (!is_array($rows) || count($rows) === 0 || !is_array($rows[0])) {
            return [];
        }

        return array_keys($rows[0]);
    }

    public function array(): array
    {
        $rows = $this->report['data'] ?? [];
        if (!is_array($rows) || count($rows) === 0) {
            return [];
        }

        return array_map(function ($row) {
            if (!is_array($row)) {
                return [$row];
            }

            return array_values($row);
        }, $rows);
    }
}
