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
        // Add recruitment_stage_id to test_sessions
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->foreignId('recruitment_stage_id')
                  ->nullable()
                  ->after('job_application_id')
                  ->constrained('recruitment_stages')
                  ->nullOnDelete()
                  ->comment('Link to specific recruitment stage');
            
            $table->index('recruitment_stage_id');
        });

        // Add recruitment_stage_id to interview_schedules
        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->foreignId('recruitment_stage_id')
                  ->nullable()
                  ->after('job_application_id')
                  ->constrained('recruitment_stages')
                  ->nullOnDelete()
                  ->comment('Link to specific recruitment stage');
            
            $table->index('recruitment_stage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_sessions', function (Blueprint $table) {
            $table->dropForeign(['recruitment_stage_id']);
            $table->dropIndex(['recruitment_stage_id']);
            $table->dropColumn('recruitment_stage_id');
        });

        Schema::table('interview_schedules', function (Blueprint $table) {
            $table->dropForeign(['recruitment_stage_id']);
            $table->dropIndex(['recruitment_stage_id']);
            $table->dropColumn('recruitment_stage_id');
        });
    }
};
