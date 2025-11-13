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
        Schema::create('bank_statement_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained('bank_reconciliations')->onDelete('cascade');
            
            // Transaction Details from Bank
            $table->date('transaction_date');
            $table->text('description');
            $table->decimal('debit_amount', 15, 2)->default(0); // Keluar
            $table->decimal('credit_amount', 15, 2)->default(0); // Masuk
            $table->decimal('running_balance', 15, 2);
            $table->string('reference_number', 100)->nullable();
            
            // Matching Status
            $table->boolean('is_matched')->default(false);
            $table->enum('matched_transaction_type', ['payment', 'expense', 'invoice_payment'])->nullable();
            $table->unsignedBigInteger('matched_transaction_id')->nullable();
            $table->enum('match_confidence', ['exact', 'fuzzy', 'manual'])->nullable();
            $table->text('match_notes')->nullable();
            
            // If unmatched, why?
            $table->enum('unmatch_reason', ['missing_in_system', 'bank_error', 'timing_difference', 'needs_investigation'])->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('reconciliation_id');
            $table->index('transaction_date');
            $table->index('is_matched');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_entries');
    }
};
