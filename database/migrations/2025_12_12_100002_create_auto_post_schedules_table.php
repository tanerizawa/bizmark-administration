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
        Schema::create('auto_post_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('article_topics')->onDelete('cascade');
            $table->timestamp('scheduled_at'); // When to generate & post
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('generation_time_seconds')->nullable(); // Performance tracking
            $table->json('metadata')->nullable(); // Job details, model used, etc.
            $table->timestamps();
            
            $table->index(['scheduled_at', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_post_schedules');
    }
};
