<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $supervisorRole = Role::updateOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        $staffRole = Role::updateOrCreate(['slug' => 'staff'], ['name' => 'Staff']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@esp.local'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        $supervisor = User::updateOrCreate(
            ['email' => 'supervisor@esp.local'],
            [
                'name' => 'Line Supervisor',
                'password' => Hash::make('password'),
                'role_id' => $supervisorRole->id,
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@esp.local'],
            [
                'name' => 'Sample Staff',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
            ]
        );

        $employees = collect([
            ['name' => 'Pema Dorji', 'cid' => '11111111111', 'phone' => '17111111', 'role_title' => 'Cleaner', 'address' => 'Thimphu', 'joining_date' => '2025-01-15'],
            ['name' => 'Sonam Choden', 'cid' => '22222222222', 'phone' => '17222222', 'role_title' => 'Guard', 'address' => 'Paro', 'joining_date' => '2025-02-01'],
            ['name' => 'Karma Wangdi', 'cid' => '33333333333', 'phone' => '17333333', 'role_title' => 'Helper', 'address' => 'Punakha', 'joining_date' => '2025-03-10'],
        ])->map(fn ($employee) => Employee::updateOrCreate(['cid' => $employee['cid']], $employee + ['status' => 'Active']));

        foreach ($employees as $employee) {
            Attendance::updateOrCreate([
                'employee_id' => $employee->id,
                'attendance_date' => today()->subDays(1)->toDateString(),
            ], [
                'status' => 'Present',
                'remarks' => 'Sample seed data',
                'marked_by' => $admin->id,
            ]);
        }

        $staff->update(['employee_id' => $employees[2]->id]);

        Task::updateOrCreate([
            'employee_id' => $employees[0]->id,
            'title' => 'Clean classroom block A',
        ], [
            'assigned_by' => $supervisor->id,
            'description' => 'Complete cleaning before morning assembly.',
            'status' => 'In Progress',
            'deadline' => today()->addDays(2)->toDateString(),
        ]);

        Task::updateOrCreate([
            'employee_id' => $employees[1]->id,
            'title' => 'Security checkpoint review',
        ], [
            'assigned_by' => $admin->id,
            'description' => 'Inspect gates and report incidents.',
            'status' => 'Pending',
            'deadline' => today()->addDays(1)->toDateString(),
        ]);

        LeaveRequest::updateOrCreate([
            'user_id' => $staff->id,
            'leave_type' => 'Casual Leave',
            'start_date' => today()->addDays(5)->toDateString(),
        ], [
            'employee_id' => $employees[2]->id,
            'end_date' => today()->addDays(6)->toDateString(),
            'reason' => 'Family emergency',
            'status' => 'Pending',
        ]);
    }
}
