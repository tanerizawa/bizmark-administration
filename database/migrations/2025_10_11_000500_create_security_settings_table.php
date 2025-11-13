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
        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('min_password_length')->default(8);
            $table->boolean('require_special_char')->default(true);
            $table->boolean('require_number')->default(true);
            $table->boolean('require_mixed_case')->default(true);
            $table->boolean('enforce_password_expiration')->default(false);
            $table->unsignedSmallInteger('password_expiration_days')->default(90);
            $table->unsignedSmallInteger('session_timeout_minutes')->default(30);
            $table->boolean('allow_concurrent_sessions')->default(true);
            $table->boolean('two_factor_enabled')->default(false);
            $table->boolean('activity_log_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_settings');
    }
};
