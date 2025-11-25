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
        Schema::create('technical_test_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            
            $table->string('test_title');
            $table->text('test_description')->nullable();
            
            // File upload
            $table->string('original_file_path', 500)->nullable()->comment('Template file provided');
            $table->string('submission_file_path', 500)->comment('Candidate submission');
            $table->string('file_type', 50)->nullable()->comment('docx, xlsx, pdf, etc');
            
            // Auto-check results (for formatted documents)
            $table->decimal('format_score', 5, 2)->nullable()->comment('Auto-scoring format compliance');
            $table->json('format_issues')->nullable()->comment('Array of detected issues');
            
            // Manual review
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('review_score', 5, 2)->nullable()->comment('Manual score 0-100');
            $table->text('review_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            
            $table->enum('status', ['submitted', 'under-review', 'reviewed'])->default('submitted');
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('job_application_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_test_submissions');
    }
};
