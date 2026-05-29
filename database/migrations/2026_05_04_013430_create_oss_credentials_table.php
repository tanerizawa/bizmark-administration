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
        Schema::create('oss_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->text('oss_username_encrypted');
            $table->text('oss_password_encrypted');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('client_id'); // one credential set per client
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oss_credentials');
    }
};
