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
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->index()->comment('global, pricing, rag, prompts, etc');
            $table->string('key', 100)->unique()->comment('Unique setting key');
            $table->text('value')->nullable()->comment('Setting value (JSON for complex types)');
            $table->string('data_type', 20)->default('string')->comment('string, number, boolean, json, array');
            $table->text('description')->nullable()->comment('Human-readable description');
            $table->boolean('is_public')->default(false)->comment('Can be exposed to frontend');
            $table->boolean('is_encrypted')->default(false)->comment('Encrypt sensitive data like API keys');
            $table->json('validation_rules')->nullable()->comment('Laravel validation rules');
            $table->text('default_value')->nullable()->comment('Default value if not set');
            $table->integer('display_order')->default(0)->comment('Order in UI');
            $table->string('group_name', 100)->nullable()->index()->comment('UI grouping');
            $table->boolean('requires_restart')->default(false)->comment('Requires system restart');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};
