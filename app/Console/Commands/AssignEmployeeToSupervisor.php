<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;

class AssignEmployeeToSupervisor extends Command
{
    protected $signature = 'supervisor:assign-employee';
    protected $description = 'Assign an employee to the supervisor user';

    public function handle()
    {
        $emp = Employee::skip(0)->first(); // Get first employee or different one if available
        
        if (!$emp) {
            $this->error('No employees found in database.');
            return 1;
        }

        $user = User::where('email', 'supervisor@example.com')->first();
        
        if (!$user) {
            $this->error('Supervisor user not found');
            return 1;
        }

        $user->update(['employee_id' => $emp->id]);
        
        $this->info("✓ Supervisor user ($user->email) assigned to employee: $emp->name (ID: $emp->id)");
        return 0;
    }
}
