<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitor_analyses', function (Blueprint $table) {
            $table->string('data_source', 20)->default('ai_estimated')->after('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('competitor_analyses', function (Blueprint $table) {
            $table->dropColumn('data_source');
        });
    }
};
