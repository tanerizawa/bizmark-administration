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
        Schema::create('ai_setting_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained('ai_settings')->onDelete('cascade');
            $table->string('key')->index();
            $table->text('old_value')->nullable();
            $table->text('new_value');
            $table->string('changed_by_name'); // Store user name for easy display
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->text('reason')->nullable(); // Why was this changed?
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('setting_id');
            $table->index('changed_by');
            $table->index('created_at'); // For time-based queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_setting_history');
    }
};
