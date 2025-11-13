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
        Schema::create('compliance_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_id')->constrained('document_drafts')->onDelete('cascade');
            $table->string('document_type')->default('UKL-UPL'); // UKL-UPL, AMDAL, PERTEK, etc
            $table->decimal('overall_score', 5, 2)->default(0); // 0-100
            
            // Detailed scores per category
            $table->decimal('structure_score', 5, 2)->default(0);
            $table->decimal('compliance_score', 5, 2)->default(0);
            $table->decimal('formatting_score', 5, 2)->default(0);
            $table->decimal('completeness_score', 5, 2)->default(0);
            
            // Issues found (JSONB array)
            $table->jsonb('issues')->nullable(); // [{category, severity, message, location, suggestion}]
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, checking, completed, failed
            $table->text('error_message')->nullable();
            
            // Metadata
            $table->integer('total_issues')->default(0);
            $table->integer('critical_issues')->default(0);
            $table->integer('warning_issues')->default(0);
            $table->integer('info_issues')->default(0);
            
            // Timestamps
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('draft_id');
            $table->index('status');
            $table->index('overall_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_checks');
    }
};
