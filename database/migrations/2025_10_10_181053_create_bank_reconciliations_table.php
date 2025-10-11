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
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->onDelete('cascade');
            
            // Period
            $table->date('reconciliation_date');
            $table->date('start_date');
            $table->date('end_date');
            
            // Balances
            $table->decimal('opening_balance_book', 15, 2);
            $table->decimal('opening_balance_bank', 15, 2);
            $table->decimal('closing_balance_book', 15, 2);
            $table->decimal('closing_balance_bank', 15, 2);
            
            // Adjustments
            $table->decimal('total_deposits_in_transit', 15, 2)->default(0);
            $table->decimal('total_outstanding_checks', 15, 2)->default(0);
            $table->decimal('total_bank_charges', 15, 2)->default(0);
            $table->decimal('total_bank_credits', 15, 2)->default(0);
            
            // Results
            $table->decimal('adjusted_bank_balance', 15, 2);
            $table->decimal('adjusted_book_balance', 15, 2);
            $table->decimal('difference', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['in_progress', 'completed', 'reviewed', 'approved'])->default('in_progress');
            
            // Bank Statement
            $table->string('bank_statement_file')->nullable();
            $table->string('bank_statement_format', 50)->nullable();
            
            // Audit
            $table->text('notes')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['cash_account_id', 'reconciliation_date']);
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
