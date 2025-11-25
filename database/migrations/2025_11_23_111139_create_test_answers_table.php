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
        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_session_id')->constrained()->onDelete('cascade');
            
            $table->string('question_id', 100)->comment('References question in test_template');
            $table->json('answer_data')->comment('Flexible: multiple choice, text, etc.');
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_earned', 5, 2)->nullable();
            $table->integer('time_spent_seconds')->nullable();
            
            $table->timestamp('answered_at')->useCurrent();
            
            // Indexes
            $table->index('test_session_id');
            $table->index(['test_session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_answers');
    }
};
