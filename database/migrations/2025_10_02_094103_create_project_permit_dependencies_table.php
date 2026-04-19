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
        Schema::create('project_permit_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_permit_id')->constrained('project_permits')->cascadeOnDelete();
            $table->foreignId('depends_on_permit_id')->constrained('project_permits')->cascadeOnDelete();
            $table->enum('dependency_type', ['MANDATORY', 'OPTIONAL'])->default('MANDATORY');
            $table->boolean('can_proceed_without')->default(false);
            $table->text('override_reason')->nullable();
            $table->string('override_document_path')->nullable();
            $table->foreignId('overridden_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('overridden_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('project_permit_id');
            $table->unique(['project_permit_id', 'depends_on_permit_id'], 'project_permit_dep_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_permit_dependencies');
    }
};
