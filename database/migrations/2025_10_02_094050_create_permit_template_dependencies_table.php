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
        Schema::create('permit_template_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('permit_templates')->cascadeOnDelete();
            $table->foreignId('permit_item_id')->constrained('permit_template_items')->cascadeOnDelete();
            $table->foreignId('depends_on_item_id')->constrained('permit_template_items')->cascadeOnDelete();
            $table->enum('dependency_type', ['MANDATORY', 'OPTIONAL', 'RECOMMENDED'])->default('MANDATORY');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['template_id', 'permit_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permit_template_dependencies');
    }
};
