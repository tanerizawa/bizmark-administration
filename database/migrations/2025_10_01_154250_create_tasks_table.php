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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('sop_notes')->nullable()->comment('SOP atau checklist tugas');
            
            // Assignment & Dates
            $table->foreignId('assigned_user_id')->nullable()->constrained('users');
            $table->date('due_date')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            
            // Status
            $table->enum('status', ['todo', 'in_progress', 'done', 'blocked'])->default('todo');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Relations
            $table->foreignId('institution_id')->nullable()->constrained('institutions');
            $table->foreignId('depends_on_task_id')->nullable()->constrained('tasks');
            
            // Additional
            $table->text('completion_notes')->nullable();
            $table->integer('estimated_hours')->nullable();
            $table->integer('actual_hours')->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['project_id', 'status']);
            $table->index(['assigned_user_id', 'status']);
            $table->index(['due_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
