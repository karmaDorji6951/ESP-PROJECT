<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Role;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class TimetableRoleFilteringTest extends TestCase
{
    use DatabaseTransactions;

    public function test_staff_sees_only_employee_or_role_assigned_timetables_in_index(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $supervisorRole = Role::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        $staffRole = Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff']);

        $assigner = User::factory()->create(['role_id' => $adminRole->id]);

        $staffEmployee = Employee::create([
            'name' => 'Staff Employee',
            'cid' => 'CID-STAFF-' . Str::uuid()->toString(),
            'phone' => null,
            'role_title' => 'cleaner',
            'address' => null,
            'joining_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $staffUser = User::factory()->create([
            'role_id' => $staffRole->id,
            'employee_id' => $staffEmployee->id,
        ]);

        $otherEmployee = Employee::create([
            'name' => 'Other Employee',
            'cid' => 'CID-OTHER-' . Str::uuid()->toString(),
            'phone' => null,
            'role_title' => 'guard',
            'address' => null,
            'joining_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $date = now()->toDateString();

        $staffEmployeeTitle = 'Staff-Employee-' . Str::uuid()->toString();
        $staffRoleTitle = 'Staff-Role-' . Str::uuid()->toString();
        $supervisorRoleTitle = 'Supervisor-Role-' . Str::uuid()->toString();

        Timetable::create([
            'title' => $staffEmployeeTitle,
            'description' => null,
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $staffEmployee->id,
            'assigned_by' => $assigner->id,
        ]);

        Timetable::create([
            'title' => $staffRoleTitle,
            'description' => null,
            'date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => null,
            'assigned_by' => $assigner->id,
            'assigned_to_role_id' => $staffRole->id,
        ]);

        Timetable::create([
            'title' => $supervisorRoleTitle,
            'description' => null,
            'date' => $date,
            'start_time' => '11:00',
            'end_time' => '12:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $otherEmployee->id,
            'assigned_by' => $assigner->id,
            'assigned_to_role_id' => $supervisorRole->id,
        ]);

        $response = $this->actingAs($staffUser->fresh())->get('/timetables?view=day&date=' . $date);

        $response->assertOk();
        $response->assertSeeText($staffEmployeeTitle);
        $response->assertSeeText($staffRoleTitle);
        $response->assertDontSeeText($supervisorRoleTitle);
    }

    public function test_supervisor_sees_all_timetables_in_index(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $supervisorRole = Role::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        $staffRole = Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff']);

        $assigner = User::factory()->create(['role_id' => $adminRole->id]);
        $supervisor = User::factory()->create(['role_id' => $supervisorRole->id]);

        $employee = Employee::create([
            'name' => 'Any Employee',
            'cid' => 'CID-ANY-' . Str::uuid()->toString(),
            'phone' => null,
            'role_title' => 'gardener',
            'address' => null,
            'joining_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $date = now()->toDateString();

        $employeeAssignedTitle = 'Employee-Assigned-' . Str::uuid()->toString();
        $roleAssignedTitle = 'Role-Assigned-' . Str::uuid()->toString();

        Timetable::create([
            'title' => $employeeAssignedTitle,
            'description' => null,
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $employee->id,
            'assigned_by' => $assigner->id,
        ]);

        Timetable::create([
            'title' => $roleAssignedTitle,
            'description' => null,
            'date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => null,
            'assigned_by' => $assigner->id,
            'assigned_to_role_id' => $staffRole->id,
        ]);

        $response = $this->actingAs($supervisor->fresh())->get('/timetables?view=day&date=' . $date);

        $response->assertOk();
        $response->assertSeeText($employeeAssignedTitle);
        $response->assertSeeText($roleAssignedTitle);
    }

    public function test_staff_can_view_only_allowed_timetables_show_endpoint(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $supervisorRole = Role::firstOrCreate(['slug' => 'supervisor'], ['name' => 'Supervisor']);
        $staffRole = Role::firstOrCreate(['slug' => 'staff'], ['name' => 'Staff']);

        $assigner = User::factory()->create(['role_id' => $adminRole->id]);

        $staffEmployee = Employee::create([
            'name' => 'Staff Employee',
            'cid' => 'CID-STAFF-' . Str::uuid()->toString(),
            'phone' => null,
            'role_title' => 'cleaner',
            'address' => null,
            'joining_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $staffUser = User::factory()->create([
            'role_id' => $staffRole->id,
            'employee_id' => $staffEmployee->id,
        ]);

        $otherEmployee = Employee::create([
            'name' => 'Other Employee',
            'cid' => 'CID-OTHER-' . Str::uuid()->toString(),
            'phone' => null,
            'role_title' => 'guard',
            'address' => null,
            'joining_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $date = now()->toDateString();

        $employeeAssigned = Timetable::create([
            'title' => 'Show-Employee-' . Str::uuid()->toString(),
            'description' => null,
            'date' => $date,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $staffEmployee->id,
            'assigned_by' => $assigner->id,
        ]);

        $roleAssigned = Timetable::create([
            'title' => 'Show-Role-' . Str::uuid()->toString(),
            'description' => null,
            'date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => null,
            'assigned_by' => $assigner->id,
            'assigned_to_role_id' => $staffRole->id,
        ]);

        $notAllowed = Timetable::create([
            'title' => 'Show-Forbidden-' . Str::uuid()->toString(),
            'description' => null,
            'date' => $date,
            'start_time' => '11:00',
            'end_time' => '12:00',
            'location' => null,
            'priority' => 'medium',
            'status' => 'scheduled',
            'employee_id' => $otherEmployee->id,
            'assigned_by' => $assigner->id,
            'assigned_to_role_id' => $supervisorRole->id,
        ]);

        $this->actingAs($staffUser->fresh())
            ->get('/timetables/' . $employeeAssigned->id)
            ->assertOk();

        $this->actingAs($staffUser->fresh())
            ->get('/timetables/' . $roleAssigned->id)
            ->assertOk();

        $this->actingAs($staffUser->fresh())
            ->get('/timetables/' . $notAllowed->id)
            ->assertForbidden();
    }
}
