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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('key', 64)->unique();
            $table->string('name');
            $table->enum('plan', ['free', 'starter', 'pro', 'enterprise'])->default('free');
            $table->json('allowed_endpoints')->nullable();
            $table->integer('monthly_limit')->default(100);
            $table->integer('usage_this_month')->default(0);
            $table->timestamp('usage_reset_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
