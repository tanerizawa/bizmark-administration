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
        Schema::create('auto_post_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->nullable()->constrained('auto_post_schedules')->onDelete('set null');
            $table->foreignId('article_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('topic_id')->nullable()->constrained('article_topics')->onDelete('set null');
            $table->enum('level', ['info', 'warning', 'error', 'success'])->default('info');
            $table->string('event'); // "generation_started", "article_published", etc.
            $table->text('message');
            $table->json('context')->nullable(); // Additional data
            $table->integer('word_count')->nullable();
            $table->integer('reading_time')->nullable();
            $table->integer('internal_links')->nullable();
            $table->float('ai_cost')->nullable(); // OpenRouter cost tracking
            $table->timestamp('created_at');

            $table->index(['created_at', 'level']);
            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_post_logs');
    }
};
