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
        Schema::create('test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_template_id')->constrained()->onDelete('cascade');
            
            // Session control
            $table->string('session_token', 64)->unique();
            $table->enum('status', ['pending', 'in-progress', 'completed', 'expired', 'cancelled'])->default('pending');
            
            // Timing
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Results
            $table->decimal('score', 5, 2)->nullable()->comment('0-100 percentage');
            $table->boolean('passed')->default(false);
            $table->integer('time_taken_minutes')->nullable();
            
            // Security & integrity
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('tab_switches')->default(0)->comment('Anti-cheat: detect tab switching');
            
            $table->timestamps();
            
            // Indexes
            $table->index('session_token');
            $table->index(['status', 'expires_at']);
            $table->index(['job_application_id', 'test_template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_sessions');
    }
};
