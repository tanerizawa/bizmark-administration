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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number', 50)->unique();
            
            // Polymorphic Relations
            $table->string('payable_type', 50);
            $table->unsignedBigInteger('payable_id');
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            
            // Payment Details
            $table->decimal('amount', 15, 2);
            $table->enum('payment_type', ['down_payment', 'installment', 'final_payment'])->nullable();
            $table->enum('payment_method', ['bank_transfer', 'ewallet', 'credit_card', 'virtual_account', 'manual'])->nullable();
            
            // Gateway Integration
            $table->string('gateway_provider', 50)->nullable();
            $table->string('gateway_transaction_id', 255)->nullable();
            $table->json('gateway_response')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'refunded'])->default('pending');
            
            // Bank Transfer Details (Manual)
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('account_holder', 255)->nullable();
            $table->string('transfer_proof_path', 500)->nullable();
            
            // Verification
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['payable_type', 'payable_id']);
            $table->index('client_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
