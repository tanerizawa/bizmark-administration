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
        Schema::create('kbli_permit_recommendations', function (Blueprint $table) {
            $table->id();
            $table->string('kbli_code', 10);
            $table->string('business_scale', 50)->nullable()->comment('micro, small, medium, large');
            $table->string('location_type', 50)->nullable()->comment('urban, rural, industrial_zone');
            
            // AI Generated Data (JSONB for PostgreSQL)
            $table->jsonb('recommended_permits')->comment('Array of permit objects with details');
            $table->jsonb('required_documents')->comment('Documents needed per permit');
            $table->jsonb('risk_assessment')->nullable()->comment('Risk level and considerations');
            $table->jsonb('estimated_timeline')->nullable()->comment('Processing time per permit');
            $table->text('additional_notes')->nullable();
            
            // AI Metadata
            $table->string('ai_model', 100)->nullable();
            $table->string('ai_prompt_hash', 64)->nullable()->comment('Track if prompt changed');
            $table->decimal('confidence_score', 3, 2)->nullable();
            
            // Caching & Performance
            $table->integer('cache_hits')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('kbli_code');
            $table->index('expires_at');
            $table->index(['kbli_code', 'business_scale', 'location_type'], 'kbli_recommendations_unique');
            
            // Foreign key
            $table->foreign('kbli_code')->references('code')->on('kbli')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kbli_permit_recommendations');
    }
};
