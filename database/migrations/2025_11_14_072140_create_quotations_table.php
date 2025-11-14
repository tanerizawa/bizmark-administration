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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 50)->unique();
            $table->foreignId('application_id')->constrained('permit_applications')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            
            // Pricing Breakdown
            $table->decimal('base_price', 15, 2);
            $table->json('additional_fees')->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(11);
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('total_amount', 15, 2);
            
            // Payment Terms
            $table->integer('down_payment_percentage')->default(30);
            $table->decimal('down_payment_amount', 15, 2)->nullable();
            $table->text('payment_terms')->nullable();
            
            // Validity
            $table->timestamp('valid_until');
            $table->text('terms_and_conditions')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['application_id', 'status']);
            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
