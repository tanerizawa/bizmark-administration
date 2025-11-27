<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Store free consultation requests from public with AI-generated estimates
     */
    public function up(): void
    {
        Schema::create('consult_requests', function (Blueprint $table) {
            $table->id();
            
            // Contact information
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('company_name')->nullable();
            
            // Business information
            $table->string('kbli_code', 10); // 5-digit KBLI code
            $table->foreign('kbli_code')->references('code')->on('kbli')->onDelete('restrict');
            
            $table->enum('business_size', ['micro', 'small', 'medium', 'large'])->default('small');
            $table->string('location')->nullable(); // Province/city
            $table->enum('location_type', ['industrial', 'commercial', 'residential', 'rural'])->default('commercial');
            $table->enum('investment_level', ['under_100m', '100m_500m', '500m_2b', 'over_2b'])->default('under_100m');
            $table->integer('employee_count')->default(0);
            
            // Project details
            $table->text('project_description');
            $table->jsonb('deliverables_requested')->nullable(); // Array of requested services
            
            // Estimate information
            $table->enum('estimate_status', ['pending', 'auto_estimated', 'reviewed', 'approved', 'rejected'])->default('auto_estimated');
            $table->jsonb('auto_estimate')->nullable(); // Full AI-generated estimate
            $table->jsonb('final_quote')->nullable(); // Admin-adjusted quote if reviewed
            $table->decimal('confidence_score', 3, 2)->nullable(); // 0.00 - 1.00
            
            // Admin review
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            
            // Follow-up
            $table->boolean('contacted')->default(false);
            $table->timestamp('contacted_at')->nullable();
            $table->boolean('converted_to_client')->default(false);
            $table->unsignedBigInteger('client_id')->nullable(); // If converted
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer_url')->nullable();
            $table->jsonb('utm_params')->nullable(); // UTM tracking
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('phone');
            $table->index('kbli_code');
            $table->index('estimate_status');
            $table->index('business_size');
            $table->index('contacted');
            $table->index('converted_to_client');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consult_requests');
    }
};
