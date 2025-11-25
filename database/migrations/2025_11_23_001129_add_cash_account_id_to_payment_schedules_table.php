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
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->foreignId('cash_account_id')
                  ->nullable()
                  ->after('paid_date')
                  ->constrained('cash_accounts')
                  ->nullOnDelete()
                  ->comment('Akun kas/bank yang menerima pembayaran');
            
            $table->index('cash_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_schedules', function (Blueprint $table) {
            $table->dropForeign(['cash_account_id']);
            $table->dropColumn('cash_account_id');
        });
    }
};
