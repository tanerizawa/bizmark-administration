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
        Schema::create('kbli', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('KBLI code (e.g., 62010)');
            $table->text('description')->comment('Business activity description');
            $table->string('category')->nullable()->comment('Risk level: Rendah, Menengah Rendah, Menengah Tinggi, Tinggi');
            $table->string('sector', 1)->comment('Business sector (A-U)');
            $table->text('notes')->nullable()->comment('Additional notes or clarifications');
            $table->timestamps();
            
            // Indexes for faster search
            $table->index('code');
            $table->index('sector');
            $table->index('category');
            // Full-text search index for PostgreSQL
            $table->rawIndex('to_tsvector(\'indonesian\', description)', 'kbli_description_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kbli');
    }
};
