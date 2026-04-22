<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'assignment_type')) {
                $table->enum('assignment_type', ['date', 'week', 'month'])->default('date')->after('description');
            }

            if (! Schema::hasColumn('tasks', 'schedule_start_date')) {
                $table->date('schedule_start_date')->nullable()->after('assignment_type');
            }

            if (! Schema::hasColumn('tasks', 'schedule_end_date')) {
                $table->date('schedule_end_date')->nullable()->after('schedule_start_date');
            }
        });

        DB::statement("UPDATE tasks SET assignment_type = 'date' WHERE assignment_type IS NULL");

        DB::statement(
            "UPDATE tasks
             SET schedule_start_date = COALESCE(schedule_start_date, deadline, DATE(created_at), CURDATE()),
                 schedule_end_date = COALESCE(schedule_end_date, deadline, DATE(created_at), CURDATE())"
        );
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['assignment_type', 'schedule_start_date', 'schedule_end_date']);
        });
    }
};
