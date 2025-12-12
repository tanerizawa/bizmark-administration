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
        Schema::create('auto_post_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false); // Master switch
            $table->integer('posts_per_day')->default(3);
            $table->json('post_times'); // ["08:00", "13:00", "19:00"] - Prime times
            $table->string('timezone')->default('Asia/Jakarta');
            
            // AI Settings
            $table->string('ai_model')->default('anthropic/claude-3.5-sonnet'); // OpenRouter model
            $table->integer('min_word_count')->default(800);
            $table->integer('max_word_count')->default(1500);
            $table->integer('min_reading_time')->default(4); // minutes
            $table->integer('max_reading_time')->default(8);
            
            // Content Rules
            $table->integer('min_headings')->default(3);
            $table->integer('max_headings')->default(6);
            $table->integer('min_paragraphs')->default(5);
            $table->integer('internal_links_count')->default(3); // Anchor links
            $table->boolean('include_featured_image')->default(true);
            $table->boolean('auto_publish')->default(false); // Or save as draft
            
            // Quality Control
            $table->float('duplicate_threshold')->default(0.75); // Similarity score 0-1
            $table->integer('cooldown_days')->default(30); // Days before reusing similar topic
            $table->json('excluded_keywords')->nullable(); // Topics to avoid
            
            // Category Distribution
            $table->json('category_weights'); // {"general": 30, "tips": 40, "regulation": 30}
            
            // Monitoring
            $table->integer('daily_limit')->default(5); // Safety limit
            $table->integer('retry_attempts')->default(3);
            $table->integer('timeout_seconds')->default(120);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_post_configs');
    }
};
