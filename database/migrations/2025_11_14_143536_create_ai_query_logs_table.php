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
        Schema::create('ai_query_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->string('kbli_code', 10)->nullable();
            $table->jsonb('business_context')->nullable()->comment('Additional context provided by user');
            
            // Request/Response
            $table->text('prompt_text')->nullable();
            $table->text('response_text')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->integer('response_time_ms')->nullable()->comment('Response time in milliseconds');
            
            // Status
            $table->string('status', 20)->default('success')->comment('success, error, timeout');
            $table->text('error_message')->nullable();
            
            // Metadata
            $table->string('ai_model', 100)->nullable();
            $table->decimal('api_cost', 10, 6)->nullable()->comment('Cost in USD');
            
            $table->timestamp('created_at')->useCurrent();
            
            // Indexes
            $table->index('client_id');
            $table->index('kbli_code');
            $table->index('created_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_query_logs');
    }
};
