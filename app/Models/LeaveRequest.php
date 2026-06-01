<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'leaves';

    protected $fillable = [
        'user_id',
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFromDateAttribute()
    {
        return $this->start_date;
    }

    public function getToDateAttribute()
    {
        return $this->end_date;
    }

    public static function annualAllowance(): int
    {
        return 21;
    }

    public static function weeklyQuota(): int
    {
        return max(1, (int) ceil(self::annualAllowance() / 52));
    }

    public static function monthlyQuota(): int
    {
        return max(1, (int) ceil(self::annualAllowance() / 12));
    }

    public static function workingDaysBetween($start, $end): int
    {
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->startOfDay();

        if ($startDate->gt($endDate)) {
            return 0;
        }

        $days = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (! $date->isWeekend()) {
                $days++;
            }
        }

        return $days;
    }

    public static function periodUsage(int $employeeId, Carbon $periodStart, Carbon $periodEnd, array $statuses = ['Approved', 'Pending']): int
    {
        $leaves = self::where('employee_id', $employeeId)
            ->whereIn('status', $statuses)
            ->whereDate('start_date', '<=', $periodEnd->format('Y-m-d'))
            ->whereDate('end_date', '>=', $periodStart->format('Y-m-d'))
            ->get();

        $usedDays = 0;

        foreach ($leaves as $leave) {
            $overlapStart = Carbon::parse($leave->start_date)->greaterThan($periodStart)
                ? Carbon::parse($leave->start_date)
                : $periodStart->copy();

            $overlapEnd = Carbon::parse($leave->end_date)->lessThan($periodEnd)
                ? Carbon::parse($leave->end_date)
                : $periodEnd->copy();

            $usedDays += self::workingDaysBetween($overlapStart, $overlapEnd);
        }

        return $usedDays;
    }

    public static function workingDaysInPeriod($start, $end, Carbon $periodStart, Carbon $periodEnd): int
    {
        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->startOfDay();

        $from = $startDate->greaterThan($periodStart) ? $startDate : $periodStart->copy();
        $to = $endDate->lessThan($periodEnd) ? $endDate : $periodEnd->copy();

        if ($from->gt($to)) {
            return 0;
        }

        return self::workingDaysBetween($from, $to);
    }

    private static function overlapDaysWithYear($start, $end, $year)
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->endOfDay();

        $from = $startDate->greaterThan($yearStart) ? $startDate : $yearStart;
        $to = $endDate->lessThan($yearEnd) ? $endDate : $yearEnd;

        if ($from->gt($to)) {
            return 0;
        }

        return self::workingDaysBetween($from, $to);
    }

    public static function usedDaysForYear(int $employeeId, int $year, array $statuses = ['Approved']): int
    {
        $query = self::where('employee_id', $employeeId)
            ->whereIn('status', $statuses)
            ->where(function ($q) use ($year) {
                $q->whereYear('start_date', $year)
                  ->orWhereYear('end_date', $year)
                  ->orWhere(function ($q2) use ($year) {
                      $q2->where('start_date', '<', Carbon::create($year, 1, 1))
                         ->where('end_date', '>', Carbon::create($year, 12, 31));
                  });
            })
            ->get();

        $days = 0;
        foreach ($query as $leave) {
            $days += self::overlapDaysWithYear($leave->start_date, $leave->end_date, $year);
        }

        return $days;
    }

    public static function getLeaveUsage(int $employeeId, int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weekEnd = $now->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        $monthStart = $now->copy()->startOfMonth()->startOfDay();
        $monthEnd = $now->copy()->endOfMonth()->endOfDay();

        $approved = self::usedDaysForYear($employeeId, $year, ['Approved']);
        $pending = self::usedDaysForYear($employeeId, $year, ['Pending']);
        $weekUsed = self::periodUsage($employeeId, $weekStart, $weekEnd);
        $monthUsed = self::periodUsage($employeeId, $monthStart, $monthEnd);

        $totalUsed = $approved + $pending;
        $allowance = self::annualAllowance();
        $remaining = max(0, $allowance - $totalUsed);

        return [
            'allowance' => $allowance,
            'approved' => $approved,
            'pending' => $pending,
            'used' => $totalUsed,
            'remaining' => $remaining,
            'weekly_quota' => self::weeklyQuota(),
            'monthly_quota' => self::monthlyQuota(),
            'weekly_used' => $weekUsed,
            'monthly_used' => $monthUsed,
            'weekly_remaining' => max(0, self::weeklyQuota() - $weekUsed),
            'monthly_remaining' => max(0, self::monthlyQuota() - $monthUsed),
            'current_week_start' => $weekStart,
            'current_week_end' => $weekEnd,
            'current_month_start' => $monthStart,
            'current_month_end' => $monthEnd,
        ];
    }
}