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
        Schema::create('permit_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 50)->unique();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('permit_type_id')->constrained('permit_types')->restrictOnDelete();
            
            // Status Workflow
            $table->enum('status', [
                'draft', 'submitted', 'under_review', 'document_incomplete',
                'quoted', 'quotation_accepted', 'quotation_rejected',
                'payment_pending', 'payment_verified', 'in_progress',
                'completed', 'cancelled'
            ])->default('draft');
            
            // Application Data
            $table->json('form_data')->nullable();
            $table->text('notes')->nullable();
            
            // Admin Review
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            
            // Quotation
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('quotation_expires_at')->nullable();
            $table->text('quotation_notes')->nullable();
            
            // Payment
            $table->decimal('down_payment_amount', 15, 2)->nullable();
            $table->integer('down_payment_percentage')->default(30);
            $table->enum('payment_status', ['pending', 'down_paid', 'partially_paid', 'fully_paid'])->nullable();
            
            // Conversion
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            
            // Timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['client_id', 'status']);
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permit_applications');
    }
};
