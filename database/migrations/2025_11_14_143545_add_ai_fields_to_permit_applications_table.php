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
        Schema::table('permit_applications', function (Blueprint $table) {
            $table->foreignId('ai_recommendation_id')->nullable()->after('kbli_description')
                ->constrained('kbli_permit_recommendations')->onDelete('set null');
            $table->jsonb('business_context')->nullable()->after('ai_recommendation_id')
                ->comment('Business scale, location type, and other context');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            $table->dropForeign(['ai_recommendation_id']);
            $table->dropColumn(['ai_recommendation_id', 'business_context']);
        });
    }
};
