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
        $allowedLoginEmails = [
            'karma.dorji@rim.edu.bt',
            'sonam.tobgay@rim.edu.bt',
            'karma.wangdi@rim.edu.bt',
            'pema.choden@rim.edu.bt',
            'sonam.tashi@rim.edu.bt',
        ];

        $adminRole = Role::updateOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $supervisorRole = Role::updateOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        $staffRole = Role::updateOrCreate(['slug' => 'staff'], ['name' => 'Staff']);

        // Remove legacy placeholder demo staff accounts if they exist.
        User::whereIn('name', ['Staff 1', 'Staff 2', 'Staff 3'])->delete();
        User::whereIn('email', ['staff1@esp.local', 'staff2@esp.local', 'staff3@esp.local'])->delete();
        User::whereIn('email', ['admin@esp.local', 'supervisor@esp.local'])->delete();

        $admin = User::updateOrCreate(
            ['email' => 'karma.dorji@rim.edu.bt'],
            [
                'name' => 'Karma Dorji',
                'phone' => '17123456',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        $supervisor = User::updateOrCreate(
            ['email' => 'sonam.tobgay@rim.edu.bt'],
            [
                'name' => 'Sonam Tobgay',
                'phone' => '17654321',
                'password' => Hash::make('password'),
                'role_id' => $supervisorRole->id,
            ]
        );

        // Create individual staff users
        $staff1 = User::updateOrCreate(
            ['email' => 'karma.wangdi@rim.edu.bt'],
            [
                'name' => 'Karma Wangdi',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
            ]
        );

        $staff2 = User::updateOrCreate(
            ['email' => 'pema.choden@rim.edu.bt'],
            [
                'name' => 'Pema Choden',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
            ]
        );

        $staff3 = User::updateOrCreate(
            ['email' => 'sonam.tashi@rim.edu.bt'],
            [
                'name' => 'Sonam Tashi',
                'password' => Hash::make('password'),
                'role_id' => $staffRole->id,
            ]
        );

        // Remove any other user accounts so only the allowed logins remain.
        User::whereNotIn('email', $allowedLoginEmails)->delete();

        $employees = collect([
            ['name' => 'Karma Wangdi', 'cid' => '11806007891', 'phone' => '17112233', 'role_title' => 'Security Guard', 'department' => 'Security', 'address' => 'Paro', 'joining_date' => '2025-01-15'],
            ['name' => 'Pema Choden', 'cid' => '12005001234', 'phone' => '77123456', 'role_title' => 'Cleaner', 'department' => 'Maintenance', 'address' => 'Chhukha', 'joining_date' => '2025-02-01'],
            ['name' => 'Sonam Tashi', 'cid' => '11504005678', 'phone' => '17654321', 'role_title' => 'Gardener', 'department' => 'Grounds', 'address' => 'Samtse', 'joining_date' => '2025-03-10'],
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

        // Assign employees to corresponding staff users
        $staff1->update(['employee_id' => $employees[0]->id]);
        $staff2->update(['employee_id' => $employees[1]->id]);
        $staff3->update(['employee_id' => $employees[2]->id]);

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
            'assigned_by' => $supervisor->id,
            'description' => 'Inspect all security checkpoints, verify visitor logs, and report any incidents.',
            'status' => 'Pending',
            'deadline' => today()->addDays(1)->toDateString(),
        ]);

        // Additional tasks for Staff 2
        Task::updateOrCreate([
            'employee_id' => $employees[1]->id,
            'title' => 'Main gate security duty',
        ], [
            'assigned_by' => $supervisor->id,
            'description' => 'Monitor main school gate, check visitor passes, ensure student safety during entry/exit times.',
            'status' => 'Pending',
            'deadline' => today()->addDays(3)->toDateString(),
        ]);

        Task::updateOrCreate([
            'employee_id' => $employees[1]->id,
            'title' => 'Evening patrol rounds',
        ], [
            'assigned_by' => $supervisor->id,
            'description' => 'Conduct evening security patrol around school campus, check all doors and windows.',
            'status' => 'Pending',
            'deadline' => today()->addDays(2)->toDateString(),
        ]);

        LeaveRequest::updateOrCreate([
            'user_id' => $staff3->id,
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
