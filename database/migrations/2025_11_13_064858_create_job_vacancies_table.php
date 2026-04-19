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
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Job title
            $table->string('slug')->unique();
            $table->string('position'); // e.g. "Drafter Dokumen Lingkungan & Teknis"
            $table->text('description'); // Full job description
            $table->text('responsibilities'); // Job duties (JSON or text)
            $table->text('qualifications'); // Requirements (JSON or text)
            $table->text('benefits')->nullable(); // Benefits (JSON or text)
            $table->string('employment_type')->default('full-time'); // full-time, part-time, contract, internship
            $table->string('location')->default('Jakarta'); // Work location
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->boolean('salary_negotiable')->default(true);
            $table->date('deadline')->nullable(); // Application deadline
            $table->enum('status', ['open', 'closed', 'draft'])->default('open');
            $table->string('google_form_url')->nullable(); // Google Form URL sebagai backup
            $table->integer('applications_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};
