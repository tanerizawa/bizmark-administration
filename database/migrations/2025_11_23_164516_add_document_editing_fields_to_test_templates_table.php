<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_templates', function (Blueprint $table) {
            // Add new columns
            $table->string('template_file_path', 500)->nullable()->after('instructions')->comment('Path to template document file');
            $table->json('evaluation_criteria')->nullable()->after('template_file_path')->comment('Checklist/rubrik penilaian untuk document editing');
        });
        
        // Change column type for PostgreSQL
        DB::statement("ALTER TABLE test_templates ALTER COLUMN test_type TYPE VARCHAR(50)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_templates', function (Blueprint $table) {
            $table->dropColumn(['template_file_path', 'evaluation_criteria']);
        });
    }
};
