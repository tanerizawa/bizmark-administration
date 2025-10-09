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
        // Change payment_method from ENUM to VARCHAR for flexibility
        DB::statement("ALTER TABLE project_expenses MODIFY payment_method VARCHAR(50) NOT NULL DEFAULT 'transfer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to ENUM
        DB::statement("ALTER TABLE project_expenses MODIFY payment_method ENUM('transfer', 'cash', 'check', 'other') NOT NULL DEFAULT 'transfer'");
    }
};
