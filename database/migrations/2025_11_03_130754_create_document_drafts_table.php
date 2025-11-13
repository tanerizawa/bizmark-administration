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
        Schema::create('document_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('document_templates')->onDelete('cascade');
            $table->foreignId('ai_log_id')->constrained('ai_processing_logs')->onDelete('cascade');
            $table->string('title');
            $table->longText('content'); // Hasil parafrase penuh
            $table->json('sections')->nullable(); // [{section: "BAB I", content: "...", page: 1}, ...]
            $table->enum('status', ['draft', 'reviewed', 'approved', 'rejected', 'exported'])->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('status');
            $table->index('project_id');
            $table->index(['project_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_drafts');
    }
};
