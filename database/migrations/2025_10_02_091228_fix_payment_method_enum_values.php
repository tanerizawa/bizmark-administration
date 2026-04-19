<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL: Drop and recreate column with new enum values
        Schema::table('project_payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('project_payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'giro', 'other'])
                  ->default('bank_transfer')->after('amount');
        });
        
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'giro', 'other'])
                  ->default('bank_transfer')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('project_payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('project_payments', function (Blueprint $table) {
            $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])
                  ->default('transfer')->after('amount');
        });
        
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
        Schema::table('project_expenses', function (Blueprint $table) {
            $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])
                  ->default('transfer')->after('category');
        });
    }
};
