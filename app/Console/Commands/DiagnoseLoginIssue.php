<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DiagnoseLoginIssue extends Command
{
    protected $signature = 'diagnose:login';
    protected $description = 'Diagnose login and session issues';

    public function handle()
    {
        $this->info('🔍 Diagnosing login issues...');
        $this->newLine();

        // Check 1: Session directory
        $this->info('1️⃣ Checking session directory...');
        $sessionPath = config('session.files');
        if (is_dir($sessionPath)) {
            $this->line("<fg=green>✓</> Session directory exists: $sessionPath");
        } else {
            $this->line("<fg=red>✗</> Session directory NOT found: $sessionPath");
            $this->warn('Creating session directory...');
            mkdir($sessionPath, 0755, true);
        }

        if (is_writable($sessionPath)) {
            $this->line("<fg=green>✓</> Session directory is writable");
        } else {
            $this->line("<fg=red>✗</> Session directory is NOT writable");
        }
        $this->newLine();

        // Check 2: Database connection
        $this->info('2️⃣ Checking database connection...');
        try {
            DB::connection()->getPdo();
            $this->line("<fg=green>✓</> Database connection successful");
        } catch (\Exception $e) {
            $this->line("<fg=red>✗</> Database connection failed: {$e->getMessage()}");
            return;
        }
        $this->newLine();

        // Check 3: Users and Roles
        $this->info('3️⃣ Checking users and roles...');
        try {
            $userCount = DB::table('users')->count();
            $roleCount = DB::table('roles')->count();
            $this->line("<fg=green>✓</> Users: $userCount, Roles: $roleCount");

            // Check for users without roles
            $usersWithoutRoles = DB::table('users')->whereNull('role_id')->count();
            if ($usersWithoutRoles > 0) {
                $this->line("<fg=yellow>⚠</> $usersWithoutRoles users without assigned roles");
            }

            // Show staff/supervisor users
            $staffUsers = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->whereIn('roles.slug', ['staff', 'supervisor'])
                ->select('users.email', 'roles.name', 'users.role_id')
                ->get();

            if ($staffUsers->count() > 0) {
                $this->line("<fg=green>✓</> Staff/Supervisor users found:");
                foreach ($staffUsers as $user) {
                    $this->line("  - {$user->email} ({$user->name}, role_id: {$user->role_id})");
                }
            } else {
                $this->line("<fg=red>✗</> No staff/supervisor users found");
            }
        } catch (\Exception $e) {
            $this->line("<fg=red>✗</> Error checking users: {$e->getMessage()}");
        }
        $this->newLine();

        // Check 4: Session driver
        $this->info('4️⃣ Checking session driver configuration...');
        $driver = config('session.driver');
        $lifetime = config('session.lifetime');
        $secure = config('session.secure');
        $samesite = config('session.same_site');
        $this->line("Driver: <fg=cyan>$driver</>");
        $this->line("Lifetime: <fg=cyan>$lifetime</> minutes");
        $this->line("HTTPS Only: <fg=cyan>" . ($secure ? 'Yes' : 'No') . "</>");
        $this->line("Same-Site: <fg=cyan>$samesite</>");
        $this->newLine();

        // Check 5: APP_KEY
        $this->info('5️⃣ Checking APP_KEY...');
        if (config('app.key')) {
            $this->line("<fg=green>✓</> APP_KEY is set");
        } else {
            $this->line("<fg=red>✗</> APP_KEY is NOT set");
        }
        $this->newLine();

        // Recommendations
        $this->info('📋 Recommendations:');
        $this->line('1. Try clearing cache and sessions:');
        $this->line('   <fg=cyan>php artisan cache:clear</>');
        $this->line('   <fg=cyan>php artisan session:clear</>');
        $this->line('   <fg=cyan>php artisan route:clear</>');
        $this->newLine();
        $this->line('2. Try deleting session files:');
        $this->line("   <fg=cyan>rm -rf " . $sessionPath . "/*</>");
        $this->newLine();
        $this->line('3. Ensure all users have valid roles assigned:');
        $this->line('   <fg=cyan>php artisan tinker</> then run:');
        $this->line('   <fg=cyan>User::where("role_id", null)->update(["role_id" => 3])</> (for staff role id 3)');
        $this->newLine();
    }
}
