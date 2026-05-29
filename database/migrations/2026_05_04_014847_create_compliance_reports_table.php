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
        Schema::create('compliance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('report_templates');
            $table->foreignId('generated_by')->constrained('clients');
            $table->json('input_data');
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['draft', 'generating', 'ready', 'submitted', 'approved'])->default('draft');
            $table->date('period_start');
            $table->date('period_end');
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index('generated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_reports');
    }
};
