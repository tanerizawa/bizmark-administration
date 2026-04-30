<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Daily view snapshots per article (for trend analysis)
        Schema::create('article_view_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('unique_views')->default(0);
            $table->string('top_referrer')->nullable();
            $table->string('top_country', 5)->nullable();
            $table->timestamps();

            $table->unique(['article_id', 'date']);
            $table->index('date');
        });

        // SEO audit scores per article
        Schema::create('seo_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('total_score')->default(0); // 0-100
            $table->json('factors'); // detailed breakdown
            $table->json('recommendations')->nullable();
            $table->timestamp('scored_at');
            $table->timestamps();

            $table->unique('article_id');
        });

        // Periodic SEO reports
        Schema::create('seo_reports', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // weekly, monthly
            $table->date('period_start');
            $table->date('period_end');
            $table->json('metrics'); // aggregated data
            $table->json('top_articles')->nullable();
            $table->json('alerts')->nullable();
            $table->boolean('emailed')->default(false);
            $table->timestamps();

            $table->index(['period', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_reports');
        Schema::dropIfExists('seo_scores');
        Schema::dropIfExists('article_view_logs');
    }
};
