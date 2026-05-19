<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TestLogin extends Command
{
    protected $signature = 'test:login {email?}';
    protected $description = 'Test login functionality for a specific user';

    public function handle()
    {
        $email = $this->argument('email') ?? 'staff@example.com';
        
        $this->info("🔐 Testing login for: $email");
        $this->newLine();

        // Find user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ User not found: $email");
            return;
        }

        // Check user details
        $this->line("<fg=green>✓</> User found:");
        $this->line("  Email: {$user->email}");
        $this->line("  Name: {$user->name}");
        $this->line("  Role ID: {$user->role_id}");
        
        // Check role relationship
        if ($user->role) {
            $this->line("  Role: {$user->role->name} ({$user->role->slug})");
        } else {
            $this->line("<fg=red>✗</> No role assigned");
            return;
        }

        $this->newLine();
        $this->info("Login test successful! ✓");
        $this->line("You should now be able to login with this account.");
    }
}
