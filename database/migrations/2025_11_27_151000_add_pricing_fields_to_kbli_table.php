<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kbli', function (Blueprint $table) {
            // Add category for better grouping (separate from sector)
            $table->string('category')->nullable()->after('sector')->index();
            $table->text('activities')->nullable()->after('category');
            $table->text('examples')->nullable()->after('activities');
            
            // Pricing & estimation fields
            $table->enum('complexity_level', ['low', 'medium', 'high'])->default('medium')->after('examples');
            $table->jsonb('default_direct_costs')->nullable()->after('complexity_level')
                ->comment('Direct costs: {"printing": 200000, "lab_tests": 0, "permits": 500000}');
            $table->jsonb('default_hours_estimate')->nullable()->after('default_direct_costs')
                ->comment('Hours by role: {"admin": 2, "technical": 16, "review": 4, "field": 8}');
            $table->jsonb('default_hourly_rates')->nullable()->after('default_hours_estimate')
                ->comment('Rates by role: {"admin": 100000, "technical": 200000, "review": 150000, "field": 175000}');
            $table->jsonb('regulatory_flags')->nullable()->after('default_hourly_rates')
                ->comment('Flags: ["requires_permit", "environmental_assessment", "amdal_required"]');
            $table->jsonb('recommended_services')->nullable()->after('regulatory_flags')
                ->comment('Services: [{"name": "UKL/UPL", "priority": "required"}, ...]');
            
            // Business metadata
            $table->boolean('is_active')->default(true)->after('recommended_services');
            $table->integer('usage_count')->default(0)->after('is_active');
            $table->softDeletes()->after('updated_at');
            
            // Indexes
            $table->index('is_active');
            $table->index('usage_count');
            $table->index('complexity_level');
        });
        
        // Add full-text search index for better search performance
        DB::statement("
            CREATE INDEX IF NOT EXISTS kbli_search_idx ON kbli 
            USING gin(to_tsvector('indonesian', 
                code || ' ' || 
                description || ' ' || 
                COALESCE(activities, '') || ' ' || 
                COALESCE(category, '')
            ))
        ");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS kbli_search_idx');
        
        Schema::table('kbli', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['usage_count']);
            $table->dropIndex(['complexity_level']);
            $table->dropIndex(['category']);
            
            $table->dropColumn([
                'category',
                'activities',
                'examples',
                'complexity_level',
                'default_direct_costs',
                'default_hours_estimate',
                'default_hourly_rates',
                'regulatory_flags',
                'recommended_services',
                'is_active',
                'usage_count',
                'deleted_at',
            ]);
        });
    }
};
