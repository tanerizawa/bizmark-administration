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
        Schema::table('permit_types', function (Blueprint $table) {
            $table->integer('typical_duration_days')->nullable()->after('description');
            $table->integer('min_duration_days')->nullable()->after('typical_duration_days');
            $table->integer('max_duration_days')->nullable()->after('min_duration_days');
            $table->json('can_parallel_with')->nullable()->after('max_duration_days');
            $table->json('requires_before')->nullable()->after('can_parallel_with');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_types', function (Blueprint $table) {
            $table->dropColumn(['typical_duration_days', 'min_duration_days', 'max_duration_days', 'can_parallel_with', 'requires_before']);
        });
    }
};
