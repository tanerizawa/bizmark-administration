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
        Schema::create('article_topics', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique(); // "Cara Mengurus IMB Rumah Tinggal"
            $table->string('slug')->unique();
            $table->text('description')->nullable(); // Brief description/outline
            $table->string('category'); // general, news, case-study, tips, regulation
            $table->json('keywords')->nullable(); // SEO keywords array
            $table->json('tags')->nullable(); // Topic tags
            $table->enum('status', ['pending', 'processing', 'published', 'failed', 'archived'])->default('pending');
            $table->integer('priority')->default(0); // Higher = prioritized
            $table->foreignId('article_id')->nullable()->constrained()->onDelete('set null'); // After published
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_for')->nullable(); // When to publish
            $table->text('generation_notes')->nullable(); // AI generation instructions
            $table->integer('views_count')->default(0);
            $table->float('similarity_score')->nullable(); // For duplicate detection
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority']);
            $table->index('category');
            $table->index('scheduled_for');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_topics');
    }
};
