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
        Schema::create('article_topic_similarities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_a_id')->constrained('article_topics')->onDelete('cascade');
            $table->foreignId('topic_b_id')->constrained('article_topics')->onDelete('cascade');
            $table->float('similarity_score'); // 0-1, calculated by AI
            $table->timestamp('calculated_at');

            $table->unique(['topic_a_id', 'topic_b_id']);
            $table->index('similarity_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_topic_similarities');
    }
};
