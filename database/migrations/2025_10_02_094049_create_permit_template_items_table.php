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
        Schema::create('permit_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('permit_templates')->cascadeOnDelete();
            $table->foreignId('permit_type_id')->nullable()->constrained('permit_types')->nullOnDelete();
            $table->string('custom_permit_name', 100)->nullable();
            $table->integer('sequence_order')->default(0);
            $table->boolean('is_goal_permit')->default(false);
            $table->integer('estimated_days')->nullable();
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['template_id', 'sequence_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permit_template_items');
    }
};
