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

        return $from->diffInDays($to) + 1;
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

        $approved = self::usedDaysForYear($employeeId, $year, ['Approved']);
        $pending = self::usedDaysForYear($employeeId, $year, ['Pending']);

        $totalUsed = $approved + $pending;
        $allowance = self::annualAllowance();
        $remaining = max(0, $allowance - $totalUsed);

        return [
            'allowance' => $allowance,
            'approved' => $approved,
            'pending' => $pending,
            'used' => $totalUsed,
            'remaining' => $remaining,
            'per_month' => round($allowance / 12, 2),
            'per_week' => round($allowance / 52, 2),
        ];
    }
}