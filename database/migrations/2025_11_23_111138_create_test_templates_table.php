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
        Schema::create('test_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('test_type', ['psychology', 'psychometric', 'technical', 'aptitude', 'personality']);
            $table->text('description')->nullable();
            $table->integer('duration_minutes');
            $table->integer('passing_score')->comment('Percentage (0-100)');
            
            // Test configuration
            $table->json('questions_data')->nullable()->comment('Question bank with answers');
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            // Indexes
            $table->index(['test_type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_templates');
    }
};
