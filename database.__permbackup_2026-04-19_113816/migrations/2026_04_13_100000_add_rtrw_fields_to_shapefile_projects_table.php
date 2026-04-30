<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shapefile_projects', function (Blueprint $table) {
            $table->string('rtrw_zona', 200)->nullable()->after('metadata');
            $table->string('rtrw_perda', 100)->nullable()->after('rtrw_zona');
            $table->text('rtrw_remark')->nullable()->after('rtrw_perda');
            $table->json('rtrw_raw')->nullable()->after('rtrw_remark');
        });
    }

    public function down(): void
    {
        Schema::table('shapefile_projects', function (Blueprint $table) {
            $table->dropColumn(['rtrw_zona', 'rtrw_perda', 'rtrw_remark', 'rtrw_raw']);
        });
    }
};
