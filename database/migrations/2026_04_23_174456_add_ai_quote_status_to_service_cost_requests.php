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
            $table->string('ai_quote_status', 20)->nullable()->default(null)->after('quote_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_cost_requests', function (Blueprint $table) {
            $table->dropColumn('ai_quote_status');
        });
    }
};
