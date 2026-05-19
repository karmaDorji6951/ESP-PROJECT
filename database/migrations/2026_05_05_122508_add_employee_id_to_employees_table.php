<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->after('id');
        });

        // Backfill from CID so existing UI fields aren't blank.
        DB::statement("UPDATE employees SET employee_id = cid WHERE employee_id IS NULL");

        Schema::table('employees', function (Blueprint $table) {
            $table->unique('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['employee_id']);
            $table->dropColumn('employee_id');
        });
    }
};
