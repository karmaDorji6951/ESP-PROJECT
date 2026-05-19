<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_evaluations', function (Blueprint $table) {
            $table->json('criteria')->nullable()->after('staff_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('task_evaluations', function (Blueprint $table) {
            $table->dropColumn('criteria');
        });
    }
};
