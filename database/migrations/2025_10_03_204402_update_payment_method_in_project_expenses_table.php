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
        // PostgreSQL: Change payment_method from ENUM to VARCHAR
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->string('payment_method', 50)->default('transfer')->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])->default('transfer')->after('category');
        });
    }
};
