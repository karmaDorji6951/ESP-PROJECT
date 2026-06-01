<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // NOTE: In this database, tbldzongkhag.id is INT (not BIGINT). Foreign keys must match types.
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            if (Schema::hasTable('employees')) {
                if (! Schema::hasColumn('employees', 'dzongkhag_id')) {
                    Schema::table('employees', function (Blueprint $table) {
                        $table
                            ->integer('dzongkhag_id')
                            ->nullable()
                            ->after(Schema::hasColumn('employees', 'department_id') ? 'department_id' : 'role_title');
                    });
                } else {
                    // If a previous failed migration created this as BIGINT, normalize it back to INT.
                    DB::statement('ALTER TABLE `employees` MODIFY `dzongkhag_id` INT NULL');
                }

                // Drop any existing FK on employees.dzongkhag_id so we can re-add it cleanly.
                $existingEmployeeFk = DB::table('information_schema.KEY_COLUMN_USAGE')
                    ->whereRaw('TABLE_SCHEMA = DATABASE()')
                    ->where('TABLE_NAME', 'employees')
                    ->where('COLUMN_NAME', 'dzongkhag_id')
                    ->whereNotNull('REFERENCED_TABLE_NAME')
                    ->value('CONSTRAINT_NAME');

                if ($existingEmployeeFk) {
                    DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `' . $existingEmployeeFk . '`');
                }

                // Add FK only if it doesn't exist.
                $existingEmployeeFkAfter = DB::table('information_schema.KEY_COLUMN_USAGE')
                    ->whereRaw('TABLE_SCHEMA = DATABASE()')
                    ->where('TABLE_NAME', 'employees')
                    ->where('COLUMN_NAME', 'dzongkhag_id')
                    ->whereNotNull('REFERENCED_TABLE_NAME')
                    ->count();

                if ($existingEmployeeFkAfter === 0 && Schema::hasTable('tbldzongkhag')) {
                    // Ensure there is an index (required for FK).
                    $hasIndex = DB::table('information_schema.STATISTICS')
                        ->whereRaw('TABLE_SCHEMA = DATABASE()')
                        ->where('TABLE_NAME', 'employees')
                        ->where('COLUMN_NAME', 'dzongkhag_id')
                        ->count();

                    if ($hasIndex === 0) {
                        DB::statement('ALTER TABLE `employees` ADD INDEX `employees_dzongkhag_id_index` (`dzongkhag_id`)');
                    }

                    Schema::table('employees', function (Blueprint $table) {
                        $table
                            ->foreign('dzongkhag_id', 'employees_dzongkhag_id_foreign')
                            ->references('id')
                            ->on('tbldzongkhag')
                            ->nullOnDelete();
                    });
                }
            }
        } else {
            Schema::table('employees', function (Blueprint $table) {
                if (! Schema::hasColumn('employees', 'dzongkhag_id')) {
                    $table
                        ->foreignId('dzongkhag_id')
                        ->nullable()
                        ->after(Schema::hasColumn('employees', 'department_id') ? 'department_id' : 'role_title')
                        ->constrained('tbldzongkhag')
                        ->nullOnDelete();
                }
            });
        }

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'gewog_id')) {
                $table
                    ->foreignId('gewog_id')
                    ->nullable()
                    ->after(Schema::hasColumn('employees', 'dzongkhag_id') ? 'dzongkhag_id' : (Schema::hasColumn('employees', 'department_id') ? 'department_id' : 'role_title'))
                    ->constrained('tblgewog')
                    ->nullOnDelete();
            }
        });

        // Normalize tblgewog.dzongkhag_id into a real FK.
        if (Schema::hasTable('tblgewog') && Schema::hasTable('tbldzongkhag') && Schema::hasColumn('tblgewog', 'dzongkhag_id')) {
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                // Ensure column type matches tbldzongkhag.id (INT)
                DB::statement('ALTER TABLE `tblgewog` MODIFY `dzongkhag_id` INT NULL');

                // If there are any orphan references from legacy data, null them out so the FK can be added safely.
                DB::statement(
                    'UPDATE `tblgewog` g '
                    . 'LEFT JOIN `tbldzongkhag` d ON d.`id` = g.`dzongkhag_id` '
                    . 'SET g.`dzongkhag_id` = NULL '
                    . 'WHERE g.`dzongkhag_id` IS NOT NULL AND d.`id` IS NULL'
                );

                // Add FK only if it doesn't exist (avoid errors on partially-migrated DBs)
                $existingFk = DB::table('information_schema.KEY_COLUMN_USAGE')
                    ->whereRaw('TABLE_SCHEMA = DATABASE()')
                    ->where('TABLE_NAME', 'tblgewog')
                    ->where('COLUMN_NAME', 'dzongkhag_id')
                    ->whereNotNull('REFERENCED_TABLE_NAME')
                    ->count();

                if ($existingFk === 0) {
                    // Ensure there is an index (required for FK).
                    $hasIndex = DB::table('information_schema.STATISTICS')
                        ->whereRaw('TABLE_SCHEMA = DATABASE()')
                        ->where('TABLE_NAME', 'tblgewog')
                        ->where('COLUMN_NAME', 'dzongkhag_id')
                        ->count();

                    if ($hasIndex === 0) {
                        DB::statement('ALTER TABLE `tblgewog` ADD INDEX `tblgewog_dzongkhag_id_index` (`dzongkhag_id`)');
                    }

                    Schema::table('tblgewog', function (Blueprint $table) {
                        $table
                            ->foreign('dzongkhag_id', 'tblgewog_dzongkhag_id_fk')
                            ->references('id')
                            ->on('tbldzongkhag')
                            ->cascadeOnDelete();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'gewog_id')) {
                    $table->dropConstrainedForeignId('gewog_id');
                }

                if (Schema::hasColumn('employees', 'dzongkhag_id')) {
                    $table->dropConstrainedForeignId('dzongkhag_id');
                }
            });
        }

        if (Schema::hasTable('tblgewog')) {
            $driver = Schema::getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                // Drop FK if present.
                $existingFk = DB::table('information_schema.TABLE_CONSTRAINTS')
                    ->whereRaw('CONSTRAINT_SCHEMA = DATABASE()')
                    ->where('TABLE_NAME', 'tblgewog')
                    ->where('CONSTRAINT_NAME', 'tblgewog_dzongkhag_id_fk')
                    ->count();

                if ($existingFk > 0) {
                    Schema::table('tblgewog', function (Blueprint $table) {
                        $table->dropForeign('tblgewog_dzongkhag_id_fk');
                    });
                }

                // Best-effort: revert to INT.
                if (Schema::hasColumn('tblgewog', 'dzongkhag_id')) {
                    DB::statement('ALTER TABLE `tblgewog` MODIFY `dzongkhag_id` INT NULL');
                }
            }
        }
    }
};
