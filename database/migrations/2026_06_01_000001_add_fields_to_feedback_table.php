<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->foreignId('building_department_id')
                ->nullable()
                ->after('recipient_user_id')
                ->constrained('departments')
                ->nullOnDelete();

            $table->foreignId('area_department_id')
                ->nullable()
                ->after('building_department_id')
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('feedback_type', 20)
                ->nullable()
                ->after('area_department_id');

            $table->string('priority', 10)
                ->nullable()
                ->after('feedback_type');

            $table->string('status', 20)
                ->default('Pending')
                ->after('priority');

            $table->boolean('is_anonymous')
                ->default(false)
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn(['feedback_type', 'priority', 'status', 'is_anonymous']);
            $table->dropConstrainedForeignId('area_department_id');
            $table->dropConstrainedForeignId('building_department_id');
        });
    }
};
