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
        Schema::create('kblis', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->index();
            $table->string('title');
            $table->string('category')->index(); // High-level sector
            $table->text('description')->nullable();
            $table->text('activities')->nullable(); // Typical activities examples
            $table->enum('complexity_level', ['low', 'medium', 'high'])->default('medium')->index();
            
            // Cost & pricing defaults (JSON)
            $table->json('default_direct_costs')->nullable(); // {printing, lab_tests, permit_fees, travel, etc}
            $table->json('default_hours_estimate')->nullable(); // {admin, technical, review, legal}
            $table->json('default_hourly_rates')->nullable(); // {admin, technical, review, legal}
            
            // Regulatory & compliance
            $table->json('regulatory_flags')->nullable(); // [requires_permit, environmental_assessment, etc]
            $table->json('recommended_services')->nullable(); // [{code, name, description, typical_cost_range}]
            
            // Metadata
            $table->text('examples')->nullable(); // Real-world business examples
            $table->text('notes')->nullable(); // Admin notes
            $table->boolean('is_active')->default(true)->index();
            $table->integer('usage_count')->default(0); // Track popular KBLIs
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kblis');
    }
};
