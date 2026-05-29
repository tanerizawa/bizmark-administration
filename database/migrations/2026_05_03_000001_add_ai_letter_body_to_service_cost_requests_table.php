<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_cost_requests', function (Blueprint $table) {
            $table->text('ai_letter_body')->nullable()->after('project_description');
        });
    }

    public function down(): void
    {
        Schema::table('service_cost_requests', function (Blueprint $table) {
            $table->dropColumn('ai_letter_body');
        });
    }
};
