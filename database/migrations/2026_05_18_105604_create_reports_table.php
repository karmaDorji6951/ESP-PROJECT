<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('report_type'); // team_attendance, team_tasks, team_leaves, team_performance
            $table->string('date_range'); // this_month, last_month, last_3_months, custom
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('period_label'); // e.g., "May 2026"
            $table->string('format'); // pdf, excel
            $table->string('file_path')->nullable();
            $table->string('report_id')->unique(); // e.g., "ATT-ABC123"
            $table->json('summary_data')->nullable(); // Store summary statistics
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
