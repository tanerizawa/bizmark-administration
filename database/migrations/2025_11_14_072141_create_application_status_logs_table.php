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
        Schema::create('application_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('permit_applications')->cascadeOnDelete();
            
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50);
            
            $table->text('notes')->nullable();
            $table->string('changed_by_type', 50)->nullable();
            $table->unsignedBigInteger('changed_by_id')->nullable();
            
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('application_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_status_logs');
    }
};
