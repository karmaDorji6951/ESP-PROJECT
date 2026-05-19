<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // MySQL enum alteration (production target for this app).
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY status ENUM('Present','Late','Absent','Leave') NOT NULL");
        }

        // For other drivers (sqlite/pgsql), this migration is a no-op.
        // If you need cross-db support, we can migrate to a string column.
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE attendances MODIFY status ENUM('Present','Absent','Leave') NOT NULL");
        }
    }
};
