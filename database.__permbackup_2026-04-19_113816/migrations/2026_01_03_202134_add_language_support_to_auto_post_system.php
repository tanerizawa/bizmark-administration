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
        // Add language to article_topics
        Schema::table('article_topics', function (Blueprint $table) {
            $table->enum('language', ['id', 'en'])->default('id')->after('category');
            $table->string('target_market')->default('local')->after('language'); // local, pma, both

            // Update index to include language
            $table->index(['status', 'language', 'priority']);
        });

        // Add language settings to auto_post_configs
        Schema::table('auto_post_configs', function (Blueprint $table) {
            $table->json('language_distribution')->nullable()->after('category_weights'); // {"id": 60, "en": 40}
            $table->json('market_focus')->nullable()->after('language_distribution'); // {"local": true, "pma": true}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_topics', function (Blueprint $table) {
            $table->dropIndex(['status', 'language', 'priority']);
            $table->dropColumn(['language', 'target_market']);
        });

        Schema::table('auto_post_configs', function (Blueprint $table) {
            $table->dropColumn(['language_distribution', 'market_focus']);
        });
    }
};
