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
        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name', 100)->comment('Nama akun (Bank BTN, Cash, dll)');
            $table->enum('account_type', ['bank', 'cash', 'receivable', 'payable'])->default('bank');
            $table->string('account_number', 50)->nullable()->comment('Nomor rekening');
            $table->string('bank_name', 100)->nullable()->comment('Nama bank');
            $table->string('account_holder', 255)->nullable()->comment('Nama pemilik rekening');
            $table->decimal('current_balance', 15, 2)->default(0)->comment('Saldo saat ini');
            $table->decimal('initial_balance', 15, 2)->default(0)->comment('Saldo awal');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['account_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_accounts');
    }
};
