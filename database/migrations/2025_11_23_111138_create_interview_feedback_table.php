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
        Schema::create('interview_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->constrained('users')->onDelete('cascade');
            
            // Scoring (1-10 scale)
            $table->tinyInteger('technical_skills')->nullable()->comment('1-10 rating');
            $table->tinyInteger('communication')->nullable()->comment('1-10 rating');
            $table->tinyInteger('problem_solving')->nullable()->comment('1-10 rating');
            $table->tinyInteger('cultural_fit')->nullable()->comment('1-10 rating');
            $table->tinyInteger('motivation')->nullable()->comment('1-10 rating');
            $table->decimal('overall_rating', 3, 1)->nullable()->comment('Average score');
            
            // Qualitative feedback
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('detailed_notes')->nullable();
            
            // Recommendation
            $table->enum('recommendation', ['strong-hire', 'hire', 'maybe', 'no-hire']);
            
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Unique constraint - one feedback per interviewer per interview
            $table->unique(['interview_schedule_id', 'interviewer_id'], 'unique_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interview_feedback');
    }
};
