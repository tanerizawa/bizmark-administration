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
        Schema::create('recruitment_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            
            $table->string('stage_name', 100)->comment('screening, psych-test, technical-test, interview-1, etc');
            $table->integer('stage_order')->comment('Sequential order');
            $table->enum('status', ['pending', 'in-progress', 'passed', 'failed', 'skipped'])->default('pending');
            
            $table->decimal('score', 5, 2)->nullable()->comment('Stage score if applicable');
            $table->text('notes')->nullable();
            
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Unique constraint - one stage per application
            $table->unique(['job_application_id', 'stage_name'], 'unique_stage');
            
            // Indexes
            $table->index(['job_application_id', 'stage_order']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitment_stages');
    }
};
