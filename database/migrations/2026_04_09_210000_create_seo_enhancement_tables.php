<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Content Refresh Audit Log
        Schema::create('content_refresh_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('status'); // refreshed, no_changes, skipped, error
            $table->json('changes')->nullable(); // ['meta_title', 'content', ...]
            $table->json('before_snapshot')->nullable(); // snapshot of old values
            $table->json('after_snapshot')->nullable(); // snapshot of new values
            $table->text('error_message')->nullable();
            $table->string('triggered_by')->default('cron'); // cron, manual, auto
            $table->integer('ai_tokens_used')->default(0);
            $table->timestamps();

            $table->index(['article_id', 'created_at']);
            $table->index('status');
        });

        // Competitive Intelligence
        Schema::create('competitor_analyses', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('our_url')->nullable();
            $table->integer('our_position')->nullable();
            $table->json('top_competitors')->nullable(); // [{url, title, position, domain}]
            $table->json('content_gaps')->nullable(); // topics competitors cover that we don't
            $table->json('recommendations')->nullable(); // AI suggestions
            $table->integer('search_volume')->nullable();
            $table->string('difficulty')->nullable(); // easy, medium, hard
            $table->date('analyzed_at');
            $table->timestamps();

            $table->index('keyword');
            $table->index('analyzed_at');
        });

        // Meta A/B Testing
        Schema::create('meta_ab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('test_type'); // title, description, both
            $table->string('variant_a_title')->nullable();
            $table->string('variant_a_description')->nullable();
            $table->string('variant_b_title')->nullable();
            $table->string('variant_b_description')->nullable();
            $table->integer('variant_a_impressions')->default(0);
            $table->integer('variant_a_clicks')->default(0);
            $table->integer('variant_b_impressions')->default(0);
            $table->integer('variant_b_clicks')->default(0);
            $table->string('winner')->nullable(); // a, b, inconclusive
            $table->decimal('confidence', 5, 2)->nullable(); // statistical confidence %
            $table->string('status')->default('running'); // running, completed, cancelled
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'status']);
        });

        // Google Search Console Data Cache
        Schema::create('search_console_data', function (Blueprint $table) {
            $table->id();
            $table->string('page_url');
            $table->string('query');
            $table->date('date');
            $table->integer('clicks')->default(0);
            $table->integer('impressions')->default(0);
            $table->decimal('ctr', 5, 2)->default(0);
            $table->decimal('position', 5, 1)->default(0);
            $table->timestamps();

            $table->unique(['page_url', 'query', 'date']);
            $table->index(['page_url', 'date']);
            $table->index(['query', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_console_data');
        Schema::dropIfExists('meta_ab_tests');
        Schema::dropIfExists('competitor_analyses');
        Schema::dropIfExists('content_refresh_logs');
    }
};
