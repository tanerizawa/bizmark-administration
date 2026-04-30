<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_clusters', function (Blueprint $table) {
            $table->id();
            $table->string('seed_keyword');
            $table->string('cluster_name');
            $table->string('search_intent')->default('informational'); // informational, transactional, navigational, commercial
            $table->json('keywords'); // array of keyword variations
            $table->json('long_tail_keywords')->nullable(); // 3-5 word phrases
            $table->string('language', 5)->default('id');
            $table->string('category')->nullable(); // maps to article categories
            $table->string('service_slug')->nullable(); // maps to services_data
            $table->integer('estimated_volume')->default(0);
            $table->integer('difficulty_score')->default(0); // 0-100
            $table->integer('priority')->default(50); // 0-100
            $table->integer('articles_count')->default(0); // how many articles target this cluster
            $table->string('status')->default('active'); // active, archived, saturated
            $table->timestamp('last_researched_at')->nullable();
            $table->timestamps();

            $table->index(['language', 'status']);
            $table->index('service_slug');
            $table->index('search_intent');
            $table->index('priority');
        });

        Schema::create('topic_clusters', function (Blueprint $table) {
            $table->id();
            $table->string('pillar_title'); // e.g. "Panduan Lengkap AMDAL 2026"
            $table->string('pillar_slug')->unique();
            $table->text('pillar_description');
            $table->string('service_slug')->nullable();
            $table->string('language', 5)->default('id');
            $table->json('subtopics'); // array of subtopic titles/slugs
            $table->json('article_ids')->nullable(); // articles mapped to this cluster
            $table->json('keyword_cluster_ids')->nullable(); // linked keyword clusters
            $table->string('status')->default('active');
            $table->integer('internal_links_built')->default(0);
            $table->timestamps();

            $table->index('service_slug');
            $table->index('status');
        });

        Schema::create('content_gaps', function (Blueprint $table) {
            $table->id();
            $table->string('suggested_title');
            $table->string('suggested_slug');
            $table->text('description');
            $table->string('target_keyword');
            $table->string('search_intent')->default('informational');
            $table->string('category')->nullable();
            $table->string('service_slug')->nullable();
            $table->string('language', 5)->default('id');
            $table->integer('priority')->default(50);
            $table->string('status')->default('pending'); // pending, queued, published, dismissed
            $table->foreignId('article_topic_id')->nullable()->constrained('article_topics')->nullOnDelete();
            $table->foreignId('topic_cluster_id')->nullable()->constrained('topic_clusters')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index('service_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_gaps');
        Schema::dropIfExists('topic_clusters');
        Schema::dropIfExists('keyword_clusters');
    }
};
