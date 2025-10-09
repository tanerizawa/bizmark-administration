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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('set null');
            
            // Document Info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->comment('proposal, kontrak, kajian, surat, sk, dll');
            $table->string('document_type')->nullable()->comment('PDF, DOC, XLS, etc');
            
            // File Info
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            
            // Version Control
            $table->string('version', 10)->default('1.0');
            $table->foreignId('parent_document_id')->nullable()->constrained('documents');
            $table->boolean('is_latest_version')->default(true);
            
            // Status
            $table->enum('status', ['draft', 'review', 'approved', 'submitted', 'final'])->default('draft');
            $table->date('document_date')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('approval_date')->nullable();
            
            // Access Control
            $table->foreignId('uploaded_by')->constrained('users');
            $table->boolean('is_confidential')->default(false);
            $table->json('access_permissions')->nullable();
            
            // Additional
            $table->text('notes')->nullable();
            $table->integer('download_count')->default(0);
            $table->datetime('last_accessed_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['project_id', 'category']);
            $table->index(['status', 'is_latest_version']);
            $table->index(['uploaded_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
