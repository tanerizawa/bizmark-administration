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
            $table->string('kbli_code', 10)->nullable()->after('company_name');
            $table->text('kbli_description')->nullable()->after('kbli_code');
            $table->string('kbli_category')->nullable()->after('kbli_description')->comment('Kategori risiko: Rendah, Menengah Rendah, Menengah Tinggi, Tinggi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_applications', function (Blueprint $table) {
            $table->dropColumn(['kbli_code', 'kbli_description', 'kbli_category']);
        });
    }
};
