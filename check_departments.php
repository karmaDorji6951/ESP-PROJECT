<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$hasDepartmentsTable = Schema::hasTable('departments');
$hasEmployeeDepartmentColumn = Schema::hasColumn('employees', 'department');

echo "Database department check:\n";
echo "- departments table: " . ($hasDepartmentsTable ? 'YES' : 'NO') . "\n";
echo "- employees.department column: " . ($hasEmployeeDepartmentColumn ? 'YES' : 'NO') . "\n\n";

if (!$hasEmployeeDepartmentColumn) {
    echo "Your system does not currently store departments on employees (missing employees.department).\n";
    echo "If you want to use departments, add a nullable department column to the employees table.\n";
    exit(0);
}

$departments = \App\Models\Employee::query()
    ->distinct()
    ->whereNotNull('department')
    ->pluck('department')
    ->filter()
    ->values();

echo "Current departments in your system:\n";
foreach ($departments as $department) {
    echo "- " . $department . "\n";
}
echo "\nTotal: " . count($departments) . " departments\n";
