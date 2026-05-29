<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip pgvector-specific SQL on non-PostgreSQL drivers (e.g. SQLite in tests)
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Enable pgvector (idempotent)
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        // Add 1536-dim embedding column (OpenAI ada-002 / compatible models)
        DB::statement('ALTER TABLE kbli ADD COLUMN IF NOT EXISTS embedding vector(1536)');

        // IVFFlat index for fast cosine similarity — lists=50 for small dataset
        DB::statement(
            'CREATE INDEX IF NOT EXISTS kbli_embedding_idx ON kbli USING ivfflat (embedding vector_cosine_ops) WITH (lists = 50)'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS kbli_embedding_idx');
        DB::statement('ALTER TABLE kbli DROP COLUMN IF EXISTS embedding');
    }
};
