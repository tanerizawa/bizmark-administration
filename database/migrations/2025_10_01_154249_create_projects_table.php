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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Kode project unik');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Client Information
            $table->string('client_name');
            $table->string('client_company')->nullable();
            $table->string('client_industry_category')->nullable();
            $table->text('client_address')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_email')->nullable();
            
            // Project Details
            $table->string('permit_type')->comment('UKL-UPL, AMDAL, Andalalin, dll');
            $table->json('sub_permits')->nullable()->comment('Sub izin terkait');
            $table->text('project_location')->nullable();
            $table->decimal('project_value', 15, 2)->nullable();
            
            // Timeline
            $table->date('contract_date')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            
            // Status & Assignment
            $table->foreignId('current_status_id')->constrained('project_statuses');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users');
            $table->foreignId('primary_institution_id')->nullable()->constrained('institutions');
            
            // Additional Info
            $table->text('notes')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_archived')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['permit_type', 'current_status_id']);
            $table->index(['client_name', 'client_company']);
            $table->index(['assigned_user_id', 'current_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
