<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->foreignId('topic_cluster_id')->nullable()->after('source_type')
                ->constrained('topic_clusters')->nullOnDelete();
        });

        Schema::table('article_topics', function (Blueprint $table) {
            $table->foreignId('topic_cluster_id')->nullable()->after('article_id')
                ->constrained('topic_clusters')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('article_topics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('topic_cluster_id');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('topic_cluster_id');
        });
    }
};
