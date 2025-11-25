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
        // Drop the ENUM constraint from test_type column
        DB::statement("ALTER TABLE test_templates DROP CONSTRAINT IF EXISTS test_templates_test_type_check");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the original ENUM constraint
        DB::statement("ALTER TABLE test_templates ADD CONSTRAINT test_templates_test_type_check CHECK (test_type::text = ANY (ARRAY['psychology'::character varying, 'psychometric'::character varying, 'technical'::character varying, 'aptitude'::character varying, 'personality'::character varying]::text[]))");
    }
};
