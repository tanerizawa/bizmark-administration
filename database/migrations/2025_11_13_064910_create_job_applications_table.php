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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_vacancy_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            
            // Education
            $table->string('education_level'); // D3, S1, S2, etc
            $table->string('major'); // Jurusan
            $table->string('institution'); // Universitas/Institut
            $table->integer('graduation_year')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            
            // Experience
            $table->text('work_experience')->nullable(); // JSON or text
            $table->boolean('has_experience_ukl_upl')->default(false);
            $table->text('skills')->nullable(); // JSON or text
            
            // Application
            $table->string('cv_path')->nullable(); // File path for CV
            $table->string('portfolio_path')->nullable(); // File path for portfolio
            $table->text('cover_letter')->nullable();
            $table->integer('expected_salary')->nullable();
            $table->date('available_from')->nullable();
            
            // Tracking
            $table->enum('status', ['pending', 'reviewed', 'interview', 'accepted', 'rejected'])->default('pending');
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            
            $table->timestamps();
            
            $table->index('status');
            $table->index('email');
            $table->index(['job_vacancy_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
