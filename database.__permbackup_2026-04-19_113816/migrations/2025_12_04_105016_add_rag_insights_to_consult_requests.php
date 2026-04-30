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
        Schema::table('consult_requests', function (Blueprint $table) {
            // Store RAG insights as JSONB for efficient querying
            $table->jsonb('rag_insights')->nullable()->after('auto_estimate');

            // Store confidence score separately for easy filtering/sorting
            $table->decimal('rag_confidence', 3, 2)->nullable()->after('rag_insights');

            // Index for filtering by confidence
            $table->index('rag_confidence');

            // Add processing timestamp
            $table->timestamp('rag_processed_at')->nullable()->after('rag_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consult_requests', function (Blueprint $table) {
            $table->dropIndex(['rag_confidence']);
            $table->dropColumn(['rag_insights', 'rag_confidence', 'rag_processed_at']);
        });
    }
};
