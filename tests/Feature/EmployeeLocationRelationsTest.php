<?php

namespace Tests\Feature;

use App\Models\Dzongkhag;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EmployeeLocationRelationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_employee_with_dzongkhag_and_gewog_and_relations_resolve(): void
    {
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // These lookup tables may exist already (and may not have timestamps),
        // so insert minimally via the query builder.
        $dzongkhagId = DB::table('tbldzongkhag')->insertGetId(['name' => 'Thimphu']);
        $gewogId = DB::table('tblgewog')->insertGetId(['name' => 'Kawang', 'dzongkhag_id' => $dzongkhagId]);

        $cid = 'CID-LOC-' . Str::uuid()->toString();

        $response = $this->actingAs($admin->fresh())->post(route('admin.employees.store'), [
            'name' => 'Location Employee',
            'cid' => $cid,
            'phone' => '17123456',
            'role_title' => 'cleaner',
            'dzongkhag_id' => $dzongkhagId,
            'gewog_id' => $gewogId,
            'address' => 'Somewhere',
            'joining_date' => now()->toDateString(),
            'status' => 'Active',
        ]);

        $response->assertRedirect(route('admin.employees.index'));

        $employee = Employee::where('cid', $cid)->firstOrFail();

        $this->assertSame($dzongkhagId, $employee->dzongkhag_id);
        $this->assertSame($gewogId, $employee->gewog_id);

        $this->assertSame('Thimphu', $employee->dzongkhag?->name);
        $this->assertSame('Kawang', $employee->gewog?->name);
        $this->assertSame($dzongkhagId, $employee->gewog?->dzongkhag_id);
    }
}
