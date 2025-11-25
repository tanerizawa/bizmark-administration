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
        Schema::create('cash_account_balance_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')
                  ->constrained('cash_accounts')
                  ->onDelete('cascade')
                  ->comment('Referensi ke tabel cash_accounts');
            
            $table->decimal('old_balance', 15, 2)->comment('Saldo sebelum perubahan');
            $table->decimal('new_balance', 15, 2)->comment('Saldo setelah perubahan');
            $table->decimal('change_amount', 15, 2)->comment('Selisih perubahan (+ untuk income, - untuk expense)');
            
            $table->string('change_type', 50)->comment('Tipe perubahan: income, expense, adjustment, reconciliation, recalculation');
            $table->bigInteger('reference_id')->nullable()->comment('ID transaksi terkait');
            $table->string('reference_type', 100)->nullable()->comment('Model class: ProjectPayment, ProjectExpense, etc');
            $table->text('description')->nullable()->comment('Deskripsi perubahan');
            
            $table->foreignId('changed_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User yang melakukan perubahan');
            
            $table->timestamp('changed_at')->useCurrent()->comment('Waktu perubahan terjadi');
            
            // Indexes untuk query performance
            $table->index('cash_account_id');
            $table->index('changed_at');
            $table->index(['reference_type', 'reference_id']);
            $table->index('change_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_account_balance_history');
    }
};
