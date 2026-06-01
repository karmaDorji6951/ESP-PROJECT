<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$hasDepartmentsTable = Schema::hasTable('departments');
// Check for departments table and employees.department_id
$hasEmployeeDepartmentColumn = Schema::hasColumn('employees', 'department');
$hasEmployeeDepartmentId = Schema::hasColumn('employees', 'department_id');

echo "Database department check:\n";
echo "- departments table: " . ($hasDepartmentsTable ? 'YES' : 'NO') . "\n";
echo "- employees.department column: " . ($hasEmployeeDepartmentColumn ? 'YES' : 'NO') . "\n";
echo "- employees.department_id column: " . ($hasEmployeeDepartmentId ? 'YES' : 'NO') . "\n\n";

if (!Schema::hasTable('departments')) {
    echo "No departments table detected.\n";
    exit(0);
}

$departments = \App\Models\Department::orderBy('name')->get();

echo "Departments table contents:\n";
foreach ($departments as $dept) {
    echo "- " . $dept->name . ( $dept->parent_id ? ' (child of ' . optional($dept->parent)->name . ')' : '' ) . "\n";
}
echo "\nTotal: " . $departments->count() . " departments\n";
