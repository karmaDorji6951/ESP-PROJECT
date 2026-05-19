<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PruneUsers extends Command
{
    protected $signature = 'users:prune {--dry-run : Show what would be deleted without deleting}';

    protected $description = 'Deletes all users except the configured allowed login accounts.';

    public function handle(): int
    {
        $allowedEmails = collect(config('access.allowed_login_emails', []))
            ->map(fn ($email) => Str::lower(trim((string) $email)))
            ->filter();

        if ($allowedEmails->isEmpty()) {
            $this->error('No allowed_login_emails configured (config/access.php). Aborting.');
            return self::FAILURE;
        }

        $query = User::query()->whereNotIn('email', $allowedEmails->all());
        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('No users to prune.');
            return self::SUCCESS;
        }

        $this->line("Users to prune: {$count}");

        if ($this->option('dry-run')) {
            (clone $query)
                ->orderBy('id')
                ->get(['id', 'name', 'email'])
                ->each(function ($user) {
                    $this->line("- {$user->id}\t{$user->name}\t{$user->email}");
                });

            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Deleted users: {$deleted}");

        return self::SUCCESS;
    }
}
