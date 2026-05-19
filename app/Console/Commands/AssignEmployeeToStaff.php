<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Console\Command;

class AssignEmployeeToStaff extends Command
{
    protected $signature = 'staff:assign-employee';
    protected $description = 'Assign an employee to the staff user';

    public function handle()
    {
        $emp = Employee::first();
        
        if (!$emp) {
            $this->error('No employees found in database. Please create an employee first.');
            return 1;
        }

        $user = User::where('email', 'staff@example.com')->first();
        
        if (!$user) {
            $this->error('Staff user not found');
            return 1;
        }

        $user->update(['employee_id' => $emp->id]);
        
        $this->info("✓ Staff user ($user->email) assigned to employee: $emp->name (ID: $emp->id)");
        return 0;
    }
}
