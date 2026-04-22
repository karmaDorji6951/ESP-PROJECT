<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
            $table->text('submission_notes')->nullable();
            $table->json('submission_data')->nullable();
            $table->enum('submission_status', ['Submitted', 'Under Review', 'Approved', 'Rejected'])->default('Submitted');
            $table->timestamp('submitted_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
