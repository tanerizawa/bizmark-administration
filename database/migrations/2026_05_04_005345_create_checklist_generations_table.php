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
        Schema::create('checklist_generations', function (Blueprint $table) {
            $table->id();
            $table->string('kbli_code', 10)->index();
            $table->string('permit_type', 100);
            $table->string('city', 100);
            $table->enum('business_scale', ['mikro', 'kecil', 'menengah', 'besar'])->default('kecil');
            $table->json('checklist_data');
            $table->string('pdf_path', 500)->nullable();
            $table->string('requester_email', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['kbli_code', 'permit_type', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_generations');
    }
};
