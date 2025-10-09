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
        Schema::create('project_permits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permit_type_id')->nullable()->constrained('permit_types')->nullOnDelete();
            $table->string('custom_permit_name', 100)->nullable();
            $table->string('custom_institution_name', 100)->nullable();
            $table->integer('sequence_order')->default(0);
            $table->boolean('is_goal_permit')->default(false);
            $table->enum('status', [
                'NOT_STARTED',
                'IN_PROGRESS',
                'WAITING_DOC',
                'SUBMITTED',
                'UNDER_REVIEW',
                'APPROVED',
                'REJECTED',
                'EXISTING',
                'CANCELLED'
            ])->default('NOT_STARTED');
            $table->boolean('has_existing_document')->default(false);
            $table->foreignId('existing_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('started_at')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->datetime('rejected_at')->nullable();
            $table->date('target_date')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->decimal('actual_cost', 15, 2)->nullable();
            $table->string('permit_number', 100)->nullable();
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'sequence_order']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_permits');
    }
};
