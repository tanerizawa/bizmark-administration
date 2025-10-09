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
        Schema::create('permit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->enum('category', [
                'environmental', 
                'land', 
                'building', 
                'transportation', 
                'business',
                'other'
            ]);
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('avg_processing_days')->nullable();
            $table->text('description')->nullable();
            $table->json('required_documents')->nullable();
            $table->decimal('estimated_cost_min', 15, 2)->nullable();
            $table->decimal('estimated_cost_max', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permit_types');
    }
};
