<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_submission_id')->nullable()->constrained('task_submissions')->nullOnDelete();
            $table->foreignId('evaluated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('staff_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('grade', 2); // A-F
            $table->text('remarks')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->unique('task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_evaluations');
    }
};
