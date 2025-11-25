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
        Schema::create('interview_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            
            // Interview details
            $table->enum('interview_type', ['preliminary', 'technical', 'hr', 'final'])->default('preliminary');
            $table->tinyInteger('interview_stage')->default(1)->comment('Stage number in sequence');
            
            // Scheduling
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            
            // Location & meeting
            $table->string('location')->nullable()->comment('online, office, or specific address');
            $table->enum('meeting_type', ['in-person', 'video-call', 'phone'])->default('video-call');
            $table->text('meeting_link')->nullable()->comment('Zoom/Jitsi meeting URL');
            $table->string('meeting_password')->nullable();
            
            // Participants
            $table->json('interviewer_ids')->nullable()->comment('Array of user IDs');
            
            // Status tracking
            $table->enum('status', ['scheduled', 'confirmed', 'rescheduled', 'completed', 'cancelled', 'no-show'])->default('scheduled');
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index('scheduled_at');
            $table->index('status');
            $table->index(['job_application_id', 'interview_stage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_schedules');
    }
};
