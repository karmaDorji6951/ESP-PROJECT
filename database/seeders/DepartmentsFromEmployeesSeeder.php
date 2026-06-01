<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Department;
use App\Models\Employee;

class DepartmentsFromEmployeesSeeder extends Seeder
{
    public function run()
    {
        // Create top-level buildings and child areas.
        $examples = [
            'Block 1' => ['Corridor', 'Garden', 'Entrance'],
            'Block 2' => ['Parking'],
        ];

        $assignedStaff = [
            ['name' => 'Pema Choden', 'cid' => '12005001234', 'phone' => '77123456', 'role_title' => 'Cleaner', 'building' => 'Block 1', 'area' => 'Corridor', 'address' => 'Chhukha', 'joining_date' => '2025-02-01'],
            ['name' => 'Sonam Tashi', 'cid' => '11504005678', 'phone' => '17654321', 'role_title' => 'Gardener', 'building' => 'Block 1', 'area' => 'Garden', 'address' => 'Samtse', 'joining_date' => '2025-03-10'],
            ['name' => 'Dorji Wangmo', 'cid' => '11907004567', 'phone' => '17223344', 'role_title' => 'Sweeper', 'building' => 'Block 1', 'area' => 'Entrance', 'address' => 'Thimphu', 'joining_date' => '2025-04-05'],
            ['name' => 'Karma Wangdi', 'cid' => '11806007891', 'phone' => '17112233', 'role_title' => 'Security Guard', 'building' => 'Block 2', 'area' => 'Parking', 'address' => 'Paro', 'joining_date' => '2025-01-15'],
        ];

        DB::transaction(function () use ($examples, $assignedStaff) {
            $buildingNames = array_keys($examples);

            $oldDepartmentIds = Department::query()
                ->where(function ($query) use ($buildingNames) {
                    $query->whereNull('parent_id')
                        ->whereNotIn('name', $buildingNames);
                })
                ->orWhere(function ($query) use ($examples) {
                    $query->whereNotNull('parent_id')
                        ->whereNotIn('name', collect($examples)->flatten()->all());
                })
                ->pluck('id');

            if ($oldDepartmentIds->isNotEmpty()) {
                DB::table('employees')
                    ->whereIn('department_id', $oldDepartmentIds)
                    ->update(['department_id' => null]);

                Department::query()
                    ->whereIn('id', $oldDepartmentIds)
                    ->delete();
            }

            foreach ($examples as $parentName => $children) {
                $parent = Department::firstOrCreate([
                    'name' => $parentName,
                ], [
                    'slug' => Str::slug($parentName),
                ]);

                foreach ($children as $child) {
                    Department::firstOrCreate([
                        'name' => $child,
                        'parent_id' => $parent->id,
                    ], [
                        'slug' => Str::slug($parentName . ' ' . $child),
                    ]);
                }
            }

            foreach ($assignedStaff as $staff) {
                $area = Department::query()
                    ->where('name', $staff['area'])
                    ->whereHas('parent', fn ($query) => $query->where('name', $staff['building']))
                    ->first();

                Employee::updateOrCreate(
                    ['cid' => $staff['cid']],
                    [
                        'name' => $staff['name'],
                        'phone' => $staff['phone'],
                        'role_title' => $staff['role_title'],
                        'department_id' => $area?->id,
                        'address' => $staff['address'],
                        'joining_date' => $staff['joining_date'],
                        'status' => 'Active',
                    ]
                );
            }

            if (! Schema::hasColumn('employees', 'department')) {
                return;
            }

            // Migrate existing employees.department strings into departments and set department_id
            $excludedDepartments = ['IT', 'DFM', 'Administration', 'Grounds', 'Security'];
            $distinct = DB::table('employees')
                ->select('department')
                ->whereNotNull('department')
                ->where('department', '!=', '')
                ->whereNotIn('department', $excludedDepartments)
                ->distinct()
                ->pluck('department');

            foreach ($distinct as $deptName) {
                $dept = Department::firstOrCreate([
                    'name' => $deptName,
                ], [
                    'slug' => Str::slug($deptName),
                ]);

                DB::table('employees')
                    ->where('department', $deptName)
                    ->update(['department_id' => $dept->id]);
            }
        });
    }
}
