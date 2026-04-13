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
        Schema::create('shapefile_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->json('geojson');
            $table->decimal('area_m2', 15, 2)->default(0);
            $table->decimal('area_ha', 12, 6)->default(0);
            $table->decimal('perimeter_m', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->string('file_path')->nullable();
            $table->string('session_token', 64)->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shapefile_projects');
    }
};
