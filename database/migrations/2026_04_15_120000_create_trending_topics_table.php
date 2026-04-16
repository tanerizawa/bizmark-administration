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
        Schema::create('trending_topics', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('category')->default('general'); // umkm, perizinan, legal, marketing, technology
            $table->string('language')->default('id');
            $table->string('data_source')->default('searxng'); // searxng, google_trends, ai_analysis
            $table->integer('trend_score')->default(0); // 0-100, higher = more trending
            $table->integer('search_volume')->nullable(); // estimated monthly searches
            $table->json('related_keywords')->nullable(); // related terms found
            $table->json('top_sources')->nullable(); // news sources that mentioned this
            $table->text('sample_headline')->nullable(); // sample headline for context
            $table->boolean('is_processed')->default(false); // article generated?
            $table->foreignId('article_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('discovered_at');
            $table->timestamp('expires_at')->nullable(); // when topic is no longer trending
            $table->timestamps();

            $table->index(['category', 'is_processed']);
            $table->index(['trend_score', 'is_processed']);
            $table->index('discovered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trending_topics');
    }
};
