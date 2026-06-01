<?php

namespace App\Console\Commands;

use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetLeaveUsage extends Command
{
    protected $signature = 'leave:reset
        {emailOrEmployeeId : User email (e.g. staff@esp.local) or employee_id}
        {--year= : Reset only leave entries overlapping this year (default: current year)}
        {--all : Reset all leave entries for the employee (ignores --year)}
        {--force : Do not prompt for confirmation}';

    protected $description = 'Reset (delete) leave entries for an employee so leave usage returns to zero';

    public function handle(): int
    {
        $identifier = (string) $this->argument('emailOrEmployeeId');

        $employeeId = null;
        $user = null;

        if (ctype_digit($identifier)) {
            $employeeId = (int) $identifier;
            $user = User::query()->where('employee_id', $employeeId)->first();
        } else {
            $user = User::query()->where('email', $identifier)->first();
            $employeeId = $user?->employee_id;
        }

        if (! $employeeId) {
            $this->error('No employee_id found for the provided identifier.');
            $this->line('Tip: pass a numeric employee_id or a user email that has employee_id set.');
            return self::FAILURE;
        }

        $deleteAll = (bool) $this->option('all');
        $year = (int) ($this->option('year') ?: Carbon::now()->year);

        $query = LeaveRequest::query()->where('employee_id', $employeeId);

        $scopeLabel = 'ALL years';
        if (! $deleteAll) {
            $yearStart = Carbon::create($year, 1, 1)->startOfDay();
            $yearEnd = Carbon::create($year, 12, 31)->endOfDay();

            $query->where(function ($q) use ($year, $yearStart, $yearEnd) {
                $q->whereYear('start_date', $year)
                    ->orWhereYear('end_date', $year)
                    ->orWhere(function ($q2) use ($yearStart, $yearEnd) {
                        $q2->where('start_date', '<', $yearStart)
                            ->where('end_date', '>', $yearEnd);
                    });
            });

            $scopeLabel = (string) $year;
        }

        $count = (clone $query)->count();

        $userLabel = $user ? "{$user->email} (user_id: {$user->id})" : 'unknown user';
        $this->info("Target employee_id: {$employeeId}");
        $this->line("Resolved user: {$userLabel}");
        $this->line("Scope: {$scopeLabel}");
        $this->line("Leave rows to delete: {$count}");

        if ($count === 0) {
            $this->info('Nothing to reset.');
            return self::SUCCESS;
        }

        if (! $this->option('force')) {
            if (! $this->confirm('This will DELETE leave records. Continue?', false)) {
                $this->warn('Aborted.');
                return self::SUCCESS;
            }
        }

        $deleted = $query->delete();

        $this->info("Deleted {$deleted} leave record(s). Leave usage should now be zero for this scope.");

        return self::SUCCESS;
    }
}
