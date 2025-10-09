<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix project_payments table
        DB::statement("ALTER TABLE project_payments MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'check', 'giro', 'other') DEFAULT 'bank_transfer'");
        
        // Fix project_expenses table
        DB::statement("ALTER TABLE project_expenses MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'check', 'giro', 'other') DEFAULT 'bank_transfer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE project_payments MODIFY COLUMN payment_method ENUM('transfer', 'cash', 'check', 'other') DEFAULT 'transfer'");
        DB::statement("ALTER TABLE project_expenses MODIFY COLUMN payment_method ENUM('transfer', 'cash', 'check', 'other') DEFAULT 'transfer'");
    }
};
