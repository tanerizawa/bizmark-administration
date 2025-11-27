<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kblis', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->index();
            $table->string('title');
            $table->string('category')->index();
            $table->text('description')->nullable();
            $table->text('activities')->nullable();
            $table->text('examples')->nullable();
            
            // Pricing & estimation fields
            $table->enum('complexity_level', ['low', 'medium', 'high'])->default('medium');
            $table->jsonb('default_direct_costs')->nullable()->comment('e.g., {"printing": 200000, "lab_tests": 0}');
            $table->jsonb('default_hours_estimate')->nullable()->comment('e.g., {"admin": 2, "technical": 16, "review": 4}');
            $table->jsonb('default_hourly_rates')->nullable()->comment('e.g., {"admin": 100000, "technical": 200000, "review": 150000}');
            $table->jsonb('regulatory_flags')->nullable()->comment('e.g., ["requires_permit", "environmental_assessment"]');
            $table->jsonb('recommended_services')->nullable()->comment('Array of recommended services');
            
            // Business metadata
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for search
            $table->index('is_active');
            $table->index('usage_count');
        });
        
        // Add full-text search index for PostgreSQL
        DB::statement('CREATE INDEX kblis_search_idx ON kblis USING gin(to_tsvector(\'indonesian\', title || \' \' || COALESCE(description, \'\') || \' \' || COALESCE(activities, \'\')))');
    }

    public function down(): void
    {
        Schema::dropIfExists('kblis');
    }
};
