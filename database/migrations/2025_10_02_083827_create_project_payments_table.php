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
        Schema::create('project_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->date('payment_date')->comment('Tanggal terima pembayaran');
            $table->decimal('amount', 15, 2)->comment('Nominal pembayaran');
            $table->enum('payment_type', ['dp', 'progress', 'final', 'other'])->default('other');
            $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])->default('transfer');
            $table->foreignId('bank_account_id')->nullable()->constrained('cash_accounts')->onDelete('set null')->comment('Rekening tujuan');
            $table->string('reference_number', 100)->nullable()->comment('Nomor referensi/bukti transfer');
            $table->text('description')->nullable()->comment('Keterangan tambahan');
            $table->string('receipt_file', 255)->nullable()->comment('Path file bukti pembayaran');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['project_id', 'payment_date']);
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_payments');
    }
};
