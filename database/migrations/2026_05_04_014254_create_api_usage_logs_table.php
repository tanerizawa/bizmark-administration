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
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_key_id')->constrained('api_keys')->cascadeOnDelete();
            $table->string('endpoint');
            $table->integer('status_code');
            $table->float('latency_ms')->nullable();
            $table->date('date')->index();
            $table->timestamps();

            $table->index(['api_key_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};
