<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (! Schema::hasColumn('timetables', 'assigned_to_role_id')) {
                $table
                    ->foreignId('assigned_to_role_id')
                    ->nullable()
                    ->after('assigned_by')
                    ->constrained('roles')
                    ->nullOnDelete();

                $table->index('assigned_to_role_id');
            }
        });

        // Backfill role id from legacy slug column.
        if (Schema::hasColumn('timetables', 'assigned_to_role')) {
            $driver = Schema::getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement(
                    "UPDATE timetables t\n".
                    "JOIN roles r ON r.slug = t.assigned_to_role\n".
                    "SET t.assigned_to_role_id = r.id\n".
                    "WHERE t.assigned_to_role_id IS NULL AND t.assigned_to_role IS NOT NULL"
                );
            } else {
                // Generic fallback (slower but portable).
                $roleMap = DB::table('roles')->pluck('id', 'slug');
                DB::table('timetables')
                    ->select('id', 'assigned_to_role')
                    ->whereNull('assigned_to_role_id')
                    ->whereNotNull('assigned_to_role')
                    ->orderBy('id')
                    ->chunkById(200, function ($rows) use ($roleMap) {
                        foreach ($rows as $row) {
                            $roleId = $roleMap[$row->assigned_to_role] ?? null;
                            if ($roleId) {
                                DB::table('timetables')->where('id', $row->id)->update(['assigned_to_role_id' => $roleId]);
                            }
                        }
                    });
            }
        }

        // Drop legacy column once backfilled.
        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'assigned_to_role')) {
                $table->dropColumn('assigned_to_role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            if (! Schema::hasColumn('timetables', 'assigned_to_role')) {
                $table->string('assigned_to_role')->nullable()->after('assigned_by');
                $table->index('assigned_to_role');
            }
        });

        // Best-effort reverse backfill.
        if (Schema::hasColumn('timetables', 'assigned_to_role_id')) {
            $driver = Schema::getConnection()->getDriverName();

            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement(
                    "UPDATE timetables t\n".
                    "LEFT JOIN roles r ON r.id = t.assigned_to_role_id\n".
                    "SET t.assigned_to_role = r.slug\n".
                    "WHERE t.assigned_to_role IS NULL AND t.assigned_to_role_id IS NOT NULL"
                );
            } else {
                $roleMap = DB::table('roles')->pluck('slug', 'id');
                DB::table('timetables')
                    ->select('id', 'assigned_to_role_id')
                    ->whereNull('assigned_to_role')
                    ->whereNotNull('assigned_to_role_id')
                    ->orderBy('id')
                    ->chunkById(200, function ($rows) use ($roleMap) {
                        foreach ($rows as $row) {
                            $slug = $roleMap[$row->assigned_to_role_id] ?? null;
                            if ($slug) {
                                DB::table('timetables')->where('id', $row->id)->update(['assigned_to_role' => $slug]);
                            }
                        }
                    });
            }
        }

        Schema::table('timetables', function (Blueprint $table) {
            if (Schema::hasColumn('timetables', 'assigned_to_role_id')) {
                $table->dropConstrainedForeignId('assigned_to_role_id');
            }
        });
    }
};
