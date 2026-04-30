<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kbli')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            Schema::table('kbli', function (Blueprint $table) {
                $table->string('category')->nullable()->after('sector')->index();
                $table->text('activities')->nullable()->after('category');
                $table->text('examples')->nullable()->after('activities');

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

                $table->boolean('is_active')->default(true)->after('recommended_services');
                $table->integer('usage_count')->default(0)->after('is_active');
                $table->softDeletes()->after('updated_at');

                $table->index('is_active');
                $table->index('usage_count');
                $table->index('complexity_level');
            });

            DB::statement("
                CREATE INDEX IF NOT EXISTS kbli_search_idx ON kbli
                USING gin(to_tsvector('indonesian',
                    code || ' ' ||
                    description || ' ' ||
                    COALESCE(activities, '') || ' ' ||
                    COALESCE(category, '')
                ))
            ");

            return;
        }

        $hasActivities = Schema::hasColumn('kbli', 'activities');
        $hasExamples = Schema::hasColumn('kbli', 'examples');
        $hasComplexityLevel = Schema::hasColumn('kbli', 'complexity_level');
        $hasIsActive = Schema::hasColumn('kbli', 'is_active');
        $hasUsageCount = Schema::hasColumn('kbli', 'usage_count');
        $hasDeletedAt = Schema::hasColumn('kbli', 'deleted_at');

        Schema::table('kbli', function (Blueprint $table) use (
            $hasActivities,
            $hasExamples,
            $hasComplexityLevel,
            $hasIsActive,
            $hasUsageCount,
            $hasDeletedAt,
        ) {
            if (! $hasActivities) {
                $table->text('activities')->nullable();
            }

            if (! $hasExamples) {
                $table->text('examples')->nullable();
            }

            if (! $hasComplexityLevel) {
                $table->string('complexity_level', 50)->default('medium');
            }

            if (! $hasIsActive) {
                $table->boolean('is_active')->default(true);
            }

            if (! $hasUsageCount) {
                $table->integer('usage_count')->default(0);
            }

            if (! $hasDeletedAt) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'pgsql') {
            return;
        }

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
