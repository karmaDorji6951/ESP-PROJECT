<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Role;
use App\Models\Task;
use App\Models\Timetable;
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

        // Add sample timetable entries for Elementary Service Personnel
        Timetable::updateOrCreate([
            'title' => 'Classroom Cleaning - Block A',
            'date' => today()->toDateString(),
        ], [
            'description' => 'Daily cleaning and sanitization of all classrooms in Block A including floors, desks, and windows',
            'start_time' => '06:00',
            'end_time' => '08:00',
            'location' => 'Block A Classrooms',
            'priority' => 'high',
            'status' => 'scheduled',
            'employee_id' => $employees[0]->id,
            'assigned_by' => $supervisor->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'School Gate Security Duty',
            'date' => today()->toDateString(),
        ], [
            'description' => 'Monitor main school gate, check visitor passes, ensure student safety during entry/exit times',
            'start_time' => '07:30',
            'end_time' => '09:30',
            'location' => 'Main School Gate',
            'priority' => 'high',
            'status' => 'scheduled',
            'employee_id' => $employees[1]->id,
            'assigned_by' => $supervisor->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'Playground Maintenance',
            'date' => today()->toDateString(),
        ], [
            'description' => 'Clean playground area, check equipment safety, remove debris, ensure safe play environment',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'location' => 'School Playground',
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $employees[2]->id,
            'assigned_by' => $supervisor->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'Lunch Hall Cleaning',
            'date' => today()->toDateString(),
        ], [
            'description' => 'Clean and sanitize lunch hall before and after lunch service, clean tables, floors, and serving areas',
            'start_time' => '11:30',
            'end_time' => '14:00',
            'location' => 'School Lunch Hall',
            'priority' => 'high',
            'status' => 'scheduled',
            'assigned_to_role' => 'staff',
            'assigned_by' => $admin->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'Waste Collection & Disposal',
            'date' => today()->addDays(1)->toDateString(),
        ], [
            'description' => 'Collect waste from all school areas, sort recyclables, dispose of garbage properly',
            'start_time' => '15:00',
            'end_time' => '16:30',
            'location' => 'Entire School Campus',
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $employees[0]->id,
            'assigned_by' => $supervisor->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'Restroom Cleaning & Sanitization',
            'date' => today()->addDays(1)->toDateString(),
        ], [
            'description' => 'Thorough cleaning and sanitization of all student and staff restrooms, refill supplies',
            'start_time' => '13:00',
            'end_time' => '15:00',
            'location' => 'All School Restrooms',
            'priority' => 'high',
            'status' => 'scheduled',
            'employee_id' => $employees[1]->id,
            'assigned_by' => $supervisor->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'Library & Study Area Maintenance',
            'date' => today()->addDays(2)->toDateString(),
        ], [
            'description' => 'Dust library shelves, clean study tables, organize reading areas, ensure cleanliness',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'location' => 'School Library',
            'priority' => 'low',
            'status' => 'scheduled',
            'employee_id' => $employees[2]->id,
            'assigned_by' => $supervisor->id,
        ]);

        Timetable::updateOrCreate([
            'title' => 'Weekly ESP Team Meeting',
            'date' => today()->addDays(3)->toDateString(),
        ], [
            'description' => 'Weekly coordination meeting for all Elementary Service Personnel to discuss schedules and issues',
            'start_time' => '16:00',
            'end_time' => '17:00',
            'location' => 'ESP Staff Room',
            'priority' => 'medium',
            'status' => 'scheduled',
            'assigned_to_role' => 'staff',
            'assigned_by' => $admin->id,
        ]);
    }
}
