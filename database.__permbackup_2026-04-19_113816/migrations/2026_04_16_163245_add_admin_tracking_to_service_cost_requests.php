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
        Schema::table('service_cost_requests', function (Blueprint $table) {
            // Admin review tracking
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('status');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            // Quote timeline
            $table->string('quoted_timeline')->nullable()->after('quoted_at');

            // Completion tracking
            $table->timestamp('completed_at')->nullable()->after('responded_at');
            $table->unsignedBigInteger('completed_by')->nullable()->after('completed_at');

            // Archive tracking
            $table->timestamp('archived_at')->nullable()->after('completed_by');

            // Foreign key constraints
            $table->foreign('reviewed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('completed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_cost_requests', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['completed_by']);
            $table->dropColumn([
                'reviewed_by',
                'reviewed_at',
                'quoted_timeline',
                'completed_at',
                'completed_by',
                'archived_at',
            ]);
        });
    }
};
