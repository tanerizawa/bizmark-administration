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
        // Change category column from ENUM to VARCHAR for more flexibility
        DB::statement("ALTER TABLE project_expenses MODIFY COLUMN category VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old enum values
        DB::statement("ALTER TABLE project_expenses MODIFY COLUMN category ENUM('vendor','laboratory','survey','travel','operational','tax','other') NOT NULL");
    }
};
