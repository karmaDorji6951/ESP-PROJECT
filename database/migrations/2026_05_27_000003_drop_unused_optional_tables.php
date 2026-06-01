<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Best-for-system default: keep optional Laravel tables unless you explicitly opt-in.
        if (! (bool) env('ESP_DROP_UNUSED_TABLES', false)) {
            return;
        }

        $force = (bool) env('ESP_FORCE_DROP_UNUSED_TABLES', false);

        // Only drop tables that are not used by this installation.
        // Default behavior is conservative: drop only when empty.
        $candidates = [
            'sessions' => [
                'in_use' => fn () => config('session.driver') === 'database',
                'reason' => 'SESSION_DRIVER=database uses the sessions table',
            ],
            'failed_jobs' => [
                'in_use' => fn () => config('queue.default') !== 'sync',
                'reason' => 'QUEUE_CONNECTION != sync uses failed_jobs for failure logging',
            ],
            'personal_access_tokens' => [
                // We can’t reliably infer Sanctum usage from config alone; allow explicit override.
                'in_use' => fn () => (bool) env('ESP_USE_SANCTUM', false),
                'reason' => 'Set ESP_USE_SANCTUM=true if Sanctum tokens are used',
            ],
            'password_reset_tokens' => [
                // Same: allow explicit override for password reset feature.
                'in_use' => fn () => (bool) env('ESP_USE_PASSWORD_RESETS', false),
                'reason' => 'Set ESP_USE_PASSWORD_RESETS=true if password reset is used',
            ],
        ];

        foreach ($candidates as $table => $meta) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (($meta['in_use'])()) {
                continue;
            }

            if ($force) {
                Schema::drop($table);
                continue;
            }

            // Default: only drop if empty.
            try {
                $count = (int) DB::table($table)->count();
            } catch (\Throwable $e) {
                // If it can't be queried safely, don't drop.
                continue;
            }

            if ($count === 0) {
                Schema::drop($table);
            }
        }
    }

    public function down(): void
    {
        // Re-create tables using the stock Laravel schemas (best-effort).

        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }
};
