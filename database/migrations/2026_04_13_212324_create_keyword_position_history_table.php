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
        Schema::create('keyword_position_history', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->string('our_url')->nullable();
            $table->unsignedTinyInteger('position')->nullable(); // 1-100, null = not ranking
            $table->unsignedTinyInteger('previous_position')->nullable();
            $table->tinyInteger('position_change')->default(0); // positive = improvement, negative = drop
            $table->string('data_source')->default('searxng'); // searxng, google_serp, ai_estimate
            $table->json('top_competitors')->nullable(); // snapshot of top 5
            $table->unsignedInteger('search_volume')->nullable();
            $table->string('search_intent')->nullable(); // informational, commercial, transactional
            $table->date('tracked_at');
            $table->timestamps();
            
            // Indexes for efficient queries
            $table->index('keyword');
            $table->index('position');
            $table->index('tracked_at');
            $table->index(['keyword', 'tracked_at']); // For trend analysis
            $table->index('position_change'); // For alerts (drops)
        });

        // Ranking alerts table for monitoring significant changes
        Schema::create('ranking_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_history_id')->constrained('keyword_position_history')->onDelete('cascade');
            $table->string('keyword');
            $table->string('alert_type'); // ranking_drop, ranking_gain, new_ranking, lost_ranking
            $table->tinyInteger('severity'); // 1=info, 2=warning, 3=critical
            $table->unsignedTinyInteger('old_position')->nullable();
            $table->unsignedTinyInteger('new_position')->nullable();
            $table->string('message');
            $table->json('details')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_actioned')->default(false);
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();
            
            $table->index('alert_type');
            $table->index('severity');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranking_alerts');
        Schema::dropIfExists('keyword_position_history');
    }
};
