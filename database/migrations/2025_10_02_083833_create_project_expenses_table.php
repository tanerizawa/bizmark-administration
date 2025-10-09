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
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade')->comment('NULL = general expense');
            $table->date('expense_date')->comment('Tanggal pengeluaran');
            $table->enum('category', ['vendor', 'laboratory', 'survey', 'travel', 'operational', 'tax', 'other']);
            $table->string('vendor_name', 255)->nullable()->comment('Nama vendor/penerima');
            $table->decimal('amount', 15, 2)->comment('Nominal pengeluaran');
            $table->enum('payment_method', ['transfer', 'cash', 'check', 'other'])->default('transfer');
            $table->foreignId('bank_account_id')->nullable()->constrained('cash_accounts')->onDelete('set null')->comment('Rekening sumber');
            $table->text('description')->nullable()->comment('Keterangan pengeluaran');
            $table->string('receipt_file', 255)->nullable()->comment('Path file bukti pembayaran');
            $table->boolean('is_billable')->default(true)->comment('Apakah bisa ditagihkan ke klien?');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['project_id', 'expense_date']);
            $table->index('expense_date');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};
